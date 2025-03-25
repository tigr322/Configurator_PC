<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ParserController extends Controller
{
    public function parse(Request $request)
    {
        // Валидация
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'source_url' => 'nullable|url',
        ]);

        // Пример обработки:
        // Тут можно вставить свою логику парсинга или запустить Artisan-команду

        // Пример логики
        // ParserService::parseFromUrl($request->source_url, $request->category_id);

        return redirect()->back()->with('success', 'Парсинг запущен!');
    }
}
