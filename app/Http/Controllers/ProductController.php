<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Rating;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Notifications\CreateProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;


class ProductController extends Controller
{
    public function store(Request $request){
      $request->validate([
     'name' => ['required', 'string'],
     'quantity' => ['required', 'integer'],
     'price' => ['required', 'numeric'],
     'description' => ['required', 'string'],
     'photo' => ['required', 'image'],
     'type' => ['required', 'string', Rule::in(['Fertilizers', 'Seeds', 'Machines'])],
    ]);

    $imageName = $request->file('photo')->getClientOriginalName();
    $path = $request->file('photo')->storeAs('products', $imageName, 'here');

   $company = Auth::user();

        // تأكد إنه المستخدم شركة فعلاً
        if (!($company instanceof Company)) {
            return response()->json(['message' => 'ليس لديك صلاحية الوصول هنا.'], 403);
        }

    $product=Product::create([
        'name'=>$request->name,
        'quantity'=>$request->quantity,
        'price'=>$request->price,
        'description'=>$request->description,
        'photo'=>$path,
        'company_id'=>$company->id,
        'type'=>$request->type,
    ]);
    $users=User::all();
    $company_name=Company::where('id',$product->company_id)->first()->name;
    Notification::send($users,new CreateProduct($product->id,$product->name,$company_name));

    return response()->json([
        'message'=>'success',
        'data'=>$product,
    ]);

    }

    public function update(Request $request,$id){
    
        $product=Product::findOrFail($id);
        $path=$product->photo;
    if ($request->hasFile('photo')) {
        $image = $request->file('photo')->getClientOriginalName();
        $path = $request->file('photo')->storeAs('products', $image, 'here'); 
    }
    
   $company = Auth::user();

        // تأكد إنه المستخدم شركة فعلاً
        if (!($company instanceof Company)) {
            return response()->json(['message' => 'ليس لديك صلاحية الوصول هنا.'], 403);
        }

    $product->update([
        'name' => $request->input('name', $product->name),
        'quantity' => $request->input('quantity', $product->quantity),
        'price' => $request->input('price', $product->price),
        'description' => $request->input('description', $product->description),
        'photo' => $path,
    ]);

    return response()->json([
        'message'=>'success',
        'data'=>$product,
    ]);

    }
    public function delete($id){
        $product=Product::findOrFail($id);
        $product->delete();
        return response()->json([
            'message'=>'the product is delete'
        ]);
    }

    public function index(Request $request)
{
    $query =Product::query();

    $type = $request->type;

    if (!is_null($type) && $type !== '') {
        $query->where('type', $type);
    }

    $products = $query->get();

    return response()->json([
        'message' => (!is_null($type) && $type !== '')
            ? "All products of type: $type"
            : 'All products',
        'data' => $products,
    ]);
}

public function getTypes()
{
    $types =Product::query()
        ->select('type')
        ->distinct()
        ->pluck('type');

    return response()->json([
        'message' => 'Available product types',
        'data' => $types,
    ]);
}



    public function show($id){
        $product=Product::findOrFail($id);
/** @var \App\Models\User $user */
      $user = auth()->user();

      $user->unreadNotifications()
      ->where('data->product_id', $id)
      ->get()
      ->each
      ->markAsRead();

        return response()->json([
            'message'=>' the product',
            'data'=>$product,
        ]);
    }

   public function purchase(Request $request, $id)
{
    $product = Product::findOrFail($id);

    $productPrice = $product->price;
    $quantity = $request->quantity;

    $priceAtPurchase = $quantity * $productPrice;

    $user = auth()->user();

    // نتأكد عنده محفظة
    $wallet = $user->wallet;

    if (!$wallet) {
        return response()->json([
            'message' => 'ليس لديك محفظة بعد. الرجاء إنشاء محفظة أولاً.'
        ], 400);
    }

    if ($wallet->balance < $priceAtPurchase) {
        return response()->json([
            'message' => 'رصيدك لا يكفي لإتمام عملية الشراء.'
        ], 400);
    }

    // إذا كان الرصيد كافي ننقص الرصيد وننشئ العملية
    DB::beginTransaction();

    try {
        // خصم المبلغ
        $wallet->balance -= $priceAtPurchase;
        $wallet->save();

        WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $priceAtPurchase,
                'fee' => null,
                'net_amount' => null,
                'transaction_type' => 'payment',
            ]);


         // إنقاص كمية المنتج
        $product->quantity -= $quantity;
        $product->save();

        // إنشاء عملية الشراء
        $purchase = Purchase::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price_at_purchase' => $priceAtPurchase,
            'purchase_date' => now(),
        ]);

        DB::commit();

        return response()->json([
            'message' => 'تمت عملية الشراء بنجاح.',
            'purchase' => $purchase,
            'wallet_balance' => $wallet->balance,
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'message' => 'حدث خطأ أثناء عملية الشراء.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function rating(Request $request, $id)
{
    $validated = $request->validate([
        'stars' => 'required|integer|min:1|max:5',
    ]);

    $userId = auth()->id();

    // تحقق هل قيّم من قبل
    $existingRating = Rating::where('user_id', $userId)
                            ->where('product_id', $id)
                            ->first();

    if ($existingRating) {
        return response()->json([
            'message'=>'لقد قمت بتقييم هذا المنتج مسبقاً.'
        ]);
    }

    // حفظ التقييم
    Rating::create([
        'user_id' => $userId,
        'product_id' => $id,
        'stars' => $validated['stars'],
    ]);

    // حساب المتوسط الجديد وعدد المقييمين
    $average = Rating::where('product_id', $id)->avg('stars');
    $count = Rating::where('product_id', $id)->count();

    // تحديث جدول المنتجات
    $product =Product::findOrFail($id);
    $product->average_rating = $average;
    $product->ratings_count = $count;
    $product->save();

    return response()->json([
        'message'=>'شكرا لك لتقييم المنتج'
    ]);
}



    
}
