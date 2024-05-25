<?php

use App\Http\Controllers\Admin\Member\MemberController;
use App\Http\Controllers\CLIController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
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

Auth::routes(['verify' => false, 'register' => false]);

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [HomeController::class, 'root'])->name('home');

    Route::get('/members', [MemberController::class, 'index'])->name('members');

    Route::get('/members/data', [MemberController::class, 'data'])->name('member_data');

    Route::get('{any}', [HomeController::class, 'index'])->name('index');

    Route::get('/logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

    Route::get('/clear', [CLIController::class, 'index']);
});
