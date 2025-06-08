<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Darryldecode\Cart\Cart;

class CartServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('cart', function($app)
        {
            $storage = $app['session'];
            $events = $app['events'];
            $instanceName = 'cart';

            $config = config('shopping_cart');
            if(is_null($config)) {
                $config = [
                    'format_numbers' => false,
                    'decimals' => 2
                ];
            }

            $cart = new Cart(
                $storage,
                $events,
                $instanceName,
                $config['format_numbers'],
                $config['decimals']
            );

            return $cart;
        });
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