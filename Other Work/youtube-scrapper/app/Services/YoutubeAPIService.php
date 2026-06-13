<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class YoutubeAPIService
{
    protected string $youtubeKey;

    public function __construct()
    {
        $this->youtubeKey = env('YOUTUBE_API_KEY');
    }

    public function searchPlaylists(array $queries, int $limit = 10): array
    {
        $playlists = [];

        foreach ($queries as $query) {
            $response = Http::get('https://www.googleapis.com/youtube/v3/search', [
                'part' => 'snippet',
                'q' => $query,
                'type' => 'playlist',
                'maxResults' => 2, // عدد النتائج لكل query
                'key' => $this->youtubeKey,
            ]);

            $items = $response->json()['items'] ?? [];

            foreach ($items as $item) {
                $playlists[] = [
                    'title' => $item['snippet']['title'],
                    'playlist_id' => $item['id']['playlistId'],
                    'url' => 'https://www.youtube.com/playlist?list=' . $item['id']['playlistId'],
                    'thumbnail' => $item['snippet']['thumbnails']['default']['url'] ?? null,
                ];
            }
        }

        return array_slice($playlists, 0, $limit); // 10 بس
    }
}