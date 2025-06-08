<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomizationTemplate;
use Illuminate\Http\Request;

class CustomizationTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templates = CustomizationTemplate::with('products')->latest()->paginate(10);
        return view('admin.templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.templates.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fields' => 'required|array|min:1',
            'fields.*.name' => 'required|string|max:255',
            'fields.*.type' => 'required|in:text,textarea,number,select,checkbox',
            'fields.*.required' => 'required|boolean',
            'fields.*.options' => 'required_if:fields.*.type,select|array',
            'fields.*.options.*' => 'required|string|max:255'
        ]);

        $template = CustomizationTemplate::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'fields' => $validated['fields']
        ]);

        return redirect()->route('admin.customization-templates.index')
            ->with('success', 'Template created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(CustomizationTemplate $customizationTemplate)
    {
        $customizationTemplate->load('products');
        return view('admin.templates.show', compact('customizationTemplate'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomizationTemplate $customizationTemplate)
    {
        return view('admin.templates.edit', compact('customizationTemplate'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomizationTemplate $customizationTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fields' => 'required|array|min:1',
            'fields.*.name' => 'required|string|max:255',
            'fields.*.type' => 'required|in:text,textarea,number,select,checkbox',
            'fields.*.required' => 'required|boolean',
            'fields.*.options' => 'required_if:fields.*.type,select|array',
            'fields.*.options.*' => 'required|string|max:255'
        ]);

        $customizationTemplate->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'fields' => $validated['fields']
        ]);

        return redirect()->route('admin.customization-templates.index')
            ->with('success', 'Template updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomizationTemplate $customizationTemplate)
    {
        if ($customizationTemplate->products()->exists()) {
            return redirect()->route('admin.customization-templates.index')
                ->with('error', 'Cannot delete template because it is being used by products.');
        }

        $customizationTemplate->delete();
        return redirect()->route('admin.customization-templates.index')
            ->with('success', 'Template deleted successfully');
    }
}
