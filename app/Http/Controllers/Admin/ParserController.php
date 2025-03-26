<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ParserService;
class ParserController extends Controller
{
    public function parse(Request $request, ParserService $parser)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'source_url' => 'required|url',
        ]);

        $parser->parseFromUrl($request->source_url, $request->category_id);

        return redirect()->back()->with('success', 'Парсинг успешно завершен!');
    }
}
