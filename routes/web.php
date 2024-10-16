<?php

use App\Http\Controllers\Api\V1\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::middleware('auth:sanctum')->get('dashboardd', function() {
    return view('dashboard', ['user' => Auth::user()]);
});

Route::get('/password-reset/{token}', function(Request $request, $token) {
    $email = $request->query('email');
    return view("passwordReset", ["token" => $token, "email"=>$email]);
});
// Route::post('/mylogin', [LoginController::class, 'authenicate']);

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

require __DIR__.'/auth.php';
