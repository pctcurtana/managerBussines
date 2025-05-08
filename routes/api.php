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
Route::get('/camera-orders', [CameraOrderController::class, 'index']);
Route::post('/camera-orders', [CameraOrderController::class, 'store']);
Route::delete('/camera-orders/{id}', [CameraOrderController::class, 'destroy']);
Route::get('/stats', [CameraOrderController::class, 'getStats']);
Route::get('/search', [CameraOrderController::class, 'search']);
Route::patch('/camera-orders/{id}/toggle-sold', [CameraOrderController::class, 'toggleSoldStatus']);
Route::put('/camera-orders/{id}', [CameraOrderController::class, 'update']);
