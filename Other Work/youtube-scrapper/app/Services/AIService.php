<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected string $apiKey;
    protected string $url = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
    }

    // ?todo add caching to avoid repeated calls for same category
    public function generateTitles(string $category)
    {
        try {


            $prompt = "Generate 10 educational YouTube course titles about {$category}. Return as plain list.";

            $response = Http::withToken($this->apiKey)
                ->post($this->url, [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                ]);

            if ($response->failed()) {
                Log::error('AIService API Error: ' . $response->body());
                return [];
            }

            $text = $response->json()['choices'][0]['message']['content'] ?? '';

            return $this->formatResponse($text);
        } catch (\Exception $e) {
            Log::error('AIService Error: ' . $e->getMessage());
        }
    }


    // ?todo handle different response formats (numbered list, bullet points, plain text)
    private function formatResponse(string $text): array
    {
        $lines = explode("\n", $text);

        return collect($lines)
            ->map(fn($line) => trim(preg_replace('/^\d+\.\s*/', '', $line)))
            ->filter()
            ->values()
            ->toArray();
    }
}