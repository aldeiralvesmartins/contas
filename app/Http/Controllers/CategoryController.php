<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount(['transactions', 'accounts'])
            ->latest()
            ->paginate(10);

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:categories,name',
            'type' => 'required|in:income,expense',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:500',
        ]);

        // Usar cor personalizada se fornecida
        if ($request->filled('custom_color') && $request->color === '') {
            $validated['color'] = $request->custom_color;
        }

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    public function show(Category $category)
    {
        $category->loadCount(['transactions', 'accounts']);
        return view('categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:categories,name,' . $category->id,
            'type' => 'required|in:income,expense',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:500',
        ]);

        // Usar cor personalizada se fornecida
        if ($request->filled('custom_color') && $request->color === '') {
            $validated['color'] = $request->custom_color;
        }

        $category->update($validated);

        return redirect()->route('categories.show', $category)
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(Category $category)
    {
        // Verificar se a categoria está em uso
        if ($category->transactions()->exists() || $category->accounts()->exists()) {
            return redirect()->route('categories.index')
                ->with('error', 'Não é possível excluir uma categoria que está em uso!');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Categoria excluída com sucesso!');
    }
}
