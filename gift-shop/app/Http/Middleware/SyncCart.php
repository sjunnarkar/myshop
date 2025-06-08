<?php

namespace App\Http\Middleware;

use App\Services\CartService;
use Closure;
use Illuminate\Http\Request;

class SyncCart
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $this->cartService->syncCart();
        }

        return $next($request);
    }
} 