<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Business insights and strategies for growth')">
    <meta name="keywords" content="@yield('meta_keywords', 'business tips, growth strategies, marketing insights')">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('canonical_url', url()->current())">
    <meta property="og:title" content="@yield('og_title', config('app.name'))">
    <meta property="og:description" content="@yield('og_description', 'Business insights and strategies for growth')">
    <meta property="og:image" content="@yield('og_image', asset('assets/emails/email.png'))">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="@yield('canonical_url', url()->current())">
    <meta property="twitter:title" content="@yield('og_title', config('app.name'))">
    <meta property="twitter:description" content="@yield('og_description', 'Business insights and strategies for growth')">
    <meta property="twitter:image" content="@yield('og_image', asset('assets/emails/email.png'))">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="@yield('canonical_url', url()->current())">
    
    <title>@yield('title', config('app.name'))</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .text-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .prose {
            max-width: 65ch;
            margin: 0 auto;
        }
        .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
            color: #1f2937;
            font-weight: 700;
            line-height: 1.25;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        .prose h1 { font-size: 2.25rem; }
        .prose h2 { font-size: 1.875rem; }
        .prose h3 { font-size: 1.5rem; }
        .prose h4 { font-size: 1.25rem; }
        .prose p {
            margin-bottom: 1.25rem;
            line-height: 1.75;
            color: #374151;
        }
        .prose ul, .prose ol {
            margin-bottom: 1.25rem;
            padding-left: 1.5rem;
        }
        .prose li {
            margin-bottom: 0.5rem;
            line-height: 1.75;
        }
        .prose blockquote {
            border-left: 4px solid #667eea;
            padding-left: 1rem;
            margin: 1.5rem 0;
            font-style: italic;
            color: #6b7280;
        }
        .prose a {
            color: #667eea;
            text-decoration: underline;
        }
        .prose a:hover {
            color: #5a67d8;
        }
        .share-button {
            transition: all 0.3s ease;
        }
        .share-button:hover {
            transform: translateY(-2px);
        }
        .highlight {
            background-color: #fef3c7;
            padding: 0.125rem 0.25rem;
            border-radius: 0.25rem;
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
    
    @yield('additional_css')
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="gradient-bg text-white shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                        <span class="text-purple-600 font-bold text-xl">B</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">{{ config('app.name') }}</h1>
                        <p class="text-purple-100">Business Insights & Strategies</p>
                    </div>
                </div>
                <nav class="hidden md:flex space-x-6">
                    <a href="{{ route('blog.index') }}" class="hover:text-purple-200 transition-colors">Home</a>
                    <a href="{{ route('blog.index') }}#about" class="hover:text-purple-200 transition-colors">About</a>
                    <a href="{{ route('blog.index') }}#contact" class="hover:text-purple-200 transition-colors">Contact</a>
                </nav>
            </div>
        </div>
    </header>

    @yield('breadcrumb')

    @yield('content')

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-4 text-center">
            <div class="mb-8">
                <h4 class="text-2xl font-bold mb-4">{{ config('app.name') }}</h4>
                <p class="text-gray-400 max-w-2xl mx-auto">
                    Empowering businesses with insights and strategies for growth.
                </p>
            </div>
            
            <div class="border-t border-gray-700 pt-8">
                <p class="text-gray-400">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add loading animation for images
        document.querySelectorAll('img').forEach(img => {
            img.addEventListener('load', function() {
                this.style.opacity = '1';
            });
            img.style.opacity = '0';
            img.style.transition = 'opacity 0.3s ease';
        });
    </script>
    
    @yield('additional_js')
</body>
</html> 