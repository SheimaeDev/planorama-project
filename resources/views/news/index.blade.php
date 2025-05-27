@extends('layouts.app')

@section('content')
<div class="container news-container">
<h2 class="text-center my-4 news-title-creative">Top Headlines Today</h2>
    @if(isset($mostPopular[0]))
        <div class="most-popular-news" aria-labelledby="most-popular-title" style="grid-column: 1 / -1;">
            @if(isset($mostPopular[0]['urlToImage']))
                <img src="{{ $mostPopular[0]['urlToImage'] }}"
                    alt="{{ $mostPopular[0]['title'] ?? 'Featured news image' }}"
                    class="most-popular-image">
            @else
                <div class="no-image">
                    <p>No image available for featured news.</p>
                </div>
            @endif
            <div class="most-popular-content">
                <h3 id="most-popular-title" class="news-title">{{ $mostPopular[0]['title'] }}</h3>
                @if(isset($mostPopular[0]['description']))
                    <p class="news-description">{{ $mostPopular[0]['description'] }}</p>
                @endif
                <a href="{{ $mostPopular[0]['url'] }}" class="news-button prominent-button" target="_blank" rel="noopener noreferrer">Read more</a>
            </div>
        </div>
    @endif

    <div class="small-news-grid">
        @foreach($remainingNews as $article)
            <div class="news-card" aria-labelledby="news-title-{{ $loop->index }}">
                @if(isset($article['urlToImage']))
                    <img src="{{ $article['urlToImage'] }}"
                        alt="{{ $article['title'] ?? 'News image' }}"
                        class="news-image">
                @else
                    <div class="no-image">
                        <p>No image.</p>
                    </div>
                @endif
                <div class="news-content">
                    <h5 id="news-title-{{ $loop->index }}" class="news-title">{{ $article['title'] }}</h5>
                    @if(isset($article['description']))
                        <p class="news-description">{{ $article['description'] }}</p>
                    @endif
                    <a href="{{ $article['url'] }}" class="news-button" target="_blank" rel="noopener noreferrer">Learn more</a>
                </div>
            </div>
        @endforeach
    </div>
</div>

<link rel="stylesheet" href="{{ asset('css/news.css') }}">
<script src="{{ asset('js/news.js') }}"></script>

@endsection
