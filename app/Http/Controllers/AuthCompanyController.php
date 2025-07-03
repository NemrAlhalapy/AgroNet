<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthCompanyController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'name'=>['required', 'string', 'max:255'],
            'email'=>['required', 'email', 'unique:companies,email'],
            'specialization'=>['required', 'string'],
            'number_phone'=>['required', 'numeric'],
            'password'=>['required'],
        ]);

        $company=Company::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'specialization'=>$request->specialization,
            'number_phone'=>$request->number_phone,
            'password'=>bcrypt($request->password),
        ]);
        $data['token']=$company->createToken('ApiCompany')->plainTextToken;
        $data['name']=$company->name;
        $data['email']=$company->email;
        return response()->json($data);
    }

     public function login(Request $request)
{
    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::guard('company')->attempt(['email' => $request->email, 'password' => $request->password])) {
        /** @var \App\Models\Company $company */
        $company = Auth::guard('company')->user();

        $data['token'] = $company->createToken('ApiCompanyRegister')->plainTextToken;
        $data['name'] = $company->name;
        $data['email'] = $company->email;

        return response()->json($data);
    }

    // في حال فشل تسجيل الدخول
    return response()->json([
        'message' => 'بيانات الدخول غير صحيحة'
    ], 401);
}

public function logout(Request $request){
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message'=>'logout']);

}
}