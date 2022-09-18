<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Import application's system settings interface and service classes
use App\BusinessLogic\Interfaces\Admin\Settings\ErrorAndNotificationSystemInterface;
use App\BusinessLogic\Services\Admin\Settings\ErrorAndNotificationSystemService;
use App\BusinessLogic\Interfaces\Admin\Settings\LogInterface;
use App\BusinessLogic\Services\Admin\Settings\LogService;
use App\BusinessLogic\Interfaces\Admin\Settings\UserListInterface;
use App\BusinessLogic\Services\Admin\Settings\UserListService;
use App\BusinessLogic\Interfaces\Admin\Settings\UserRoleTypeInterface;
use App\BusinessLogic\Services\Admin\Settings\UserRoleTypeService;

// Import application's management system settings interface and service classes
use App\BusinessLogic\Interfaces\Admin\Management\ClientInterface;
use App\BusinessLogic\Services\Admin\Management\ClientService;

class BusinessLogicProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            ErrorAndNotificationSystemInterface::class,
            ErrorAndNotificationSystemService::class,
        );
        $this->app->bind(
            LogInterface::class,
            LogService::class,
        );
        $this->app->bind(
            UserListInterface::class,
            UserListService::class,
        );
        $this->app->bind(
            UserRoleTypeInterface::class,
            UserRoleTypeService::class,
        );
        $this->app->bind(
            ClientInterface::class,
            ClientService::class,
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
