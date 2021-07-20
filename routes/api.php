<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/me', function(Request $request) {
        return auth()->user();
    });
  
    Route::get('/',[AuthController::class,'index']);
    Route::post('logout',[AuthController::class,'logout']);  
    Route::put('update/{id}',[AuthController::class,'update']); 
    Route::post('notification',[AuthController::class,'notification']);   
    Route::post('get_devices',[AuthController::class,'get_devices']); 
    Route::post('useful_info',[AuthController::class,'useful_info']); 
    
    
    
});

Route::post('register',[AuthController::class,'register']);
Route::post('login',[AuthController::class,'login']);
Route::post('verify-phone-otp',[AuthController::class,'verifyPhoneOtp']);
Route::post('resendpin',[AuthController::class,'resendpin']);
Route::post('forget_password',[AuthController::class,'forget_password']);
Route::post('reset_password',[AuthController::class,'resetpassword']);

