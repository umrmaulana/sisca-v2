<?php

use App\Http\Controllers\SiscaV2\AuthController;
use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return view('auth.login');
// })->middleware('guest');
// Route::get('/login', function () {
//     return view('auth.login');
// });

Route::get('/', function () {
    if (auth('sisca-v2')->check()) {
        return redirect()->route('sisca-v2.dashboard');
    }
    return redirect()->route('sisca-v2.login');
})->name('home');

// Login Routes (No middleware for login page)
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest:sisca-v2');
Route::post('login', [AuthController::class, 'login'])->name('login.submit')->middleware('guest:sisca-v2');

// No Access Route
Route::get('no-access', function () {
    return view('sisca-v2.errors.no-access');
})->name('no-access');


// Include SISCA V2 Routes at the end to avoid conflicts
require __DIR__ . '/sisca-v2.php';
