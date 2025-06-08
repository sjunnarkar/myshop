<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Services\CartService;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\DB;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('layouts.shop', function ($view) {
            $cartService = app(CartService::class);
            $cartItems = $cartService->getCart();
            $cartCount = $cartItems->sum('quantity');
            
            $view->with('cartCount', $cartCount);
        });

        // Share low stock count with admin views
        View::composer('layouts.admin', function ($view) {
            $lowStockCount = InventoryItem::where('stock_level', '<=', DB::raw('reorder_point'))
                ->count();
            
            $view->with('lowStockCount', $lowStockCount);
        });
    }
} 