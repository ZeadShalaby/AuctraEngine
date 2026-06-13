<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\FetchRequest;
use App\Models\Playlist;
use App\Services\AIService;
use App\Services\YoutubeAPIService;

class FetchController extends Controller
{

    protected $ai;
    protected $youtube;

    public function __construct(AIService $ai, YoutubeAPIService $youtube)
    {
        $this->ai = $ai;
        $this->youtube = $youtube;

    }


    public function index()
    {
        return view('home');
    }


    public function fetch(FetchRequest $request)
    {
        try {
            $categories = collect(explode("\n", $request->categories))
                ->map(fn($cat) => trim($cat))
                ->filter();

            $results = [];

            foreach ($categories as $category) {
                $titles = $this->ai->generateTitles($category);
                $playlists = $this->youtube->searchPlaylists($titles);
                $playlists = array_map(fn($pl) => array_merge($pl, ['category' => $category]), $playlists);

                Playlist::insert($playlists); // ? save to db

                $results[] = [
                    'category' => $category,
                    'titles' => $titles,
                    'playlists' => $playlists,
                ];
            }

            return response()->json($results);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
