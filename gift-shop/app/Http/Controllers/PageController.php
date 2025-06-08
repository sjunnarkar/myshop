<?php

namespace App\Http\Controllers;

use App\Models\Page;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = Page::active()->where('slug', $slug)->firstOrFail();
        
        return view('pages.show', [
            'page' => $page,
            'meta_title' => $page->meta_title ?? $page->title,
            'meta_description' => $page->meta_description
        ])->with('layout', $page->layout);
    }
} 