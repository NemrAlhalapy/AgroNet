<?php

namespace App\Http\Controllers;

use App\Models\Engineer;
use App\Models\Farmer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class AuthController extends Controller
{
    public function register(Request $request){

        $request->validate([
            'name'=>['required', 'string', 'max:255'],
            'email'=>['required', 'email', 'unique:users,email'],
            'password'=>['required'],
            'number_phone'=>['required'],
            'age'=>['required', 'integer'],
            'role'=>['required', 'in:farmer,engineer'],
            'university' => 'required_if:role,engineer|string|max:255',
            'specialty' => 'required_if:role,engineer|in:Irrigation and Drainage Engineering,Agricultural mechanization,Soil engineering',
            'years_of_experience' => 'required_if:role,engineer|integer|min:0',
        ]);

        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password' => bcrypt($request->password),
            'number_phone'=>$request->number_phone,
            'age'=>$request->age,
            'role'=>$request->role,
        ]);

        if ($request->role === 'engineer') {
            Engineer::create([
            'user_id' => $user->id,
            'university' => $request->university,
            'specialty' => $request->specialty,
            'years_of_experience' => $request->years_of_experience,
        ]);
        }

        if($request->role==='farmer'){
            Farmer::create([
                'user_id'=>$user->id,
            ]);
        }
    
        $data['token']=$user->createToken('ApiUser')->plainTextToken;
        $data['name']=$user->name;
        $data['email']=$user->email;
        return response()->json($data);

    }

     public function login(Request $request)
{
    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->status == 0) {
            
            $data['token'] = $user->createToken('ApiUserRegister')->plainTextToken;
            $data['name'] = $user->name;
            $data['email'] = $user->email;
            return response()->json($data);
        }

        $data['token'] = $user->createToken('ApiUserRegister')->plainTextToken;
        $data['name'] = $user->name;
        $data['email'] = $user->email;
        return response()->json($data);
    }

    // ➕ رسالة الخطأ عند فشل تسجيل الدخول
    return response()->json([
        'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'
    ], 401); // كود الحالة 401 يعني Unauthorized
}


    public function logout(Request $request){
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message'=>'logout']);

}
}
