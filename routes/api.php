<?php

use App\Http\Controllers\AuthCompanyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

//--------تسجيل دخول المستخدم -----------------//
Route::controller(AuthController::class)->group(function (){
    Route::post('register','register');
    Route::post('login','login');
    Route::post('logout','logout')->middleware('auth:sanctum');
});


//----------تسجيل دخول الشركات----------//
Route::controller(AuthCompanyController::class)->group(function (){
    Route::post('register/company','register');
    Route::post('login/company','login');
    Route::post('logout','logout')->middleware('auth:sanctum');
});


//-------------المنشورات---------------//
Route::middleware('auth:sanctum')->prefix('posts')->group(function () {
    Route::post('store', [PostController::class, 'store']);
    Route::get('index', [PostController::class, 'index']);
    Route::get('show/{id}', [PostController::class, 'show']);
    Route::put('update/{id}', [PostController::class, 'update'])->middleware('check_user');
    Route::delete('delete/{id}', [PostController::class, 'delete'])->middleware('check_user');
    Route::post('{id}/like', [PostController::class, 'like']);
    Route::post('{id}/dislike', [PostController::class, 'dislike']);
});
//-------------التعليقات على المنشورات-----------//
Route::middleware('auth:sanctum')->prefix('comments')->group(function () {
    Route::post('store/{id}', [PostController::class, 'Cstore']);
    Route::get('index/{id}', [PostController::class, 'Cindex']);
    Route::get('show/{id}', [PostController::class, 'Cshow']);
    Route::put('update/{id}', [PostController::class, 'Cupdate'])->middleware('check_user_comment');
    Route::delete('delete/{id}', [PostController::class, 'Cdelete'])->middleware('check_user_comment');
});


//-----------------المنتجات---------------//
Route::middleware('auth:sanctum')->prefix('products')->group(function () {
    Route::get('index', [ProductController::class, 'index']);
    Route::get('show/{id}', [ProductController::class, 'show']);
    Route::post('purchase/{id}', [ProductController::class, 'purchase']);
    Route::post('rating/{id}', [ProductController::class, 'rating']);
});
Route::middleware(['auth:sanctum', 'auth.company'])->prefix('products')->group(function () {
    Route::post('store', [ProductController::class, 'store']);
    Route::put('update/{id}', [ProductController::class, 'update'])->middleware('check_company');
    Route::delete('delete/{id}', [ProductController::class, 'delete'])->middleware('check_company');
});
Route::get('products/types', [ProductController::class, 'getTypes']);


//------------------المحفظة------------//
Route::middleware('auth:sanctum')->prefix('wallet')->group(function () {
    Route::post('store', [WalletController::class, 'store']);
    Route::get('show', [WalletController::class, 'show']);
});


//-----------------الاستشارات---------------//
Route::middleware('auth:sanctum')->prefix('consultation')->group(function () {
    Route::post('send', [ConsultationController::class, 'send']);
    Route::post('reply/{id}', [ConsultationController::class, 'reply']);
    Route::get('engineer-consultations', [ConsultationController::class, 'engineerConsultations']);
    Route::get('farmer-consultations', [ConsultationController::class, 'farmerConsultations']);
    Route::get('show/{id}', [ConsultationController::class, 'show']);
});
