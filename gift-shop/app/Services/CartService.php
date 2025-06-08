<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class CartService
{
    public function getCart()
    {
        // Get session cart
        $cart = collect(Session::get('cart', []));

        // If user is logged in, merge with database cart
        if (auth()->check()) {
            $dbCart = \App\Models\CartItem::where('user_id', auth()->id())->get();
            foreach ($dbCart as $item) {
                $existingItem = $cart->first(function ($cartItem) use ($item) {
                    return $cartItem['id'] == $item->product_id;
                });

                if ($existingItem) {
                    // Update existing item quantity
                    $cart = $cart->map(function ($cartItem) use ($item) {
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
                    });
                } else {
                    // Add new item
                    $cart->push([
                        'id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'customization_details' => $item->customization_details,
                        'customized_image' => $item->customized_image,
                        'special_instructions' => $item->special_instructions
                    ]);
                }
            }
            
            // Update session with merged cart
            Session::put('cart', $cart->toArray());
        }

        return $cart->map(function ($item) {
            $product = Product::find($item['id']);
            if ($product) {
                $item['product'] = $product;
            }
            return $item;
        });
    }

    private function validateCustomizationData($product, $customizationDetails)
    {
        if (!$customizationDetails) {
            return true;
        }

        // Get the product's customization templates
        $templates = $product->customizationTemplates;
        
        if ($templates->isEmpty() && !empty($customizationDetails)) {
            throw new \InvalidArgumentException('Product does not support customization');
        }

        foreach ($templates as $template) {
            $templateFields = collect($template->fields);
            
            // Check if all required fields are present
            foreach ($templateFields as $field) {
                $fieldName = $field['name'];
                
                if (!empty($field['required']) && $field['required'] === true) {
                    if (!isset($customizationDetails[$fieldName]) || empty($customizationDetails[$fieldName])) {
                        throw new \InvalidArgumentException("Required field '{$fieldName}' is missing");
                    }
                }
                
                // Validate field value if present
                if (isset($customizationDetails[$fieldName])) {
                    $value = $customizationDetails[$fieldName];
                    
                    switch ($field['type']) {
                        case 'text':
                        case 'textarea':
                            if (!is_string($value)) {
                                throw new \InvalidArgumentException("Field '{$fieldName}' must be a string");
                            }
                            break;
                            
                        case 'select':
                            if (!in_array($value, array_column($field['options'], 'value'))) {
                                throw new \InvalidArgumentException("Invalid option for field '{$fieldName}'");
                            }
                            break;
                            
                        case 'number':
                            if (!is_numeric($value)) {
                                throw new \InvalidArgumentException("Field '{$fieldName}' must be a number");
                            }
                            break;
                            
                        case 'file':
                            if (!is_string($value) || !filter_var($value, FILTER_VALIDATE_URL)) {
                                throw new \InvalidArgumentException("Field '{$fieldName}' must be a valid file URL");
                            }
                            break;
                    }
                }
            }
        }
        
        return true;
    }

    public function addToCart($productId, $quantity = 1, $customizationDetails = null, $specialInstructions = null)
    {
        $product = Product::findOrFail($productId);
        
        // Validate customization data before proceeding
        $this->validateCustomizationData($product, $customizationDetails);
        
        $cart = Session::get('cart', []);
        
        // Check if product already exists in cart
        $existingItemKey = array_search($productId, array_column($cart, 'id'));
        $newQuantity = $quantity;
        
        if ($existingItemKey !== false) {
            // Update quantity of existing item
            $newQuantity = $cart[$existingItemKey]['quantity'] + $quantity;
            $cart[$existingItemKey]['quantity'] = $newQuantity;
            
            // Update other details if provided
            if ($customizationDetails !== null) {
                $cart[$existingItemKey]['customization_details'] = $customizationDetails;
            }
            if ($specialInstructions !== null) {
                $cart[$existingItemKey]['special_instructions'] = $specialInstructions;
            }
        } else {
            // Add new item to cart - store only necessary data
            $cart[] = [
                'id' => $productId,
                'quantity' => $newQuantity,
                'customization_details' => $customizationDetails,
                'special_instructions' => $specialInstructions
            ];
        }
        
        Session::put('cart', $cart);

        // If user is logged in, also save to database
        if (auth()->check()) {
            $existingItem = \App\Models\CartItem::where('user_id', auth()->id())
                ->where('product_id', $productId)
                ->first();

            if ($existingItem) {
                $existingItem->update([
                    'quantity' => $newQuantity,
                    'customization_details' => $customizationDetails,
                    'special_instructions' => $specialInstructions
                ]);
            } else {
                \App\Models\CartItem::create([
                    'user_id' => auth()->id(),
                    'product_id' => $productId,
                    'quantity' => $newQuantity,
                    'customization_details' => $customizationDetails,
                    'special_instructions' => $specialInstructions
                ]);
            }
        }

        return true;
    }

    public function updateCart($productId, $quantity, $customizationDetails = null, $specialInstructions = null)
    {
        $product = Product::findOrFail($productId);
        
        // Validate customization data before proceeding
        $this->validateCustomizationData($product, $customizationDetails);
        
        $cart = Session::get('cart', []);
        foreach ($cart as $key => $item) {
            if ($item['id'] == $productId) {
                $cart[$key]['quantity'] = $quantity;
                if ($customizationDetails !== null) {
                    $cart[$key]['customization_details'] = $customizationDetails;
                }
                if ($specialInstructions !== null) {
                    $cart[$key]['special_instructions'] = $specialInstructions;
                }
                break;
            }
        }
        Session::put('cart', $cart);

        // If user is logged in, also update in database
        if (auth()->check()) {
            \App\Models\CartItem::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'product_id' => $productId
                ],
                [
                    'quantity' => $quantity,
                    'customization_details' => $customizationDetails,
                    'special_instructions' => $specialInstructions
                ]
            );
        }

        return true;
    }

    public function removeFromCart($productId)
    {
        $cart = Session::get('cart', []);
        $cart = array_filter($cart, function ($item) use ($productId) {
            return $item['id'] != $productId;
        });
        Session::put('cart', array_values($cart));

        // If user is logged in, also remove from database
        if (auth()->check()) {
            \App\Models\CartItem::where('user_id', auth()->id())
                ->where('product_id', $productId)
                ->delete();
        }

        return true;
    }

    public function clearCart()
    {
        Session::forget('cart');

        // If user is logged in, also clear from database
        if (auth()->check()) {
            \App\Models\CartItem::where('user_id', auth()->id())->delete();
        }
    }

    public function syncCart()
    {
        if (!auth()->check()) {
            return;
        }

        // Get session cart
        $sessionCart = collect(Session::get('cart', []));

        // Get database cart
        $dbCart = \App\Models\CartItem::where('user_id', auth()->id())->get();

        // Merge session cart into database
        foreach ($sessionCart as $item) {
            \App\Models\CartItem::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'product_id' => $item['id']
                ],
                [
                    'quantity' => $item['quantity'],
                    'customization_details' => $item['customization_details'] ?? null,
                    'special_instructions' => $item['special_instructions'] ?? null
                ]
            );
        }

        // Update session with database cart
        $updatedCart = $dbCart->map(function ($item) {
            return [
                'id' => $item->product_id,
                'quantity' => $item->quantity,
                'customization_details' => $item->customization_details,
                'special_instructions' => $item->special_instructions
            ];
        })->toArray();

        Session::put('cart', $updatedCart);
    }
} 