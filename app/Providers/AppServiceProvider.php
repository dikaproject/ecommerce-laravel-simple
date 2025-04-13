<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Product;
use App\Models\TransactionItem;
use App\Models\Category;

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
        View::composer('components.product-card', function ($view) {
            $product = $view->getData()['product'];
            
            if (!isset($product->sold)) {
                $soldQuantity = TransactionItem::where('product_id', $product->id)
                    ->sum('quantity');
                
                $product->sold = $soldQuantity;
            }
        });

        // Share top categories with the navbar component
        View::composer('components.navbar', function ($view) {
            $navbarCategories = Category::take(4)->get();
            $view->with('navbarCategories', $navbarCategories);
        });
    }
}
