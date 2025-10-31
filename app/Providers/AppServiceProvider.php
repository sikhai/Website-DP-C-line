<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;

use App\Models\Product;
use App\Observers\ProductObserver;
use TCG\Voyager\Facades\Voyager;
use App\FormFields\AttributesFormField;
use App\FormFields\VendorAttributesFormField;


class AppServiceProvider extends ServiceProvider
{
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
        Product::observe(ProductObserver::class);

        Voyager::addFormField(AttributesFormField::class);

        Voyager::addFormField(VendorAttributesFormField::class);
        
        Blade::if('home', function () {
            return Route::currentRouteName() === 'home';
        });
    }
}
