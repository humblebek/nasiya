<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CostController;
use App\Http\Controllers\MonthlyController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('admin')->middleware(['auth:sanctum','role:SuperAdmin|Admin'])->group(function (){
    Route::apiResource('device',DeviceController::class);
    Route::apiResource('client',ClientController::class);
    Route::apiResource('cost',CostController::class);
    Route::apiResource('orders',OrderController::class);
    Route::apiResource('monthly',MonthlyController::class);
    Route::post('orders/payment/{id}', [PaymentController::class, 'store']);
    Route::get('payment',[PaymentController::class,'index']);
    Route::get('payment/{payment}',[PaymentController::class,'show']);
    Route::put('payment/{payment}',[PaymentController::class,'update']);
    Route::put('/auth/update/{id}', [AuthController::class, 'updateAdmin']);
    Route::get('dashboard',[OrderController::class,'showRelevantOrders']);
});

Route::prefix('admin')->middleware(['auth:sanctum','role:SuperAdmin'])->group(function (){
    Route::get('/auth/alladmins', [AuthController::class, 'allAdmins']);
    Route::get('/auth/show/{id}', [AuthController::class, 'showAdmin']);
    Route::post('/auth/register', [AuthController::class, 'createAdmin']);
    Route::delete('/auth/delete/{id}', [AuthController::class, 'deleteAdmin']);
});

Route::get('balance', [Controller::class, 'balance']);
Route::post('/auth/login', [AuthController::class, 'loginAdmin']);



