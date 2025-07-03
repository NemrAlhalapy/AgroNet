<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $userId = auth()->user()->id;
        $amount = $request->amount;

        // نحسب الخصم 2%
        $fee = round($amount * 0.02, 2);
        $netAmount = $amount - $fee;

        DB::beginTransaction();

        try {
            // نحاول نجد محفظة المستخدم
            $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->first();

            // إذا ما عنده محفظة ننشئ له وحدة
            if (!$wallet) {
                $wallet = Wallet::create([
                    'user_id' => $userId,
                    'balance' => 0,
                ]);
            }

            // نحدث الرصيد
            $wallet->balance += $netAmount;
            $wallet->save();

            // نسجل العملية في wallet_transactions
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'fee' => $fee,
                'net_amount' => $netAmount,
                'transaction_type' => 'deposit',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'تم إيداع المبلغ بنجاح',
                'wallet_balance' => $wallet->balance,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'حدث خطأ أثناء الإيداع',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(){
        $userid=auth()->user()->id;
        $user=User::findOrFail($userid);
        if(!($user->wallet))
        return response()->json([
        'message'=>'you dont have a wallet create one']);
        return response()->json([
            'data'=>$user->wallet->balance
        ]);
    }

    
}
