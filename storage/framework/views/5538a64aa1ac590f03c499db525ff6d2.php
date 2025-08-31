<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Meta Tags -->
    <title><?php echo $__env->yieldContent('title', config('app.name')); ?></title>
    <meta name="description" content="<?php echo $__env->yieldContent('meta_description', 'Discover business insights and strategies'); ?>">
    <meta name="keywords" content="<?php echo $__env->yieldContent('meta_keywords', 'business, growth, strategy, insights'); ?>">
    <meta name="author" content="<?php echo e(config('app.name')); ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?php echo $__env->yieldContent('og_type', 'website'); ?>">
    <meta property="og:url" content="<?php echo $__env->yieldContent('canonical_url', url()->current()); ?>">
    <meta property="og:title" content="<?php echo $__env->yieldContent('title', config('app.name')); ?>">
    <meta property="og:description" content="<?php echo $__env->yieldContent('meta_description', 'Discover business insights and strategies'); ?>">
    <meta property="og:image" content="<?php echo $__env->yieldContent('og_image', asset('assets/emails/welcome_.png')); ?>">
    <meta property="og:site_name" content="<?php echo e(config('app.name')); ?>">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?php echo $__env->yieldContent('canonical_url', url()->current()); ?>">
    <meta name="twitter:title" content="<?php echo $__env->yieldContent('title', config('app.name')); ?>">
    <meta name="twitter:description" content="<?php echo $__env->yieldContent('meta_description', 'Discover business insights and strategies'); ?>">
    <meta name="twitter:image" content="<?php echo $__env->yieldContent('og_image', asset('assets/emails/welcome_.png')); ?>">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo $__env->yieldContent('canonical_url', url()->current()); ?>">
    
    <!-- Additional Meta Tags -->
    <?php echo $__env->yieldContent('additional_meta'); ?>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <?php echo $__env->yieldContent('additional_css'); ?>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <a href="/" class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-xl">🔁</span>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">Duplication Hub</h1>
                            <p class="text-sm text-gray-600">Duplicate and Win</p>
                        </div>
                    </a>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/" class="text-blue-600 font-medium">Home</a>
                </div>
                
                <!-- Login Button - Right Aligned -->
                <div class="hidden md:flex items-center">
                    <a href="/login" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 shadow-sm hover:shadow-md">
                        Login
                    </a>
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" class="text-gray-700 hover:text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb -->
    <?php if (! empty(trim($__env->yieldContent('breadcrumb')))): ?>
        <div class="bg-white border-b border-gray-200 py-3">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <?php echo $__env->yieldContent('breadcrumb'); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h4 class="text-xl font-bold mb-4"><?php echo e(config('app.name')); ?></h4>
                <p class="text-gray-400 mb-6">
                    Duplicate and Win. Your step-by-step success system for multiple online platforms.
                </p>
                <div class="border-t border-gray-700 pt-6 text-gray-400">
                    <p>&copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <?php echo $__env->yieldContent('additional_js'); ?>
    
    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.querySelector('button[type="button"]');
            const mobileMenu = document.querySelector('.md\\:hidden + div');
            
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }
        });
    </script>
</body>
</html> <?php /**PATH /var/www/html/resources/views/layouts/master.blade.php ENDPATH**/ ?>