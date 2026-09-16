<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    public function index()
    {
        $categories = ServiceCategory::withCount('serviceRequests')->orderBy('name')->get();

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:service_categories,name'],
            'description' => ['nullable', 'string'],
        ]);

        ServiceCategory::create($data);

        return redirect()->route('categories.index')->with('status', 'Category created.');
    }

    public function edit(ServiceCategory $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, ServiceCategory $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:service_categories,name,'.$category->id],
            'description' => ['nullable', 'string'],
        ]);

        $category->update($data);

        return redirect()->route('categories.index')->with('status', 'Category updated.');
    }

    public function destroy(ServiceCategory $category)
    {
        $category->delete();

        return redirect()->route('categories.index')->with('status', 'Category deleted.');
    }
}
