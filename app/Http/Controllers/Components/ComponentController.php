<?php

namespace App\Http\Controllers\Components;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Component;
class ComponentController extends Controller
{
 /**
     * Показать список всех комплектующих с фильтрацией и сортировкой
     */
    public function index(Request $request)
    {
        $query = Component::query()->with('category');

        // Фильтрация
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        if ($request->filled('brand')) {
            $query->where('brand', 'like', '%' . $request->brand . '%');
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Сортировка
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }

        // Пагинация
        $components = $query->paginate(10);

        // Все категории для фильтра
        $categories = Category::all();

        return view('pccomponents.catalog', compact('components', 'categories'));
    }

    /**
     * Показать один компонент
     */
    public function show($id)
    {
        $component = Component::with(['category', 'parsedData'])->findOrFail($id);

        return view('pccomponents.show', compact('component'));
    }
}
