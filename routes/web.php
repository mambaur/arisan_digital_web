<?php

use App\Http\Controllers\Admin\Feedback\FeedbackController;
use App\Http\Controllers\Admin\Group\GroupController;
use App\Http\Controllers\Admin\Member\MemberController;
use App\Http\Controllers\Admin\Profile\ProfileController;
use App\Http\Controllers\Admin\Setting\AboutInfoController;
use App\Http\Controllers\Admin\Setting\ConfigurationController;
use App\Http\Controllers\Admin\Setting\StaticWebController;
use App\Http\Controllers\Admin\Subscription\SubscriptionController;
use App\Http\Controllers\Admin\User\UserController;
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

    Route::get('/test/notification', [HomeController::class, 'testNotification'])->name('test_notification');

    /**
     * Member
     * 
     */

    Route::get('/members', [MemberController::class, 'index'])->name('members');

    Route::get('/members/data', [MemberController::class, 'data'])->name('member_data');

    /**
     * Subscription
     * 
     */

    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions');

    Route::get('/subscriptions/data', [SubscriptionController::class, 'data'])->name('subscription_data');

    /**
     * Feedback
     * 
     */

    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback');

    Route::get('/feedback/data', [FeedbackController::class, 'data'])->name('feedback_data');

    Route::post('/feedback/comment', [FeedbackController::class, 'updateComment'])->name('feedback_update_comment');

    Route::delete('/feedback/delete/{id}', [FeedbackController::class, 'destroy'])->name('feedback_delete');

    /**
     * Setting
     * 
     */

    Route::get('/setting/configurations', [ConfigurationController::class, 'index'])->name('setting_configurations');

    Route::post('/setting/configurations', [ConfigurationController::class, 'store'])->name('setting_configuration_store');

    Route::get('/setting/setting-about-info', [AboutInfoController::class, 'index'])->name('setting_about_info');

    Route::post('/setting/setting-about-info', [AboutInfoController::class, 'store'])->name('setting_about_info_store');

    /**
     * User
     * 
     */

    Route::get('/users', [UserController::class, 'index'])->name('users');

    Route::get('/users/data', [UserController::class, 'data'])->name('user_data');

    Route::get('/users/search/data', [UserController::class, 'getUserSearchData'])->name('user_search_data');

    /**
     * Group
     * 
     */

    Route::get('/groups', [GroupController::class, 'index'])->name('groups');

    Route::get('/group/create', [GroupController::class, 'create'])->name('group_create');

    Route::post('/group/store', [GroupController::class, 'store'])->name('group_store');

    Route::get('/group/edit/{id}', [GroupController::class, 'edit'])->name('group_edit');

    Route::put('/group/update/{id}', [GroupController::class, 'update'])->name('group_update');

    Route::get('/groups/data', [GroupController::class, 'data'])->name('group_data');

    Route::delete('/groups/delete/{id}', [GroupController::class, 'destroy'])->name('group_delete');

    /**
     * Profile
     * 
     */

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

    Route::put('/profile/update/{id}', [ProfileController::class, 'update'])->name('profile_update');

    Route::put('/profile/update/password/{id}', [ProfileController::class, 'updatePassword'])->name('profile_update_password');

    Route::get('{any}', [HomeController::class, 'index'])->name('index');

    Route::get('/logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

    Route::get('/clear', [CLIController::class, 'index']);
});

Route::get('/reset-password/success', function () {
    return view('auth.passwords.reset-success');
});

Route::get('/info/kebijakan-privasi', [StaticWebController::class, 'privacy']);
Route::get('/info/syarat-dan-ketentuan', [StaticWebController::class, 'termCondition']);
Route::get('/info/apa-itu-arisan', [StaticWebController::class, 'whatIsArisan']);
Route::get('/info/tentang-arisan', [StaticWebController::class, 'aboutArisan']);
Route::get('/info/tips-arisan', [StaticWebController::class, 'tipsArisan']);
