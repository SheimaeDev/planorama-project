<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NewsController extends Controller
{
     public function index()
    {
        $apiKey = 'adeb30b6c7744878b39126b155eb10fc'; 
        $response = Http::get("https://newsapi.org/v2/top-headlines", [
            'language' => 'en',
            'sortBy' => 'popularity',
            'pageSize' => 20, 
            'sources' => 'bbc-news,cnn,associated-press', 
            'apiKey' => $apiKey,
        ]);

        $news = $response->json()['articles'];
        return view('news.index', compact('news'));
    }
}
