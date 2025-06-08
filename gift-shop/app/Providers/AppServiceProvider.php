<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Blade;
use App\Helpers\CurrencyHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Share low stock count with admin views
        View::composer('layouts.admin', function ($view) {
            $lowStockCount = \App\Models\InventoryItem::where('stock_level', '<=', DB::raw('reorder_point'))
                ->count();
            
            $view->with('lowStockCount', $lowStockCount);
        });

        // Add currency formatter directive
        Blade::directive('currency', function ($amount) {
            return "<?php echo CurrencyHelper::format($amount); ?>";
        });

        // Make CurrencyHelper available in views
        Blade::include('App\Helpers\CurrencyHelper', 'CurrencyHelper');
    }
}
