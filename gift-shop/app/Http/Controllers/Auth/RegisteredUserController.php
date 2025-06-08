<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Session;
use App\Providers\RouteServiceProvider;

class RegisteredUserController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Get session cart before registration
        $sessionCart = Session::get('cart', []);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // If there are items in the session cart, save them to the database
        if (!empty($sessionCart)) {
            foreach ($sessionCart as $item) {
                \App\Models\CartItem::create([
                    'user_id' => $user->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'customization_details' => $item['customization_details'] ?? null,
                    'customized_image' => $item['customized_image'] ?? null,
                    'special_instructions' => $item['special_instructions'] ?? null
                ]);
            }
        }

        return redirect(RouteServiceProvider::HOME);
    }
} 