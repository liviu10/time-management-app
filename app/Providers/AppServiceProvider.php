<?php

namespace App\Providers;

// Import application's system settings interface classes
use App\BusinessLogic\Interfaces\Admin\Settings\ErrorAndNotificationSystemInterface;
use App\BusinessLogic\Interfaces\Admin\Settings\LogInterface;
use App\BusinessLogic\Interfaces\Admin\Settings\UserListInterface;
use App\BusinessLogic\Interfaces\Admin\Settings\UserRoleTypeInterface;

// Import application's management system settings interface classes
use App\BusinessLogic\Interfaces\Admin\Management\ClientInterface;
use App\BusinessLogic\Interfaces\Admin\Management\ProjectInterface;
use App\BusinessLogic\Interfaces\Admin\Management\ProjectTaskInterface;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningUnitTests()) {
            Schema::defaultStringLength(191);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local', 'testing') && class_exists(DuskServiceProvider::class)) {
            $this->app->register(DuskServiceProvider::class);
        }
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
        $this->app->bind(
            ProjectInterface::class,
            ProjectService::class,
        );
        $this->app->bind(
            ProjectTaskInterface::class,
            ProjectTaskService::class,
        );
    }
}
