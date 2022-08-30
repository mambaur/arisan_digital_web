<?php

use App\Http\Controllers\API\MemberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


/*
|--------------------------------------------------------------------------
| Members Routes
|--------------------------------------------------------------------------
|
| Manage members data API
|
*/

Route::get('/members', [MemberController::class, 'index']);

Route::get('/member/{id}', [MemberController::class, 'show']);

Route::post('/member/store', [MemberController::class, 'store']);

Route::patch('/member/update/{id}', [MemberController::class, 'update']);

Route::delete('/member/delete/{id}', [MemberController::class, 'destroy']);
