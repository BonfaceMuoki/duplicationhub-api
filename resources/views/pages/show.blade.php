@extends('layouts.master')

@section('title', $seoData['title'])
@section('meta_description', $seoData['description'])
@section('meta_keywords', $seoData['keywords'])

@section('og_type', $seoData['og_type'])
@section('og_image', $seoData['og_image'] ?: $page->full_image_url ?: asset('assets/emails/welcome_.png'))
@section('canonical_url', $seoData['canonical_url'])

@section('additional_meta')
    <!-- Article specific meta tags -->
    <meta property="article:published_time" content="{{ $seoData['published_time'] }}">
    <meta property="article:modified_time" content="{{ $seoData['modified_time'] }}">
    <meta property="article:author" content="{{ $seoData['author'] }}">
    <meta property="article:section" content="Business Growth">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoData['title'] }}">
    <meta name="twitter:description" content="{{ $seoData['description'] }}">
    <meta name="twitter:image" content="{{ $seoData['og_image'] ?: $page->full_image_url ?: asset('assets/emails/welcome_.png') }}">
    
    <!-- Additional Open Graph tags -->
    <meta property="og:site_name" content="{{ config('app.name', 'Duplication Hub') }}">
    <meta property="og:locale" content="en_US">
    
    <!-- Schema Markup -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Article",
        "headline": "{{ $page->title }}",
        "description": "{{ $seoData['description'] }}",
        "image": "{{ $seoData['og_image'] ?: $page->full_image_url ?: asset('assets/emails/welcome_.png') }}",
        "author": {
            "@type": "Person",
            "name": "{{ $seoData['author'] }}"
        },
        "publisher": {
            "@type": "Organization",
            "name": "{{ config('app.name', 'Duplication Hub') }}",
            "logo": {
                "@type": "ImageObject",
                "url": "{{ asset('favicon.ico') }}"
            }
        },
        "datePublished": "{{ $seoData['published_time'] }}",
        "dateModified": "{{ $seoData['modified_time'] }}",
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "{{ $seoData['canonical_url'] }}"
        }
    }
    </script>
@endsection

@section('breadcrumb')
    <!-- <nav class="flex" aria-label="Breadcrumb">
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
    </nav> -->
@endsection

@section('content')
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
                                <div class="relative group overflow-hidden rounded-xl shadow-md cursor-pointer" onclick="openImageModal('{{ $page->full_image_url }}', '{{ $page->title }}')">
                                    <img src="{{ $page->full_image_url }}" alt="{{ $page->title }}" 
                                         class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    <!-- Click to enlarge indicator -->
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <div class="bg-white bg-opacity-90 rounded-full p-3">
                                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                            </svg>
                                        </div>
                                    </div>
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
                            <div class="mb-4">
                                <a href="{{ $page->video_url }}" target="_blank" rel="noopener noreferrer" class="block">
                                    <div class="relative h-32 rounded-lg shadow-md overflow-hidden group cursor-pointer bg-gradient-to-br from-gray-900 to-gray-800">
                                        @if(filter_var($page->video_url, FILTER_VALIDATE_URL) && (str_contains($page->video_url, 'youtube.com') || str_contains($page->video_url, 'youtu.be')))
                                            <!-- YouTube Video Embed -->
                                            <iframe 
                                                src="{{ str_replace('watch?v=', 'embed/', str_replace('youtu.be/', 'youtube.com/embed/', $page->video_url)) }}?rel=0&modestbranding=1" 
                                                title="{{ $page->title }} Video"
                                                class="w-full h-full"
                                                frameborder="0" 
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                allowfullscreen>
                                            </iframe>
                                        @else
                                            <!-- Generic Video Display -->
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <div class="text-center text-white">
                                                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-3 mx-auto group-hover:bg-opacity-30 transition-all duration-300">
                                                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M8 5v14l11-7z"/>
                                                        </svg>
                                                    </div>
                                                    <p class="text-sm font-medium">Watch Video</p>
                                                    <p class="text-xs text-gray-300 mt-1">Click to open</p>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <!-- Universal Overlay for all videos -->
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
                                            <div class="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 text-center">
                                                <div class="w-12 h-12 bg-white bg-opacity-90 rounded-full flex items-center justify-center mb-2 mx-auto">
                                                    <svg class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M8 5v14l11-7z"/>
                                                    </svg>
                                                </div>
                                                <p class="text-xs font-medium">Click to watch</p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <p class="text-xs text-gray-500 mt-2 text-center">Click to watch in new tab</p>
                            </div>
                        @endif
                    </div>
                @endif
                
                <!-- Quick Actions Sidebar -->
                <!-- <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-[#000080] mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#87CEEB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Quick Actions
                    </h3>
                
                    <div class="space-y-3">
                        <button onclick="openInterestModal()" 
                                class="block w-full text-center px-4 py-3 bg-green-600 text-[#FFFFFF] text-sm font-medium rounded-lg hover:bg-green-700 transition-colors duration-200">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Get Started
                        </button>
                        <a href="#social-share" class="block w-full text-center px-4 py-2 bg-blue-500 text-[#FFFFFF] text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors duration-200">
                            Share Page
                        </a>
                        <a href="javascript:void(0)" onclick="openWhatsApp('HI ADMIN. PLEASE SEND ME THE LINK TO JOIN THIS PLATFORM AND MY FREE WEBPAGE.')"
                           class="inline-flex items-center justify-center px-8 py-4 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.87 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                            </svg>
                            WhatsApp Us
                        </a>
                        <a href="{{ route('pages.index') }}" class="block w-full text-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                            Browse More
                        </a>
                    </div>
                </div> -->
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-xl text-[#000080] font-semibold leading-relaxed">{{ $page->summary }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Call to Action -->
                <div class="mb-12 text-center">
                    <div class="bg-gradient-to-r from-[#000080] to-[#87CEEB] rounded-2xl p-8 shadow-xl">
                        <h3 class="text-3xl font-bold text-[#FFFFFF] mb-4">Ready to take action?</h3>
                        <p class="text-xl text-[#FFFFFF] mb-8">Don't just read - implement these strategies and transform your business today.</p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <button onclick="openInterestModal()" 
                                    class="inline-flex items-center justify-center px-8 py-4 bg-green-600 text-[#FFFFFF] font-bold rounded-xl hover:bg-green-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Get Started Today
                            </button>
                            <a href="javascript:void(0)" onclick="openWhatsApp('HI ADMIN. PLEASE SEND ME THE LINK TO JOIN THIS PLATFORM AND MY FREE WEBPAGE.')"
                               class="inline-flex items-center justify-center px-8 py-4 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.87 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                </svg>
                                WhatsApp Us
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="prose-content bg-white p-8 rounded-2xl shadow-lg">
                    <div class="rich-text-content prose prose-lg max-w-none prose-headings:text-[#000080] prose-headings:font-bold prose-p:text-[#808080] prose-p:leading-relaxed prose-a:text-[#87CEEB] prose-a:no-underline hover:prose-a:text-[#000080] prose-strong:text-[#000080] prose-strong:font-semibold prose-em:text-[#808080] prose-blockquote:border-l-4 prose-blockquote:border-[#87CEEB] prose-blockquote:pl-6 prose-blockquote:italic prose-blockquote:text-[#808080] prose-ul:text-[#808080] prose-ol:text-[#808080] prose-li:marker:text-[#87CEEB] prose-code:bg-gray-100 prose-code:text-[#808080] prose-code:px-2 prose-code:py-1 prose-code:rounded prose-pre:bg-gray-900 prose-pre:text-[#FFFFFF] prose-pre:p-4 prose-pre:rounded-lg prose-pre:overflow-x-auto">
                        {!! $page->body !!}
                    </div>
                </div>
                
                <!-- Bottom Call to Action -->
                <div class="mt-16 bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-800 rounded-3xl p-12 text-center relative overflow-hidden">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute top-0 left-0 w-full h-full" style="background-image: radial-gradient(circle at 25% 25%, rgba(255,255,255,0.2) 0%, transparent 50%), radial-gradient(circle at 75% 75%, rgba(255,255,255,0.1) 0%, transparent 50%);"></div>
                    </div>
                    
                    <!-- Content -->
                    <div class="relative z-10">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-white bg-opacity-20 rounded-full mb-6">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        
                        <h2 class="text-4xl md:text-5xl font-bold text-white mb-6 leading-tight">
                            Tap the button below to receive:
                        </h2>
                        
                                    <!-- Trust Indicators -->
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-8 text-white text-opacity-80">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm font-medium">The Link To Join This Platform</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm font-medium">Your Own Free Webpage (just like this one!)</span>
                            </div>                         
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-6 justify-center items-center mb-8">
                            <button onclick="openInterestModal()" 
                                    class="group relative inline-flex items-center justify-center px-10 py-5 bg-green-600 text-white font-bold text-lg rounded-2xl hover:bg-green-700 transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-3xl">
                                <span class="flex items-center">
                                    <svg class="w-6 h-6 mr-3 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Get My Free Webpage
                                </span>
                                <div class="absolute inset-0 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-2xl opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                            </button>
                            
                            <a href="javascript:void(0)" onclick="openWhatsApp('HI ADMIN. PLEASE SEND ME THE LINK TO JOIN THIS PLATFORM AND MY FREE WEBPAGE.')"
                               class="group inline-flex items-center justify-center px-10 py-5 bg-green-600 text-white font-bold text-lg rounded-2xl hover:bg-green-700 transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-3xl">
                                <svg class="w-6 h-6 mr-3 group-hover:scale-110 transition-transform duration-300" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.87 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                </svg>
                                Chat on WhatsApp
                            </a>
                        </div>
                        

                    </div>
                </div>
            </article>
        </div>
    </div>
</main>

<!-- Interest Modal -->
<div id="interestModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <div>
                <h3 class="text-2xl font-bold text-[#000080]">Get Your Free Webpage</h3>
                <p class="text-[#808080] mt-1">Fill out the form below to receive your free webpage and platform access</p>
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
                
                <!-- Name Fields Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                        <input type="text" id="first_name" name="first_name" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               placeholder="Enter your first name">
                    </div>
                    
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Surname *</label>
                        <input type="text" id="last_name" name="last_name" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               placeholder="Enter your surname">
                    </div>
                </div>
                
                <!-- Contact Fields Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               placeholder="Enter your email address">
                    </div>
                    
                    <div>
                        <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-2">WhatsApp Number (with country code) *</label>
                        <input type="tel" id="whatsapp_number" name="whatsapp_number" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               placeholder="+1234567890">
                    </div>
                </div>
                
                <!-- Gender Field -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                    <select id="gender" name="gender"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                        <option value="">Select your gender (optional)</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                        <option value="Prefer not to say">Prefer not to say</option>
                    </select>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-green-600 text-white font-bold py-4 px-6 rounded-xl hover:bg-green-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <span class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Get My Free Webpage
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

<!-- Toast Container -->
<div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2">
    <!-- Toast notifications will be dynamically inserted here -->
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-95 z-50 hidden items-center justify-center p-4 backdrop-blur-sm">
    <div class="relative w-full h-full flex items-center justify-center">
        <!-- Close Button -->
        <button onclick="closeImageModal()" 
                class="absolute top-6 right-6 z-20 bg-white bg-opacity-20 backdrop-blur-sm text-white rounded-full p-3 hover:bg-opacity-30 transition-all duration-300 transform hover:scale-110 shadow-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        
        <!-- Image Container -->
        <div class="relative max-w-7xl max-h-full w-full h-full flex items-center justify-center">
            <div class="relative group">
                <img id="modalImage" src="" alt="" 
                     class="max-w-full max-h-[85vh] object-contain rounded-2xl shadow-2xl transform transition-all duration-500 group-hover:scale-105">
                
                <!-- Image Title Overlay -->
                <div id="imageTitle" class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black via-black/80 to-transparent text-white p-8 rounded-b-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <h3 class="text-2xl font-bold mb-2"></h3>
                    <p class="text-sm text-gray-300">Click outside or press ESC to close</p>
                </div>
                
                <!-- Loading Spinner -->
                <div id="imageLoading" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 rounded-2xl">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white"></div>
                </div>
            </div>
        </div>
        
        <!-- Navigation Arrows (for future enhancement) -->
        <div class="absolute left-6 top-1/2 transform -translate-y-1/2 z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <button class="bg-white bg-opacity-20 backdrop-blur-sm text-white rounded-full p-3 hover:bg-opacity-30 transition-all duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
        </div>
        
        <div class="absolute right-6 top-1/2 transform -translate-y-1/2 z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <button class="bg-white bg-opacity-20 backdrop-blur-sm text-white rounded-full p-3 hover:bg-opacity-30 transition-all duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
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

function openImageModal(imageUrl, imageTitle) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const imageTitleElement = document.getElementById('imageTitle').querySelector('h3');
    const imageLoading = document.getElementById('imageLoading');
    
    // Show modal immediately
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
    
    // Show loading spinner
    imageLoading.classList.remove('hidden');
    imageLoading.classList.add('flex');
    
    // Set image properties
    modalImage.alt = imageTitle;
    imageTitleElement.textContent = imageTitle;
    
    // Load image with fade-in effect
    const img = new Image();
    img.onload = function() {
        modalImage.src = imageUrl;
        imageLoading.classList.add('hidden');
        imageLoading.classList.remove('flex');
        
        // Add fade-in animation
        modalImage.style.opacity = '0';
        modalImage.style.transform = 'scale(0.9)';
        setTimeout(() => {
            modalImage.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            modalImage.style.opacity = '1';
            modalImage.style.transform = 'scale(1)';
        }, 50);
    };
    
    img.onerror = function() {
        imageLoading.classList.add('hidden');
        imageLoading.classList.remove('flex');
        modalImage.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgdmlld0JveD0iMCAwIDQwMCAzMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0MDAiIGhlaWdodD0iMzAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0xNzUgMTI1SDIyNVYxNzVIMTc1VjEyNVoiIGZpbGw9IiM5Q0EzQUYiLz4KPHN2ZyB4PSIxNzUiIHk9IjEyNSIgd2lkdGg9IjUwIiBoZWlnaHQ9IjUwIiB2aWV3Qm94PSIwIDAgMjQgMjQiIGZpbGw9Im5vbmUiPgo8cGF0aCBkPSJNMTIgMkM2LjQ4IDIgMiA2LjQ4IDIgMTJTNi40OCAyMiAxMiAyMlMyMiAxNy41MiAyMiAxMlMxNy41MiAyIDEyIDJaTTEzIDE3SDEzVjE1SDEzVjE3Wk0xMyAxM0gxM1Y3SDEzVjEzWiIgZmlsbD0iIzlDQTNBRiIvPgo8L3N2Zz4KPC9zdmc+';
        modalImage.alt = 'Image failed to load';
    };
    
    img.src = imageUrl;
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const imageLoading = document.getElementById('imageLoading');
    
    // Add fade-out animation
    modalImage.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
    modalImage.style.opacity = '0';
    modalImage.style.transform = 'scale(0.9)';
    
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
        
        // Reset image for next time
        modalImage.src = '';
        modalImage.style.opacity = '1';
        modalImage.style.transform = 'scale(1)';
        imageLoading.classList.add('hidden');
        imageLoading.classList.remove('flex');
    }, 300);
}

// Close modal when clicking outside
document.getElementById('interestModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeInterestModal();
    }
});

document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

// Handle form submission
document.getElementById('interestForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate phone number format
    const phoneInput = document.getElementById('whatsapp_number');
    const phoneValue = phoneInput.value.trim();
    
    // Check if phone number starts with + (international format)
    if (phoneValue && !phoneValue.startsWith('+')) {
        showToast('Please enter a valid  whatsapp phone number starting with + (e.g., +27732050995, +1234567890, +44123456789)', 'warning', 6000);
        phoneInput.focus();
        return;
    }
    
    // Basic validation for phone number length (minimum 10 digits after country code)
    if (phoneValue && phoneValue.length < 10) {
        showToast('Please enter a complete whatsapp phone number with country code (minimum 10 digits)', 'warning', 5000);
        phoneInput.focus();
        return;
    }
    
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
            // Show success toast
            showToast('Thank you! Your interest has been submitted successfully. We\'ll contact you soon!', 'success', 8000);
            
            // Show success modal
            closeInterestModal();
            document.getElementById('successModal').classList.remove('hidden');
            document.getElementById('successModal').classList.add('flex');
            
    // Reset form and clear any cached values
    document.getElementById('interestForm').reset();
    
    // Clear any cached form data
    const form = document.getElementById('interestForm');
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.value = '';
        input.removeAttribute('value');
    });
        } else {
            showToast('There was an error submitting your interest. Please try again.', 'error', 6000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('There was an error submitting your interest. Please try again.', 'error', 6000);
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
        closeImageModal();
    }
});

// Toast notification system
function showToast(message, type = 'info', duration = 5000) {
    const container = document.getElementById('toastContainer');
    const toastId = 'toast-' + Date.now();
    
    // Toast type configurations
    const toastConfig = {
        success: {
            bgColor: 'bg-green-500',
            icon: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>`,
            textColor: 'text-white'
        },
        error: {
            bgColor: 'bg-red-500',
            icon: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>`,
            textColor: 'text-white'
        },
        warning: {
            bgColor: 'bg-yellow-500',
            icon: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>`,
            textColor: 'text-white'
        },
        info: {
            bgColor: 'bg-blue-500',
            icon: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>`,
            textColor: 'text-white'
        }
    };
    
    const config = toastConfig[type] || toastConfig.info;
    
    // Create toast element
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `${config.bgColor} ${config.textColor} rounded-lg shadow-lg p-4 max-w-sm w-full transform transition-all duration-300 ease-in-out translate-x-full opacity-0`;
    
    toast.innerHTML = `
        <div class="flex items-start">
            <div class="flex-shrink-0">
                ${config.icon}
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <div class="ml-4 flex-shrink-0">
                <button onclick="removeToast('${toastId}')" class="inline-flex text-white hover:text-gray-200 focus:outline-none focus:text-gray-200 transition-colors duration-200">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;
    
    // Add to container
    container.appendChild(toast);
    
    // Trigger animation
    setTimeout(() => {
        toast.classList.remove('translate-x-full', 'opacity-0');
        toast.classList.add('translate-x-0', 'opacity-100');
    }, 100);
    
    // Auto remove after duration
    if (duration > 0) {
        setTimeout(() => {
            removeToast(toastId);
        }, duration);
    }
    
    return toastId;
}

function removeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }
}

// WhatsApp detection and URL generation
function openWhatsApp(message) {
    const phoneNumber = '+27732050995';
    const encodedMessage = encodeURIComponent(message);
    
    // Detect if user is on mobile device
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    
    let whatsappUrl;
    if (isMobile) {
        // For mobile devices, use the app URL
        whatsappUrl = `whatsapp://send?phone=${phoneNumber}&text=${encodedMessage}`;
    } else {
        // For desktop, use WhatsApp Web
        whatsappUrl = `https://web.whatsapp.com/send?phone=${phoneNumber}&text=${encodedMessage}`;
    }
    
    // Open WhatsApp
    window.open(whatsappUrl, '_blank');
}
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
    
    /* Image Modal Enhancements */
    #imageModal {
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
    
    #imageModal img {
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    #imageModal .group:hover img {
        transform: scale(1.02);
    }
    
    /* Loading Animation */
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    
    /* Smooth Modal Transitions */
    #imageModal {
        transition: opacity 0.3s ease-in-out;
    }
    
    #imageModal.hidden {
        opacity: 0;
        pointer-events: none;
    }
    
    #imageModal.flex {
        opacity: 1;
        pointer-events: auto;
    }
    
    /* Toast Animations */
    #toastContainer {
        z-index: 9999;
    }
    
    .toast-enter {
        transform: translateX(100%);
        opacity: 0;
    }
    
    .toast-enter-active {
        transform: translateX(0);
        opacity: 1;
        transition: all 0.3s ease-in-out;
    }
    
    .toast-exit {
        transform: translateX(0);
        opacity: 1;
    }
    
    .toast-exit-active {
        transform: translateX(100%);
        opacity: 0;
        transition: all 0.3s ease-in-out;
    }
    
    /* Toast hover effects */
    #toastContainer > div:hover {
        transform: translateX(-4px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
</style>
@endsection 