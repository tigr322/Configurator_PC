<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;

class AiChatController extends Controller
{
    // Legacy endpoint kept for backward compatibility
    public function chat(Request $request)
    {
        return response()->json([
            'reply' => 'AI chat not configured for this endpoint. Use /ai instead.',
        ], 200);
    }

    // GET /ai — simple page (auth only)
    public function index()
    {
        return view('ai.index');
    }

    // POST /ai/generate — proxy to Yandex API Gateway (auth + throttle)
    public function generate(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'prompt' => ['required', 'string', 'min:1', 'max:2000'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $apiKey   = (string) config('services.yandex.api_key');
        $folderId = (string) config('services.yandex.folder_id');

        if (!$apiKey || !$folderId) {
            \Log::error('Yandex credentials missing', compact('apiKey','folderId'));
            return response()->json(['message' => 'Service misconfigured'], 500);
        }

        try {
            $res = \Http::asJson()
                ->acceptJson()
                ->timeout(20)
                ->retry(2, 500) // 2 повтора с шагом 500мс
                ->withHeaders([
                    'Authorization' => 'Api-Key '.$apiKey,
                ])
                ->post('https://llm.api.cloud.yandex.net/foundationModels/v1/completion', [
                    'modelUri' => "gpt://{$folderId}/yandexgpt/latest",
                    'completionOptions' => [
                        'stream'       => false,
                        'temperature'  => 0.3,
                        'maxTokens'    => 800,
                    ],
                    'messages' => [
                        ['role' => 'user', 'text' => (string) $request->string('prompt')],
                    ],
                ]);

            // Удобный режим в local: вернуть исходный ответ апстрима (для диагностики)
            if ($res->failed() && app()->environment('local')) {
                return response($res->body(), $res->status())
                    ->header('Content-Type', $res->header('Content-Type') ?? 'application/json');
            }

            if ($res->status() === 429) {
                \Log::warning('Yandex rate-limited');
                return response()->json(['message' => 'Rate limit. Try again shortly.'], 429);
            }

            if ($res->status() === 401 || $res->status() === 403) {
                \Log::error('Yandex auth error', ['status' => $res->status(), 'body' => $res->body()]);
                return response()->json(['message' => 'Auth error with upstream'], 502);
            }

            if ($res->failed()) {
                \Log::warning('Yandex non-2xx', ['status' => $res->status(), 'body' => $res->body()]);
                return response()->json(['message' => 'Upstream service unavailable'], 503);
            }

            $data = $res->json();
            $text = \Illuminate\Support\Arr::get($data, 'result.alternatives.0.message.text');

            return response()->json(['text' => (string) $text]);
        } catch (\Throwable $e) {
            \Log::error('Yandex exception', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Upstream exception', 'error' => $e->getMessage()], 500);
        }
    }

}




