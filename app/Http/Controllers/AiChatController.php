<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiChatController extends Controller
{//Будущая интеграция с DeepSeek :)
    public function chat(Request $request)
    {
        $message = $request->input('message');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('DEEPSEEK_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.deepseek.com/v1/chat/completions', [
            'model' => 'deepseek-chat',
            'messages' => [
                ['role' => 'system', 'content' => 'Ты помощник по сборке ПК. Отвечай коротко и по делу.'],
                ['role' => 'user', 'content' => $message],
            ],
            'temperature' => 0.7,
        ]);
        
        if ($response->failed()) {
            logger()->error('DeepSeek API ошибка', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        
            return response()->json([
                'reply' => 'Ответ не получен. DeepSeek API вернул ошибку.',
            ]);
        }
    }
}
