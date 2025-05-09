<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CameraOrderController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route cho Camera Shop
// Áp dụng cache middleware cho các route GET - giữ cache trong 5 phút (300 giây)
Route::middleware('cache.headers:public;max_age=300;etag')->group(function () {
    Route::get('/camera-orders', [CameraOrderController::class, 'index']);
    Route::get('/camera-orders/{id}', [CameraOrderController::class, 'show']);
    Route::get('/stats', [CameraOrderController::class, 'getStats']);
    Route::get('/search', [CameraOrderController::class, 'search']);
});

// Route không cache
Route::post('/camera-orders', [CameraOrderController::class, 'store']);
Route::delete('/camera-orders/{id}', [CameraOrderController::class, 'destroy']);
Route::patch('/camera-orders/{id}/toggle-sold', [CameraOrderController::class, 'toggleSoldStatus']);
Route::put('/camera-orders/{id}', [CameraOrderController::class, 'update']);
