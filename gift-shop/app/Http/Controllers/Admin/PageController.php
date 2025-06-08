<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::orderBy('order')->paginate(15);
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'slug' => 'nullable|unique:pages,slug',
            'content' => 'required',
            'meta_title' => 'nullable|max:255',
            'meta_description' => 'nullable',
            'is_active' => 'boolean',
            'show_in_footer' => 'boolean',
            'show_in_header' => 'boolean',
            'layout' => 'required|in:default,full-width,sidebar',
            'order' => 'integer'
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        Page::create($validated);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page created successfully.');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'slug' => 'nullable|unique:pages,slug,' . $page->id,
            'content' => 'required',
            'meta_title' => 'nullable|max:255',
            'meta_description' => 'nullable',
            'is_active' => 'boolean',
            'show_in_footer' => 'boolean',
            'show_in_header' => 'boolean',
            'layout' => 'required|in:default,full-width,sidebar',
            'order' => 'integer'
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $page->update($validated);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page updated successfully.');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->route('admin.pages.index')
            ->with('success', 'Page deleted successfully.');
    }

    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'pages' => 'required|array',
            'pages.*.id' => 'required|exists:pages,id',
            'pages.*.order' => 'required|integer'
        ]);

        foreach ($validated['pages'] as $pageData) {
            Page::where('id', $pageData['id'])->update(['order' => $pageData['order']]);
        }

        return response()->json(['message' => 'Order updated successfully']);
    }
} 