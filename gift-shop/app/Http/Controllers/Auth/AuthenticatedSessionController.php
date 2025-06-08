<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthenticatedSessionController extends Controller
{
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Get session cart before login
        $sessionCart = Session::get('cart', []);

        // Get cart items from database
        $dbCart = \App\Models\CartItem::where('user_id', auth()->id())->get();
        
        // Merge database cart items with session cart
        foreach ($dbCart as $item) {
            $existingItem = collect($sessionCart)->first(function ($cartItem) use ($item) {
                return $cartItem['id'] == $item->product_id;
            });

            if ($existingItem) {
                // Update existing item in session cart
                $sessionCart = array_map(function ($cartItem) use ($item) {
                    if ($cartItem['id'] == $item->product_id) {
                        return [
                            'id' => $item->product_id,
                            'quantity' => $item->quantity,
                            'customization_details' => $item->customization_details,
                            'customized_image' => $item->customized_image,
                            'special_instructions' => $item->special_instructions
                        ];
                    }
                    return $cartItem;
                }, $sessionCart);
            } else {
                // Add new item to session cart
                $sessionCart[] = [
                    'id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'customization_details' => $item->customization_details,
                    'customized_image' => $item->customized_image,
                    'special_instructions' => $item->special_instructions
                ];
            }
        }

        // Update session with merged cart
        Session::put('cart', $sessionCart);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function destroy(Request $request)
    {
        // Get cart items from database before logout
        $cartItems = \App\Models\CartItem::where('user_id', auth()->id())->get();
        
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Restore cart items to session after logout
        if ($cartItems->isNotEmpty()) {
            $sessionCart = $cartItems->map(function ($item) {
                return [
                    'id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'customization_details' => $item->customization_details,
                    'customized_image' => $item->customized_image,
                    'special_instructions' => $item->special_instructions
                ];
            })->toArray();
            
            Session::put('cart', $sessionCart);
        }

        return redirect('/');
    }
} 