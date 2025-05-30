<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Class NewsController
 * @package App\Http\Controllers
 *
 * Controlador encargado de la obtención y visualización de noticias.
 * Utiliza la API de NewsAPI.org para traer titulares de noticias.
 */
class NewsController extends Controller
{
    /**
     * Muestra la página principal de noticias, obteniendo los titulares más recientes
     * de varias fuentes internacionales.
     *
     * Se divide el resultado en noticias más populares y el resto para su visualización.
     *
     * @return \Illuminate\View\View Retorna la vista 'news.index' con las noticias más populares
     * y el resto de noticias.
     */
    public function index()
    {
        $apiKey = env('MY_API_TOKEN'); // Variable de entorno API KY
        $response = Http::get("https://newsapi.org/v2/top-headlines", [
            'language' => 'en', // Idioma de las noticias
            'sortBy' => 'popularity', // Criterio de ordenación: por popularidad
            'pageSize' => 40, // Número de resultados por página
            'sources' => 'bbc-news,cnn,associated-press,reuters,the-washington-post,the-new-york-times', // Fuentes de noticias
            'apiKey' => $apiKey,
        ]);

        $news = $response->json()['articles'];

        $mostPopular = array_slice($news, 0, 10); // Las primeras 10 noticias como las más populares
        $remainingNews = array_slice($news, 10); // El resto de las noticias

        return view('news.index', compact('mostPopular', 'remainingNews'));
    }
}