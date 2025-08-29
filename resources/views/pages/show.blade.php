<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->meta_title ?: $page->title }}</title>
    <meta name="description" content="{{ $page->meta_description ?: $page->summary }}">
    
    <!-- Schema Markup -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@graph": [
            {
                "@type": "Organization",
                "@id": "{{ url('/') }}#organization",
                "name": "{{ config('app.name', 'Duplication Hub') }}",
                "url": "{{ url('/') }}",
                "logo": {
                    "@type": "ImageObject",
                    "url": "{{ asset('favicon.ico') }}",
                    "width": 32,
                    "height": 32
                },
                "description": "Business growth and referral marketing platform",
                "sameAs": [
                    "https://www.facebook.com/duplicationhub",
                    "https://twitter.com/duplicationhub",
                    "https://www.linkedin.com/company/duplicationhub"
                ],
                "contactPoint": {
                    "@type": "ContactPoint",
                    "telephone": "+254740857767",
                    "contactType": "customer service",
                    "availableLanguage": "English"
                }
            },
            {
                "@type": "Article",
                "@id": "{{ url()->current() }}#article",
                "headline": "{{ $page->title }}",
                "description": "{{ $page->meta_description ?: $page->summary }}",
                "image": {
                    "@type": "ImageObject",
                    "url": "{{ $page->full_image_url ?: asset('assets/emails/email.png') }}",
                    "width": 1200,
                    "height": 630
                },
                "author": {
                    "@type": "Person",
                    "@id": "{{ url('/') }}#author",
                    "name": "{{ $page->user ? $page->user->full_name : 'Admin User' }}",
                    "url": "{{ url('/') }}"
                },
                "publisher": {
                    "@id": "{{ url('/') }}#organization"
                },
                "mainEntityOfPage": {
                    "@type": "WebPage",
                    "@id": "{{ url()->current() }}"
                },
                "datePublished": "{{ $page->created_at->toISOString() }}",
                "dateModified": "{{ $page->updated_at->toISOString() }}",
                "articleSection": "Business Growth",
                "keywords": "business growth, referral marketing, lead generation, {{ $page->title }}",
                "wordCount": "{{ str_word_count($page->summary ?? '') + str_word_count($page->body ?? '') }}",
                "inLanguage": "en-US",
                "isAccessibleForFree": true,
                "license": "https://creativecommons.org/licenses/by/4.0/"
            },
            {
                "@type": "BreadcrumbList",
                "@id": "{{ url()->current() }}#breadcrumb",
                "itemListElement": [
                    {
                        "@type": "ListItem",
                        "position": 1,
                        "name": "Home",
                        "item": "{{ url('/') }}"
                    },
                    {
                        "@type": "ListItem",
                        "position": 2,
                        "name": "Pages",
                        "item": "{{ route('pages.index') }}"
                    },
                    {
                        "@type": "ListItem",
                        "position": 3,
                        "name": "{{ $page->title }}",
                        "item": "{{ url()->current() }}"
                    }
                ]
            },
            {
                "@type": "WebPage",
                "@id": "{{ url()->current() }}#webpage",
                "name": "{{ $page->title }}",
                "description": "{{ $page->meta_description ?: $page->summary }}",
                "url": "{{ url()->current() }}",
                "isPartOf": {
                    "@id": "{{ url('/') }}#website"
                },
                "about": {
                    "@id": "{{ url()->current() }}#article"
                },
                "breadcrumb": {
                    "@id": "{{ url()->current() }}#breadcrumb"
                },
                "inLanguage": "en-US",
                "isAccessibleForFree": true,
                "datePublished": "{{ $page->created_at->toISOString() }}",
                "dateModified": "{{ $page->updated_at->toISOString() }}"
            },
            {
                "@type": "WebSite",
                "@id": "{{ url('/') }}#website",
                "name": "{{ config('app.name', 'Duplication Hub') }}",
                "url": "{{ url('/') }}",
                "description": "Business growth and referral marketing platform",
                "publisher": {
                    "@id": "{{ url('/') }}#organization"
                },
                "potentialAction": {
                    "@type": "SearchAction",
                    "target": {
                        "@type": "EntryPoint",
                        "urlTemplate": "{{ url('/') }}/search?q={search_term_string}"
                    },
                    "query-input": "required name=search_term_string"
                }
            }
        ]
    }
    </script>

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="{{ $page->meta_title ?: $page->title }}">
    <meta property="og:description" content="{{ $page->meta_description ?: $page->summary }}">
    <meta property="og:image" content="{{ $page->og_image_url ?: $page->full_image_url ?: asset('assets/emails/email.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="{{ config('app.name', 'Duplication Hub') }}">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $page->meta_title ?: $page->title }}">
    <meta name="twitter:description" content="{{ $page->meta_description ?: $page->summary }}">
    <meta name="twitter:image" content="{{ $page->og_image_url ?: $page->full_image_url ?: asset('assets/emails/email.png') }}">
    @php
                $agent = new Jenssegers\Agent\Agent();
    @endphp
    <!-- Additional Meta Tags -->
    @if($page->meta_title)
        <meta name="title" content="{{ $page->meta_title }}">
    @endif
    @if($page->meta_description)
        <meta name="description" content="{{ $page->meta_description }}">
    @endif
    @if($page->canonical_url)
        <link rel="canonical" href="{{ $page->canonical_url }}">
    @endif
    @if($page->is_indexable === false)
        <meta name="robots" content="noindex, nofollow">
    @endif
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom styles */
        .gradient-bg {
            background: linear-gradient(135deg, #87CEEB 0%, #000080 100%);
        }
        .text-shadow {
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body class="bg-gray-50">
@extends('layouts.master')

@section('title', $seoData['title'])
@section('meta_description', $seoData['description'])
@section('meta_keywords', $seoData['keywords'])

@section('og_type', $seoData['og_type'])
@section('og_image', $seoData['og_image'])
@section('canonical_url', $seoData['canonical_url'])

@section('additional_meta')
    <!-- Article specific meta tags -->
    <meta property="article:published_time" content="{{ $seoData['published_time'] }}">
    <meta property="article:modified_time" content="{{ $seoData['modified_time'] }}">
    <meta property="article:author" content="{{ $seoData['author'] }}">
    <meta property="article:section" content="{{ $seoData['article_section'] ?? 'Business' }}">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoData['title'] }}">
    <meta name="twitter:description" content="{{ $seoData['description'] }}">
    <meta name="twitter:image" content="{{ $seoData['og_image'] }}">
@endsection

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="/" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-300 mx-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ route('pages.index') }}" class="text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors duration-200">
                        Pages
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-300 mx-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm font-semibold text-gray-900">{{ $page->title }}</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
<!-- Header -->
<!-- <header class="bg-gradient-to-br from-gray-50 to-white shadow-lg border-b border-gray-200">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold text-[#000080] mb-6 leading-tight">{{ $page->title }}</h1>
            @if($page->headline)
                <p class="text-xl md:text-2xl text-[#808080] mb-8 max-w-3xl mx-auto leading-relaxed">{{ $page->headline }}</p>
            @endif
            <div class="flex items-center justify-center space-x-6 text-sm text-gray-600 bg-white/70 backdrop-blur-sm rounded-full px-6 py-3 shadow-sm">
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    {{ $page->created_at->format('M d, Y') }}
                </span>
                @if($page->user)
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        {{ $page->user->full_name }}
                    </span>
                @endif
                @if($page->views > 0)
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        {{ $page->views }} {{ Str::plural('view', $page->views) }}
                    </span>
                @endif
            </div>
        </div>
    </div>
</header> -->

<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 lg:gap-12">
        <!-- Left Sidebar - Media Content -->
        <div class="lg:col-span-1">
            <div class="sticky top-8 space-y-6">
                @if($page->image_url || $page->video_url)
                    <!-- Media Section -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                        <h3 class="text-lg font-semibold text-[#000080] mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-[#87CEEB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Media
                        </h3>
                        
                        @if($page->image_url)
                            <!-- Featured Image -->
                            <div class="mb-6">
                                <div class="relative group overflow-hidden rounded-xl shadow-md">
                                    <img src="{{ $page->full_image_url }}" alt="{{ $page->title }}" 
                                         class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    <!-- Fallback for broken images -->
                                    <div class="hidden absolute inset-0 bg-gradient-to-br from-blue-100 to-indigo-200 rounded-xl flex items-center justify-center">
                                        <div class="text-center text-gray-600">
                                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <p class="text-sm font-medium">Image unavailable</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        @if($page->video_url)
                            <!-- Video Section -->
                            <div>
                                <div class="relative aspect-video rounded-xl shadow-md overflow-hidden">
                                    @if(filter_var($page->video_url, FILTER_VALIDATE_URL) && (str_contains($page->video_url, 'youtube.com') || str_contains($page->video_url, 'youtu.be')))
                                        <iframe 
                                            src="{{ str_replace('watch?v=', 'embed/', str_replace('youtu.be/', 'youtube.com/embed/', $page->video_url)) }}?rel=0&modestbranding=1" 
                                            title="{{ $page->title }} Video"
                                            class="w-full h-full"
                                            frameborder="0" 
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                            allowfullscreen>
                                        </iframe>
                                    @else
                                        <!-- Fallback for invalid video URLs -->
                                        <div class="absolute inset-0 bg-gradient-to-br from-red-100 to-pink-200 rounded-xl flex items-center justify-center">
                                            <div class="text-center text-gray-600">
                                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                                <p class="text-sm font-medium">Video unavailable</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
                
                <!-- Page Info Sidebar -->
                <!-- <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Page Info
                    </h3>
                    
                    <div class="space-y-4 text-sm">
                        <div class="flex items-center text-gray-600">
                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>{{ $page->created_at->format('M d, Y') }}</span>
                        </div>
                        
                        @if($page->user)
                            <div class="flex items-center text-gray-600">
                                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>{{ $page->user->name }}</span>
                            </div>
                        @endif
                        
                        @if($page->views > 0)
                            <div class="flex items-center text-gray-600">
                                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <span>{{ $page->views }} {{ Str::plural('view', $page->views) }}</span>
                            </div>
                        @endif
                    </div>
                </div> -->
                
                <!-- Quick Actions Sidebar -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                                            <h3 class="text-lg font-semibold text-[#000080] mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-[#87CEEB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Quick Actions
                        </h3>
                    
                    <div class="space-y-3">
                        <a href="#social-share" class="block w-full text-center px-4 py-2 bg-[#00FF00] text-[#FFFFFF] text-sm font-medium rounded-lg hover:bg-green-600 transition-colors duration-200">
                            Share Page
                        </a>
                        @if ($agent->isDesktop())
                <a href="https://web.whatsapp.com/send?phone=254740857767&text={{ rawurlencode('Hi! I found you through ' . $page->title . ' - ' . url()->current()) }}"
                   target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center justify-center px-8 py-4 bg-green-500 text-white font-bold rounded-xl hover:bg-green-600 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                    </svg>
                    WhatsApp Us
                </a>
            @else
                <a href="https://wa.me/254740857767?text={{ rawurlencode('Hi! I found you through ' . $page->title . ' - ' . url()->current()) }}"
                   target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center justify-center px-8 py-4 bg-green-500 text-white font-bold rounded-xl hover:bg-green-600 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                    </svg>
                    WhatsApp Us
                </a>
            @endif
                        <a href="{{ route('pages.index') }}" class="block w-full text-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                            Browse More
                        </a>
                        @if($relatedPages->count() > 0)
                            <a href="#related-pages" class="block w-full text-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                                Related Content
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Content Area -->
        <div class="lg:col-span-3">
            <article class="prose prose-lg max-w-none">
                @if($page->summary)
                    <div class="bg-gradient-to-r from-gray-50 to-white border-l-4 border-[#87CEEB] p-8 mb-12 rounded-r-lg shadow-sm">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-[#87CEEB] mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-xl text-[#000080] font-semibold leading-relaxed">{{ $page->summary }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Call to Action - Positioned for immediate action -->
                <div class="mb-12 text-center">
                    <div class="bg-gradient-to-r from-[#000080] to-[#87CEEB] rounded-2xl p-8 shadow-xl">
                        <h3 class="text-3xl font-bold text-[#FFFFFF] mb-4">Ready to take action?</h3>
                        <p class="text-xl text-[#FFFFFF] mb-8 max-w-2xl mx-auto">Don't just read - implement these strategies and transform your business today.</p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <button onclick="openInterestModal()" 
                                    class="inline-flex items-center justify-center px-8 py-4 bg-[#00FF00] text-[#FFFFFF] font-bold rounded-xl hover:bg-green-600 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Get Started Today
                            </button>
               
               

            @if ($agent->isDesktop())
                <a href="https://web.whatsapp.com/send?phone=254740857767&text={{ rawurlencode('Hi! I found you through ' . $page->title . ' - ' . url()->current()) }}"
                   target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center justify-center px-8 py-4 bg-green-500 text-white font-bold rounded-xl hover:bg-green-600 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                    </svg>
                    WhatsApp Us
                </a>
            @else
                <a href="https://wa.me/254740857767?text={{ rawurlencode('Hi! I found you through ' . $page->title . ' - ' . url()->current()) }}"
                   target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center justify-center px-8 py-4 bg-green-500 text-white font-bold rounded-xl hover:bg-green-600 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                    </svg>
                    WhatsApp Us
                </a>
            @endif
                        </div>
                    </div>
                </div>
                
                <div class="prose-content bg-white p-8 rounded-2xl shadow-lg">
                    <div class="rich-text-content prose prose-lg max-w-none prose-headings:text-[#000080] prose-headings:font-bold prose-p:text-[#808080] prose-p:leading-relaxed prose-a:text-[#87CEEB] prose-a:no-underline hover:prose-a:text-[#000080] prose-strong:text-[#000080] prose-strong:font-semibold prose-em:text-[#808080] prose-blockquote:border-l-4 prose-blockquote:border-[#87CEEB] prose-blockquote:pl-6 prose-blockquote:italic prose-blockquote:text-[#808080] prose-ul:text-[#808080] prose-ol:text-[#808080] prose-li:marker:text-[#87CEEB] prose-code:bg-gray-100 prose-code:text-[#808080] prose-code:px-2 prose-code:py-1 prose-code:rounded prose-pre:bg-gray-900 prose-pre:text-[#FFFFFF] prose-pre:p-4 prose-pre:rounded-lg prose-pre:overflow-x-auto">
                        {!! $page->body !!}
                        </div>
                    
                    <style>
                        /* Custom styles for Quill editor output */
                        .rich-text-content ol,
                        .rich-text-content ul {
                            list-style: none;
                            padding-left: 0;
                            margin: 1.5rem 0;
                        }
                        
                        .rich-text-content li {
                            position: relative;
                            padding-left: 1.5rem;
                            margin-bottom: 0.5rem;
                            line-height: 1.6;
                            color: #374151;
                        }
                        
                        .rich-text-content ol li::before {
                            content: counter(list-counter) ".";
                            counter-increment: list-counter;
                            position: absolute;
                            left: 0;
                            font-weight: 600;
                            color: #87CEEB;
                        }
                        
                        .rich-text-content ul li::before {
                            content: "•";
                            position: absolute;
                            left: 0;
                            font-weight: 600;
                            color: #87CEEB;
                            font-size: 1.2em;
                        }
                        
                        .rich-text-content ol {
                            counter-reset: list-counter;
                        }
                        
                        /* Override Quill editor styles */
                        .rich-text-content [data-list="bullet"] {
                            list-style: none !important;
                        }
                        
                        /* Convert ordered lists with bullets to unordered lists */
                        .rich-text-content ol[data-list="bullet"] {
                            list-style: none !important;
                        }
                        
                        .rich-text-content ol[data-list="bullet"] li::before {
                            content: "•" !important;
                            counter-increment: none !important;
                        }
                        
                        .rich-text-content .ql-align-justify {
                            text-align: justify;
                        }
                        
                        .rich-text-content .ql-ui {
                            display: none !important;
                        }
                        
                        /* Ensure proper spacing */
                        .rich-text-content p {
                            margin-bottom: 1rem;
                        }
                        
                        .rich-text-content p:last-child {
                            margin-bottom: 0;
                        }
                    </style>
                </div>
            </article>
            
            <!-- Social Share -->
            <div class="mt-16 pt-12 border-t border-gray-200" id="social-share">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div>
                        <h3 class="text-2xl font-bold text-[#000080] mb-2">Share this page</h3>
                        <p class="text-[#808080]">Help others discover this valuable content</p>
                    </div>
                    <div class="flex space-x-4">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" 
                           target="_blank" rel="noopener noreferrer"
                           class="bg-[#000080] text-[#FFFFFF] px-6 py-3 rounded-xl hover:bg-blue-800 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            <span>Facebook</span>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($page->title) }}&url={{ urlencode(url()->current()) }}" 
                           target="_blank" rel="noopener noreferrer"
                           class="bg-[#87CEEB] text-[#FFFFFF] px-6 py-3 rounded-xl hover:bg-blue-500 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.665 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                            <span>Twitter</span>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" 
                           target="_blank" rel="noopener noreferrer"
                           class="bg-[#000080] text-[#FFFFFF] px-6 py-3 rounded-xl hover:bg-blue-800 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                            <span>LinkedIn</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Author Bio -->
            @if($page->user)
                <div class="mt-16 p-8 bg-gradient-to-r from-gray-50 to-white rounded-2xl shadow-lg border border-gray-200">
                    <div class="flex items-center space-x-6">
                        <div class="w-20 h-20 bg-gradient-to-br from-[#000080] to-[#87CEEB] rounded-full flex items-center justify-center shadow-lg">
                            <span class="text-[#FFFFFF] text-2xl font-bold">{{ substr($page->user->first_name, 0, 1) }}</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-2xl font-bold text-[#000080] mb-2">{{ $page->user->full_name }}</h4>
                            <p class="text-[#808080] text-lg leading-relaxed">Business strategist and growth expert with years of experience helping companies scale and succeed.</p>
                        </div>
                    </div>
                </div>
            @endif
            
        </div>
    </div>
</main>

<!-- Interest Modal -->
<div id="interestModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <div>
                <h3 class="text-2xl font-bold text-[#000080]">Get Started Today</h3>
                <p class="text-[#808080] mt-1">Let's discuss how we can help your business grow</p>
            </div>
            <button onclick="closeInterestModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div class="p-6">
            <form id="interestForm" class="space-y-6" action="/leads/submit" method="POST">
                @csrf
                <input type="hidden" name="page_id" value="{{ $page->id }}">
                <input type="hidden" name="ref" value="{{ request()->get('ref', 'direct') }}">
                <input type="hidden" name="source" value="interest_modal">
                
                <!-- UTM Tracking Fields -->
                <input type="hidden" name="utm_source" value="{{ request()->get('utm_source', '') }}">
                <input type="hidden" name="utm_medium" value="{{ request()->get('utm_medium', '') }}">
                <input type="hidden" name="utm_campaign" value="{{ request()->get('utm_campaign', '') }}">
                
                <!-- Name Fields Row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- First Name Field -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                        <input type="text" id="first_name" name="first_name" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               placeholder="Enter your first name">
                    </div>
                    
                    <!-- Middle Name Field -->
                    <div>
                        <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-2">Middle Name</label>
                        <input type="text" id="middle_name" name="middle_name"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               placeholder="Enter your middle name (optional)">
                    </div>
                    
                    <!-- Last Name Field -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               placeholder="Enter your last name">
                    </div>
                </div>
                
                <!-- Demographics Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Date of Birth Field -->
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               placeholder="Select your date of birth">
                        <p class="text-xs text-gray-500 mt-1">This helps us provide age-appropriate content and services</p>
                    </div>
                    
                    <!-- Gender Field -->
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                        <select id="gender" name="gender" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            <option value="">Select your gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                            <option value="Prefer not to say">Prefer not to say</option>
                        </select>
                    </div>
                </div>
                
                <!-- Contact Fields Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               placeholder="Enter your email address">
                    </div>
                    
                    <!-- WhatsApp Number Field -->
                    <div>
                        <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-2">WhatsApp Number</label>
                        <input type="tel" id="whatsapp_number" name="whatsapp_number"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               placeholder="Enter your WhatsApp number (optional)">
                        <p class="text-xs text-gray-500 mt-1">Include country code (e.g., +1 for US, +44 for UK)</p>
                    </div>
                </div>
                
                <!-- Message Field -->
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                    <textarea id="message" name="message" rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 resize-none"
                              placeholder="Tell us about your business goals and how we can help (optional)"></textarea>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-[#000080] to-[#87CEEB] text-[#FFFFFF] font-bold py-4 px-6 rounded-xl hover:from-blue-700 hover:to-indigo-800 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <span class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Send Interest
                    </span>
                </button>
            </form>
        </div>
        
        <!-- Modal Footer -->
        <div class="px-6 pb-6 text-center">
            <p class="text-sm text-gray-500">
                We'll get back to you within 24 hours to discuss your business needs.
            </p>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full text-center p-8">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h3 class="text-2xl font-bold text-[#000080] mb-2">Thank You!</h3>
        <p class="text-[#808080] mb-6">Your interest has been submitted successfully. We'll contact you soon!</p>
        <button onclick="closeSuccessModal()" 
                class="bg-[#000080] text-[#FFFFFF] font-bold py-3 px-6 rounded-xl hover:bg-blue-700 transition-colors duration-200">
            Close
        </button>
    </div>
</div>

<!-- JavaScript for Modal Functionality -->
<script>
function openInterestModal() {
    document.getElementById('interestModal').classList.remove('hidden');
    document.getElementById('interestModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeInterestModal() {
    document.getElementById('interestModal').classList.add('hidden');
    document.getElementById('interestModal').classList.remove('flex');
    document.body.style.overflow = 'auto';
}

function closeSuccessModal() {
    document.getElementById('successModal').classList.add('hidden');
    document.getElementById('successModal').classList.remove('flex');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('interestModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeInterestModal();
    }
});

// Handle form submission
document.getElementById('interestForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="flex items-center justify-center"><svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Submitting...</span>';
    submitBtn.disabled = true;
    
    // Submit form via AJAX
    fetch('/api/leads/submit', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success modal
            closeInterestModal();
            document.getElementById('successModal').classList.remove('hidden');
            document.getElementById('successModal').classList.add('flex');
            
            // Reset form
            document.getElementById('interestForm').reset();
        } else {
            alert('There was an error submitting your interest. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('There was an error submitting your interest. Please try again.');
    })
    .finally(() => {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// Close modals with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeInterestModal();
        closeSuccessModal();
    }
});
</script>
@endsection

@section('additional_css')
<style>
    .prose-content {
        @apply text-[#808080] leading-relaxed;
    }
    .prose-content h1, .prose-content h2, .prose-content h3, .prose-content h4, .prose-content h5, .prose-content h6 {
        @apply font-bold text-[#000080] mb-6 mt-10;
    }
    .prose-content h1 { @apply text-4xl; }
    .prose-content h2 { @apply text-3xl; }
    .prose-content h3 { @apply text-2xl; }
    .prose-content p { @apply mb-6 text-lg; }
    .prose-content ul, .prose-content ol { @apply mb-6 pl-8; }
    .prose-content li { @apply mb-3 text-lg; }
    .prose-content blockquote { 
        @apply border-l-4 border-[#87CEEB] pl-6 italic text-[#808080] my-8 text-lg bg-gray-50 py-4 rounded-r-lg; 
    }
    .prose-content a { 
        @apply text-[#87CEEB] hover:text-[#000080] underline decoration-2 underline-offset-2 transition-colors duration-200; 
    }
    .prose-content img { 
        @apply rounded-xl shadow-lg my-8 max-w-full h-auto; 
    }
    .prose-content code {
        @apply bg-gray-100 text-[#808080] px-2 py-1 rounded text-sm font-mono;
    }
    .prose-content pre {
        @apply bg-gray-900 text-[#FFFFFF] p-4 rounded-lg overflow-x-auto my-6;
    }
    .prose-content pre code {
        @apply bg-transparent text-inherit p-0;
    }
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection

</body>
</html> 