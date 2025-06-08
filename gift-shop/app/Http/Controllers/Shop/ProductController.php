<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $query = Product::with('category')
            ->where('is_active', true)
            ->whereHas('category', function($q) {
                $q->where('is_active', true);
            });

        // Apply category filter
        if (request('category')) {
            $category = Category::where('slug', request('category'))
                ->where('is_active', true)
                ->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Apply price range filter
        if (request('price_min') && request('price_max')) {
            $query->whereBetween('base_price', [
                request('price_min'),
                request('price_max')
            ]);
        }

        // Apply sorting
        switch (request('sort')) {
            case 'price_asc':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('base_price', 'desc');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12);
        $categories = Category::active()->sorted()->get();
        
        // Get price range for filters
        $priceRange = (object) [
            'min_price' => Product::where('is_active', true)->min('base_price') ?? 0,
            'max_price' => Product::where('is_active', true)->max('base_price') ?? 100
        ];

        return view('shop.index', compact('products', 'categories', 'priceRange'));
    }

    /**
     * Display the specified product.
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->whereHas('category', function($q) {
                $q->where('is_active', true);
            })
            ->firstOrFail();

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        return view('shop.show', compact('product', 'relatedProducts'));
    }

    /**
     * Display products by category.
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function category($slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
        
        $products = Product::where('category_id', $category->id)
            ->where('is_active', true)
            ->paginate(12);

        $categories = Category::active()->sorted()->get();

        return view('shop.category', compact('products', 'category', 'categories'));
    }
}
