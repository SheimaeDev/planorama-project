@extends('layouts.app')

@section('content')
    <div id="loader" class="loader-wrapper">
        <div class="spinner"></div>
    </div>

    <div class="container news-container horizontal-layout" style="display: none;">
        <h2 class="text-center my-4 news-title-creative">Top Headlines Today</h2>

<section class="popular-news-section horizontal-section">
    <h3 class="section-title" aria-label="Breaking news"><span class="blinking-red">Breaking News</span></h3>
    <div class="horizontal-carousel-container">
        <div class="horizontal-carousel-slide popular-news-slider infinite-scroll">
            @foreach($mostPopular as $article)
    <a href="{{ $article['url'] }}" target="_blank" rel="noopener noreferrer" class="news-card popular-card horizontal-card" aria-labelledby="popular-title-{{ $loop->index }}" style="text-decoration: none; color: inherit;">                    @if(isset($article['urlToImage']))
                        <img src="{{ $article['urlToImage'] }}"
                             alt="{{ $article['title'] ?? 'Breaking news image' }}"
                             class="news-image horizontal-image larger-image">
                    @else
                        <div class="no-image horizontal-image larger-image">
                            <p>No image available.</p>
                        </div>
                    @endif
                    <div class="news-content">
                        <h5 id="popular-title-{{ $loop->index }}" class="news-title elegant-font black-title">
                            {{ $article['title'] }}
                        </h5>
                        @if(isset($article['publishedAt']))
                            <p class="news-date elegant-font">{{ \Carbon\Carbon::parse($article['publishedAt'])->format('d M Y') }}</p>
                        @endif
                    </div>
                    @if($loop->index < 5)
                        <div class="new-indicator" aria-hidden="true"></div>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="other-news-section mt-4 horizontal-section">
    <h3 class="section-title elegant-font">Other News</h3>
    <div class="horizontal-carousel-container">
        <div class="horizontal-carousel-slide other-news-slider infinite-scroll">
            @foreach(array_slice($remainingNews, 0, 45) as $article)
    <a href="{{ $article['url'] }}" target="_blank" rel="noopener noreferrer" class="news-card popular-card horizontal-card" aria-labelledby="popular-title-{{ $loop->index }}" style="text-decoration: none; color: inherit;">                    @if(isset($article['urlToImage']))
                        <img src="{{ $article['urlToImage'] }}" alt="{{ $article['title'] ?? 'News image' }}" class="news-image horizontal-image small-image">
                    @else
                        <div class="no-image horizontal-image small-image"><p>No image available.</p></div>
                    @endif
                    <div class="news-content">
                        <h5 id="other-title-{{ $loop->index }}" class="news-title elegant-font black-title">{{ $article['title'] }}</h5>
                        @if(isset($article['publishedAt']))
                            <p class="news-date elegant-font">{{ \Carbon\Carbon::parse($article['publishedAt'])->format('d M Y') }}</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
    </div>

    <link rel="stylesheet" href="{{ asset('css/news.css') }}">
    <script src="{{ asset('js/news.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const loader = document.getElementById('loader');
            const content = document.querySelector('.news-container');

            setTimeout(() => {
                loader.style.display = 'none';
                content.style.display = 'block';
            }, 800);
        });
    </script>
@endsection

