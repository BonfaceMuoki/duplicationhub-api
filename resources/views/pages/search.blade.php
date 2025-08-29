@extends('layouts.master')

@section('title', $seoData['title'])
@section('meta_description', $seoData['description'])
@section('canonical_url', $seoData['canonical_url'])

@section('content')
<!-- Header -->
<header class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
            <div class="flex items-center">
                <a href="{{ route('pages.index') }}" class="text-gray-600 hover:text-blue-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Search Results</h1>
            </div>
        </div>
    </div>
</header>

<!-- Search Section -->
<section class="bg-gradient-to-r from-blue-600 to-purple-700 text-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">Search Pages</h2>
        <p class="text-xl mb-8 text-blue-100">Find the insights and strategies you need</p>
        
        <!-- Search Bar -->
        <div class="max-w-2xl mx-auto">
            <form action="{{ route('pages.search') }}" method="GET" class="flex">
                <input type="text" name="q" placeholder="Search pages..." 
                       value="{{ $query }}"
                       class="flex-1 px-6 py-4 text-gray-900 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" 
                        class="px-8 py-4 bg-blue-800 hover:bg-blue-900 rounded-r-lg transition duration-200">
                    Search
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Search Results -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-2">
                @if($pages->count() > 0)
                    Found {{ $pages->count() }} {{ Str::plural('result', $pages->count()) }} for "{{ $query }}"
                @else
                    No results found for "{{ $query }}"
                @endif
            </h3>
            <p class="text-gray-600">Search query: "{{ $query }}"</p>
        </div>

        @if($pages->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($pages as $page)
                    <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-200">
                        @if($page->image_url)
                            <div class="aspect-w-16 aspect-h-9">
                                <img src="{{ $page->image_url }}" alt="{{ $page->title }}" 
                                     class="w-full h-48 object-cover">
                            </div>
                        @endif
                        <div class="p-6">
                            <div class="flex items-center text-sm text-gray-500 mb-2">
                                <span>{{ $page->created_at->format('M d, Y') }}</span>
                                @if($page->user)
                                    <span class="mx-2">•</span>
                                    <span>{{ $page->user->name }}</span>
                                @endif
                            </div>
                            <h4 class="text-xl font-semibold text-gray-900 mb-3">
                                <a href="{{ route('pages.show', $page->slug) }}" 
                                   class="hover:text-blue-600 transition duration-200">
                                    {!! highlightSearchTerms($page->title, $query) !!}
                                </a>
                            </h4>
                            @if($page->summary)
                                <p class="text-gray-600 mb-4 line-clamp-3">
                                    {!! highlightSearchTerms($page->summary, $query) !!}
                                </p>
                            @endif
                            <div class="flex items-center justify-between">
                                <a href="{{ route('pages.show', $page->slug) }}" 
                                   class="text-blue-600 hover:text-blue-800 font-medium">
                                    Read More →
                                </a>
                                @if($page->views > 0)
                                    <span class="text-sm text-gray-500">
                                        {{ $page->views }} {{ Str::plural('view', $page->views) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="text-center py-16">
                <div class="text-gray-400 mb-6">
                    <svg class="w-24 h-24 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <h4 class="text-2xl font-bold text-gray-900 mb-4">No results found</h4>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">
                    We couldn't find any pages matching "{{ $query }}". Try different keywords or browse our pages.
                </p>
                <div class="space-y-4">
                    <a href="{{ route('pages.index') }}" 
                       class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                        Browse All Pages
                    </a>
                    <div class="text-sm text-gray-500">
                        <p class="mb-2">Search tips:</p>
                        <ul class="space-y-1">
                            <li>• Check your spelling</li>
                            <li>• Try more general keywords</li>
                            <li>• Use fewer keywords</li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Newsletter Section -->
<section class="py-16 bg-blue-600">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h3 class="text-3xl font-bold text-white mb-4">Stay Updated</h3>
        <p class="text-xl text-blue-100 mb-8">Get notified when we publish new pages and insights</p>
        <form class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
            <input type="email" placeholder="Enter your email" 
                   class="flex-1 px-6 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300">
            <button type="submit" 
                    class="px-8 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-gray-100 transition duration-200">
                Subscribe
            </button>
        </form>
    </div>
</section>
@endsection

@section('additional_css')
<style>
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .highlight {
        background-color: #fef3c7;
        padding: 2px 4px;
        border-radius: 4px;
        font-weight: 600;
    }
</style>
@endsection

@php
function highlightSearchTerms($text, $query) {
    if (empty($query)) {
        return $text;
    }
    
    $terms = explode(' ', $query);
    $highlighted = $text;
    
    foreach ($terms as $term) {
        $term = trim($term);
        if (strlen($term) > 2) {
            $highlighted = preg_replace(
                '/(' . preg_quote($term, '/') . ')/i',
                '<span class="highlight">$1</span>',
                $highlighted
            );
        }
    }
    
    return $highlighted;
}
@endphp 