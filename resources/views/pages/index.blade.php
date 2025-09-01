@extends('layouts.master')

@section('title', 'Business Insights & Strategies - Duplication Hub')
@section('meta_description', 'Discover our collection of business tips, growth strategies, and marketing insights. Learn from experts and transform your business today.')
@section('meta_keywords', 'business growth, strategy, insights, marketing, lead generation, referral marketing')

@section('og_type', 'website')
@section('canonical_url', url()->current())
@section('og_image', asset('assets/emails/welcome_.png'))

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-purple-700 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl md:text-5xl font-bold mb-6">Discover Insights & Strategies</h2>
        <p class="text-xl md:text-2xl mb-8 text-blue-100">Explore our collection of business tips, growth strategies, and marketing insights</p>
        
        <!-- Search Bar -->
        <div class="max-w-2xl mx-auto">
            <form action="{{ route('pages.search') }}" method="GET" class="flex">
                <input type="text" name="q" placeholder="Search pages..." 
                       class="flex-1 px-6 py-4 text-gray-900 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" 
                        class="px-8 py-4 bg-blue-800 hover:bg-blue-900 rounded-r-lg transition duration-200">
                    Search
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Latest Pages Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">       

        @if($pages->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($pages as $page)
                
                    <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-200">
                        @if($page->image_url)
                            <div class="h-48 overflow-hidden">
                                <img src="{{ $page->full_image_url }}" alt="{{ $page->title }}" 
                                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                            </div>
                        @else
                            <div class="h-48 bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center">
                                <svg class="w-16 h-16 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
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
                                    {{ $page->title }}
                                </a>
                            </h4>
                            @if($page->summary)
                                <p class="text-gray-600 mb-4 line-clamp-3">{{ $page->summary }}</p>
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
            <div class="text-center py-12">
                <div class="text-gray-400 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h4 class="text-lg font-medium text-gray-900 mb-2">No pages found</h4>
                <p class="text-gray-600">Check back later for new content.</p>
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