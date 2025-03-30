<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ParserService;
use Illuminate\Http\Request;

class ParserController extends Controller
{
    public function parse(Request $request, ParserService $parser)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'source_url' => 'required|url',
        ]);

        // Используем сервис для парсинга
        $message = $parser->parseFromUrl($request->source_url, $request->category_id);

        return redirect()->back()->with('success', $message);
    }
}
