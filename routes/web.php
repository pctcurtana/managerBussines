<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

// Auth routes không sử dụng middleware VerifyCsrfToken
Route::group(['middleware' => ['web', 'throttle:60,1']], function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post')->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
});

// CSRF token route
Route::get('/csrf-token', function (Request $request) {
    return response()->json(['token' => csrf_token()]);
})->name('csrf.token');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});
