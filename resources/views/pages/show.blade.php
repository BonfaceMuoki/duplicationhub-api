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
                            <div class="mb-4">
                                <a href="{{ $page->video_url }}" target="_blank" rel="noopener noreferrer" class="block">
                                    <div class="relative h-32 rounded-lg shadow-md overflow-hidden group cursor-pointer">
                                        @if(filter_var($page->video_url, FILTER_VALIDATE_URL) && (str_contains($page->video_url, 'youtube.com') || str_contains($page->video_url, 'youtu.be')))
                                            <iframe 
                                                src="{{ str_replace('watch?v=', 'embed/', str_replace('youtu.be/', 'youtube.com/embed/', $page->video_url)) }}?rel=0&modestbranding=1" 
                                                title="{{ $page->title }} Video"
                                                class="w-full h-full"
                                                frameborder="0" 
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                allowfullscreen>
                                            </iframe>
                                            <!-- Overlay with play icon and "Click to open in new tab" text -->
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center">
                                                <div class="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 text-center">
                                                    <svg class="w-8 h-8 mx-auto mb-1" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M8 5v14l11-7z"/>
                                                    </svg>
                                                    <p class="text-xs font-medium">Click to open</p>
                                                </div>
                                            </div>
                                        @else
                                            <!-- Fallback for invalid video URLs -->
                                            <div class="absolute inset-0 bg-gradient-to-br from-red-100 to-pink-200 rounded-lg flex items-center justify-center">
                                                <div class="text-center text-gray-600">
                                                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <p class="text-xs font-medium">Video unavailable</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </a>
                                <p class="text-xs text-gray-500 mt-2 text-center">Click to watch in new tab</p>
                            </div>
                        @endif
                    </div>
                @endif
                
                <!-- Quick Actions Sidebar -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-[#000080] mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#87CEEB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Quick Actions
                    </h3>
                
                    <div class="space-y-3">
                        <button onclick="openInterestModal()" 
                                class="block w-full text-center px-4 py-3 bg-[#00FF00] text-[#FFFFFF] text-sm font-medium rounded-lg hover:bg-green-600 transition-colors duration-200">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Get Started
                        </button>
                        <a href="#social-share" class="block w-full text-center px-4 py-2 bg-blue-500 text-[#FFFFFF] text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors duration-200">
                            Share Page
                        </a>
                        <a href="https://wa.me/254740857767?text={{ rawurlencode('Hi! I found you through ' . $page->title . ' - ' . url()->current()) }}"
                           target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center justify-center px-8 py-4 bg-green-500 text-white font-bold rounded-xl hover:bg-green-600 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.87 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                            </svg>
                            WhatsApp Us
                        </a>
                        <a href="{{ route('pages.index') }}" class="block w-full text-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                            Browse More
                        </a>
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
                                    class="inline-flex items-center justify-center px-8 py-4 bg-[#00FF00] text-[#FFFFFF] font-bold rounded-xl hover:bg-green-600 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Get Started Today
                            </button>
                            <a href="https://wa.me/254740857767?text={{ rawurlencode('Hi! I found you through ' . $page->title . ' - ' . url()->current()) }}"
                               target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center justify-center px-8 py-4 bg-green-500 text-white font-bold rounded-xl hover:bg-green-600 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
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
                
                <!-- Name Fields Row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                        <input type="text" id="first_name" name="first_name" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               placeholder="Enter your first name">
                    </div>
                    
                    <div>
                        <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-2">Middle Name</label>
                        <input type="text" id="middle_name" name="middle_name"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               placeholder="Enter your middle name (optional)">
                    </div>
                    
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               placeholder="Enter your last name">
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
                        <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-2">WhatsApp Number</label>
                        <input type="tel" id="whatsapp_number" name="whatsapp_number"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               placeholder="Enter your WhatsApp number (optional)">
                    </div>
                </div>
                
                <!-- Date of Birth Field -->
                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                           placeholder="Select your date of birth (optional)">
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
</style>
@endsection 