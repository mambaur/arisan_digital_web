<?php

use App\Http\Controllers\API\V1\Histories\ArisanHistoryController;
use App\Http\Controllers\API\V1\Auth\AuthController;
use App\Http\Controllers\API\V1\Groups\GroupController;
use App\Http\Controllers\API\V1\Groups\GroupOwnerController;
use App\Http\Controllers\API\V1\Guest\GroupController as GuestGroupController;
use App\Http\Controllers\API\V1\Members\MemberController;
use App\Http\Controllers\API\V1\Notifications\NotificationController;
use App\Http\Controllers\API\V1\Payment\PaymentAccountController;
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

// Auth::routes();

Route::post('/login', [AuthController::class, 'login']);

Route::post('/login-with-google', [AuthController::class, 'loginWithGoogle']);

Route::post('/login-manual', [AuthController::class, 'loginManual']);

Route::post('/register', [AuthController::class, 'register']);

Route::post('/resend-verification', [AuthController::class, 'resendVerification']);

Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    /*
    |--------------------------------------------------------------------------
    | Auth Routes
    |--------------------------------------------------------------------------
    |
    | Manage auth data API
    |
    */

    Route::get('/user', [AuthController::class, 'show']);

    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | Members Routes
    |--------------------------------------------------------------------------
    |
    | Manage members data API
    |
    */

    Route::get('/members', [MemberController::class, 'index']);

    Route::post('/members/mail/reminder/{group_id}', [MemberController::class, 'mailReminder']);

    Route::get('/member/{id}', [MemberController::class, 'show']);

    Route::post('/member/store', [MemberController::class, 'store']);

    Route::patch('/member/update/{id}', [MemberController::class, 'update']);

    Route::patch('/member/update/status-active/{id}', [MemberController::class, 'updateStatusActive']);

    Route::patch('/member/update/status-paid/{id}', [MemberController::class, 'updateStatusPaid']);

    Route::patch('/member/reset/status-paid/{id}', [MemberController::class, 'resetStatusPaid']);

    Route::delete('/member/delete/{id}', [MemberController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Groups Routes
    |--------------------------------------------------------------------------
    |
    | Manage groups data API
    |
    */

    Route::get('/groups', [GroupController::class, 'index']);

    Route::get('/group/{id}', [GroupController::class, 'show']);

    Route::get('/group/members/{id}', [GroupController::class, 'memberGroup']);

    Route::post('/group/store', [GroupController::class, 'store']);

    Route::patch('/group/update/{id}', [GroupController::class, 'update']);

    Route::patch('/group/update/status/{id}', [GroupController::class, 'updateStatus']);

    Route::patch('/group/update/notes/{id}', [GroupController::class, 'updateNotes']);

    Route::patch('/group/update/periods-date/{id}', [GroupController::class, 'updatePeriodsDate']);

    Route::delete('/group/delete/{id}', [GroupController::class, 'destroy']);


    /*
    |--------------------------------------------------------------------------
    | Group Owner Routes
    |--------------------------------------------------------------------------
    |
    | Manage group owner data API
    |
    */

    Route::get('/group/owners/{id}', [GroupOwnerController::class, 'index']);

    Route::get('/group/owners/init', [GroupOwnerController::class, 'initGroupOwner']);

    Route::post('/group/owners/store', [GroupOwnerController::class, 'store']);

    Route::delete('/group/owners/delete/{id}', [GroupOwnerController::class, 'destroy']);

    Route::put('/group/owners/update-status/{id}', [GroupOwnerController::class, 'updateStatus']);

    /*
    |--------------------------------------------------------------------------
    | Arisan History Routes
    |--------------------------------------------------------------------------
    |
    | Manage arisan history data API
    |
    */

    Route::post('/arisan-history/store', [ArisanHistoryController::class, 'store']);

    Route::post('/arisan-history/delete/{id}', [ArisanHistoryController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Notifications Routes
    |--------------------------------------------------------------------------
    |
    | Manage notification data API
    |
    */

    Route::get('/notifications', [NotificationController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | Payment Account Routes
    |--------------------------------------------------------------------------
    |
    | Manage payment account data API
    |
    */

    Route::get('/payment-accounts/{id}', [PaymentAccountController::class, 'index']);

    Route::post('/payment-accounts/store', [PaymentAccountController::class, 'store']);

    Route::put('/payment-accounts/update/{id}', [PaymentAccountController::class, 'update']);

    Route::delete('/payment-accounts/delete/{id}', [PaymentAccountController::class, 'destroy']);
});

Route::get('/guest/group/{code}', [GuestGroupController::class, 'index']);

Route::get('/arisan-histories/{id}', [ArisanHistoryController::class, 'index']);

Route::get('/arisan-history/{id}', [ArisanHistoryController::class, 'show']);
