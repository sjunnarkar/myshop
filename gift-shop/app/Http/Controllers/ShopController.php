<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->active();

        // Apply category filter
        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Apply price filter
        if ($request->has('price_min')) {
            $query->where('base_price', '>=', $request->price_min);
        }
        if ($request->has('price_max')) {
            $query->where('base_price', '<=', $request->price_max);
        }

        // Apply sorting
        switch ($request->sort) {
            case 'price_asc':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('base_price', 'desc');
                break;
            case 'newest':
                $query->latest();
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::active()->sorted()->get();

        // Get min and max prices for the filter
        $priceRange = Product::selectRaw('MIN(base_price) as min_price, MAX(base_price) as max_price')
            ->active()
            ->first();

        return view('shop.index', compact('products', 'categories', 'priceRange'));
    }

    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        // Get related products from the same category
        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('shop.show', compact('product', 'relatedProducts'));
    }
} 