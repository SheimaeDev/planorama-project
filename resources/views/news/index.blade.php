@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center my-4">Latest News</h2>

    <div class="news-grid">
        @foreach($news as $article)
            <div class="news-card">
                <img src="{{ $article['urlToImage'] }}" alt="News Image" class="news-image">
                <div class="news-content">
                    <h5>{{ $article['title'] }}</h5>
                    <p>{{ $article['description'] }}</p>
                    <a href="{{ $article['url'] }}" class="news-button" target="_blank">Read More</a>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Enlace al archivo CSS -->
<link rel="stylesheet" href="{{ asset('css/news.css') }}">

<!-- Enlace al archivo JS -->
<script src="{{ asset('js/news.js') }}"></script>

@endsection
