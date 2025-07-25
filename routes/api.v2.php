<?php

use App\Http\Controllers\Admin\Subscription\SubscriptionController;
use App\Http\Controllers\API\V2\Histories\ArisanHistoryController;
use App\Http\Controllers\API\V2\Auth\AuthController;
use App\Http\Controllers\API\V2\Groups\GroupController;
use App\Http\Controllers\API\V2\Groups\GroupOwnerController;
use App\Http\Controllers\API\V2\Guest\GroupController as GuestGroupController;
use App\Http\Controllers\API\V2\Members\MemberController;
use App\Http\Controllers\API\V2\Notifications\NotificationController;
use App\Http\Controllers\API\V2\Payment\PaymentAccountController;
use App\Http\Controllers\API\V2\Articles\ArticleController;
use App\Http\Controllers\API\V2\Feedback\FeedbackController;
use App\Http\Controllers\API\V2\Settings\SettingController;
use App\Http\Controllers\API\V2\Subscription\SubcriptionController;
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

    Route::post('/user/update-last-seen', [AuthController::class, 'updateLastSeen']);

    Route::get('/user/generate-code', [AuthController::class, 'initGenerateCode']);

    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | Members Routes
    |--------------------------------------------------------------------------
    |
    | Manage members data API
    |
    */

    Route::get('/members/{group_id}', [MemberController::class, 'index']);

    Route::get('/members/generate/member-from-created-by-group', [MemberController::class, 'generateMemberFromCreatedByGroup']);

    Route::post('/members/mail/reminder/{group_id}', [MemberController::class, 'mailReminder']);

    Route::post('/members/notification/reminder/payment/{group_id}', [MemberController::class, 'paymentNotificationReminder']);

    Route::post('/members/notification/reminder/invitation/{member_id}', [MemberController::class, 'invitationNotificationReminder']);

    Route::get('/member/count/{group_id}', [MemberController::class, 'countMember']);

    Route::get('/member/{id}', [MemberController::class, 'show']);

    Route::post('/member/store', [MemberController::class, 'store']);

    Route::post('/member/store/user-code', [MemberController::class, 'storeByUserCode']);

    Route::post('/member/store/user-email', [MemberController::class, 'storeByUserEmail']);

    Route::post('/member/store/group-code', [MemberController::class, 'storeByGroupCode']);

    Route::patch('/member/update/{id}', [MemberController::class, 'update']);

    Route::patch('/member/update/status-active/{id}', [MemberController::class, 'updateStatusActive']);

    Route::patch('/member/update/reinvit/{member_id}', [MemberController::class, 'reinvit']);

    Route::patch('/member/update/status-paid/{id}', [MemberController::class, 'updateStatusPaid']);

    Route::patch('/member/reset/status-paid/{id}', [MemberController::class, 'resetStatusPaid']);

    Route::delete('/member/delete/{id}', [MemberController::class, 'destroy']);


    /*
    |--------------------------------------------------------------------------
    | Settings Routes
    |--------------------------------------------------------------------------
    |
    | Manage settings data API
    |
    */

    Route::get('/setting/all', [SettingController::class, 'all']);

    Route::get('/setting/{key}', [SettingController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | Groups Routes
    |--------------------------------------------------------------------------
    |
    | Manage groups data API
    |
    */

    Route::get('/groups', [GroupController::class, 'index']);

    Route::get('/group/generate-owner', [GroupController::class, 'generateOwner']);

    Route::get('/group/generate-user-id-member', [GroupController::class, 'generateUserIdMembers']);

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
    | Groups Routes
    |--------------------------------------------------------------------------
    |
    | Manage groups data API
    |
    */

    Route::get('/articles', [ArticleController::class, 'index']);


    /*
    |--------------------------------------------------------------------------
    | Group Owner Routes
    |--------------------------------------------------------------------------
    |
    | Manage group owner data API
    |
    */

    Route::get('/group/owners/{id}', [GroupOwnerController::class, 'index']);

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

    Route::get('/arisan-history/init-winners', [ArisanHistoryController::class, 'initWinner']);

    Route::post('/arisan-history/store', [ArisanHistoryController::class, 'store']);

    Route::post('/arisan-history/store-winner', [ArisanHistoryController::class, 'storeWinner']);

    Route::post('/arisan-history/delete/{id}', [ArisanHistoryController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Subscription Routes
    |--------------------------------------------------------------------------
    |
    | Manage subscriptions data API
    |
    */

    Route::post('/subscription/store', [SubcriptionController::class, 'store']);

    /*
    |--------------------------------------------------------------------------
    | Feedback Routes
    |--------------------------------------------------------------------------
    |
    | Manage feedback data API
    |
    */

    Route::get('/feedback', [FeedbackController::class, 'index']);

    Route::post('/feedback', [FeedbackController::class, 'store']);

    /*
    |--------------------------------------------------------------------------
    | Notifications Routes
    |--------------------------------------------------------------------------
    |
    | Manage notification data API
    |
    */

    Route::get('/notifications', [NotificationController::class, 'index']);

    Route::get('/notification/count', [NotificationController::class, 'count']);

    Route::post('/notification/read', [NotificationController::class, 'markAllAsRead']);

    Route::post('/notification/test', [NotificationController::class, 'test']);

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
