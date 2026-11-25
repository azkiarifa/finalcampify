<?php

namespace App\Http\Controllers\SellerController;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController_seller extends Controller
{
    /**
     * Tampilkan semua kategori outdoor
     */
    public function index(Request $request)
    {
        $query = Category::withCount(['services', 'products']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('nama_kategori', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhere('slug', 'like', "%{$s}%");
            });
        }

        $categories = $query->latest()->get();
        return view('SellerView.categories.index_seller', compact('categories'));
    }

    /**
     * Form tambah kategori baru
     */
    public function create()
    {
        return view('SellerView.categories.create_seller');
    }

    /**
     * Simpan kategori baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string|max:500',
        ]);

        Category::create([
            'name'          => $request->name,
            'nama_kategori' => $request->name,
            'slug'          => Str::slug($request->name),
            'description'   => $request->description,
            'icon'          => Category::guessIcon($request->name),
        ]);

        return redirect()->route('seller.categories.index')->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    /**
     * Form edit kategori
     */
    public function edit(Category $category)
    {
        return view('SellerView.categories.edit_seller', compact('category'));
    }

    /**
     * Update kategori
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
        ]);

        $category->update([
            'name'          => $request->name,
            'nama_kategori' => $request->name,
            'slug'          => Str::slug($request->name),
            'description'   => $request->description,
            'icon'          => $category->icon ?: Category::guessIcon($request->name),
        ]);

        return redirect()->route('seller.categories.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Hapus kategori
     */
    public function destroy(Category $category)
    {
        if ($category->services()->exists() || $category->products()->exists()) {
            return redirect()->route('seller.categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh layanan atau produk aktif.');
        }

        $category->delete();
        return redirect()->route('seller.categories.index')->with('success', 'Kategori berhasil dihapus!');
    }
}
