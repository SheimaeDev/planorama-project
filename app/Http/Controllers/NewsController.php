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
            'pageSize' => 40, 
            'sources' => 'bbc-news,cnn,associated-press,reuters,the-washington-post,the-new-york-times', 
            'apiKey' => $apiKey,
        ]);

        $news = $response->json()['articles'];

        $mostPopular = array_slice($news, 0, 10); 
        $remainingNews = array_slice($news, 10); 

        return view('news.index', compact('mostPopular', 'remainingNews'));
    }
}