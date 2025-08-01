<?php

namespace App\Providers;

use App\Services\Admin\AdminAuthService;
use App\Services\Chef\ChefAuthService;
use App\Services\Chef\ChefProfileService;
use App\Services\Chef\ChefService;
use App\Services\ChefStoreService;
use App\Services\FoodOptionGroupService;
use App\Services\FoodOptionService;
use App\Services\FoodService;
use App\Services\Interfaces\Admin\AdminAuthServiceInterface;
use App\Services\Interfaces\Chef\ChefAuthServiceInterface;
use App\Services\Interfaces\Chef\ChefProfileServiceInterface;
use App\Services\Interfaces\Chef\ChefServiceInterface;
use App\Services\Interfaces\ChefStoreServiceInterface;
use App\Services\Interfaces\FoodOptionGroupServiceInterface;
use App\Services\Interfaces\FoodOptionServiceInterface;
use App\Services\Interfaces\FoodServiceInterface;
use App\Services\Interfaces\NotificationsServiceInterface;
use App\Services\Interfaces\OrderServiceInterface;
use App\Services\Interfaces\TagServiceInterface;
use App\Services\Interfaces\TicketServiceInterface;
use App\Services\Interfaces\User\UserAddressServiceInterface;
use App\Services\Interfaces\User\UserAuthServiceInterface;
use App\Services\Interfaces\User\UserProfileServiceInterface;
use App\Services\Interfaces\User\UserServiceInterface;
use App\Services\NotificationService;
use App\Services\OrderService;
use App\Services\TagService;
use App\Services\TicketService;
use App\Services\User\UserAddressService;
use App\Services\User\UserAuthService;
use App\Services\User\UserProfileService;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{


    public $singletons = [
        //CHEF
        ChefAuthServiceInterface::class => ChefAuthService::class,
        ChefProfileServiceInterface::class => ChefProfileService::class,
        ChefStoreServiceInterface::class => ChefStoreService::class,
        FoodServiceInterface::class => FoodService::class,
        FoodOptionServiceInterface::class => FoodOptionService::class,
        FoodOptionGroupServiceInterface::class => FoodOptionGroupService::class,
        ChefServiceInterface::class => ChefService::class,

        //USER
        UserAUthServiceInterface::class => UserAuthService::class,
        UserAddressServiceInterface::class => UserAddressService::class,
        UserProfileServiceInterface::class => UserProfileService::class,
        UserServiceInterface::class => UserService::class,

        //Admin
        AdminAuthServiceInterface::class => AdminAuthService::class,


        //Common
        OrderServiceInterface::class => OrderService::class,
        NotificationsServiceInterface::class => NotificationService::class,
        TicketServiceInterface::class => TICketService::class,
        TagServiceInterface::class => TagService::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Broadcast::resolveAuthenticatedUserUsing(function (Request $request) {
            return $request->user();
        });
    }
}
