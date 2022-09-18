<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| FRONT VIEW WEB APPLICATION CONTROLLERS
|--------------------------------------------------------------------------
|
| Here is where you can import all the controller files that are only
| available for the front view web application. These controllers will handle
| only the HTTP requests that comes from the user.
|
*/
    // Import User Login and Registration System Controller files
    use App\Http\Controllers\Auth\ForgotPasswordController;
    use App\Http\Controllers\Auth\LoginController;
    use App\Http\Controllers\Auth\OAuthController;
    use App\Http\Controllers\Auth\RegisterController;
    use App\Http\Controllers\Auth\ResetPasswordController;
    use App\Http\Controllers\Auth\UserController;
    use App\Http\Controllers\Auth\VerificationController;
    use App\Http\Controllers\Settings\PasswordController;
    use App\Http\Controllers\Settings\ProfileController;


/*
|--------------------------------------------------------------------------
| ADMIN VIEW WEB APPLICATION CONTROLLERS
|--------------------------------------------------------------------------
|
| Here is where you can import all the controller files that are only
| available for the front view web application. These controllers will handle
| only the HTTP requests that comes from the user.
|
*/
    // Import application's system settings
    use App\Http\Controllers\Admin\Settings\ErrorAndNotificationSystemController;
    use App\Http\Controllers\Admin\Settings\LogSystemController;
    use App\Http\Controllers\Admin\Settings\UserListController;
    use App\Http\Controllers\Admin\Settings\UserRoleTypeController;

    // Import the Management system
    use App\Http\Controllers\Admin\Management\ClientController;
    use App\Http\Controllers\Admin\Management\ProjectController;

/*
|--------------------------------------------------------------------------
| API Routes for the Admin View of the Web Application
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for the admin view of the application.
| These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['middleware' => 'auth:api', 'prefix' => '/admin'], function () {
    // Application admin API routes settings
    Route::group([ 'prefix' => '/settings' ], function () {
        Route::apiResource('/errors-and-notification', ErrorAndNotificationSystemController::class)
            ->only(['index', 'store', 'show', 'update']);
        Route::apiResource('/logs', LogSystemController::class)
            ->only(['index', 'show']);
        Route::group([ 'prefix' => '/users' ], function () {
            Route::apiResource('/role-types', UserRoleTypeController::class)
                ->only(['index', 'store', 'show', 'update']);
            Route::apiResource('/list', UserListController::class)
                ->only(['index', 'store', 'show', 'update']);
        });
    });

    // Application management API routes settings
    Route::group([ 'prefix' => '/management' ], function () {
        Route::apiResource('/clients', ClientController::class);
        Route::apiResource('/projects', ProjectController::class);
    });
});

/*
|--------------------------------------------------------------------------
| API Routes for the Login & Registration System of the Web Application
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for the login & registration system
| of the application. These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', [LoginController::class, 'logout']);
    Route::get('user', [UserController::class, 'current']);
    Route::patch('settings/profile', [ProfileController::class, 'update']);
    Route::patch('settings/password', [PasswordController::class, 'update']);
});

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('password/reset', [ResetPasswordController::class, 'reset']);
    Route::post('email/verify/{user}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend', [VerificationController::class, 'resend']);
    Route::post('oauth/{driver}', [OAuthController::class, 'redirect']);
    Route::get('oauth/{driver}/callback', [OAuthController::class, 'handleCallback'])->name('oauth.callback');
});
