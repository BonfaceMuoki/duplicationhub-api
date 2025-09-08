@extends('layouts.master')

@section('title', 'Duplication Hub - Duplicate and Win | ' . config('app.name'))

@section('meta_description', 'Join Duplication Hub and learn how to Duplicate and Win. Get step-by-step guidance for multiple online platforms and build your duplicating team with ease.')

@section('og_type', 'website')

@section('additional_css')
<style>
    .gradient-text {
        background: linear-gradient(135deg, #87CEEB 0%, #000080 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .hero-gradient {
        background: linear-gradient(135deg, #87CEEB 0%, #000080 100%);
    }
    
    .value-card {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .value-card:hover {
        transform: translateY(-5px);
        border-color: #87CEEB;
        box-shadow: 0 20px 40px rgba(135, 206, 235, 0.15);
    }
    
    .stats-card {
        background: linear-gradient(135deg, #87CEEB 0%, #000080 100%);
    }
    
    .cta-gradient {
        background: linear-gradient(135deg, #000080 0%, #87CEEB 100%);
    }
    
    .platform-card {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .platform-card:hover {
        transform: translateY(-5px);
        border-color: #87CEEB;
        box-shadow: 0 20px 40px rgba(135, 206, 235, 0.15);
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

@section('content')
<!-- Toast Container -->
<div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2">
    <!-- Toast notifications will be dynamically inserted here -->
</div>

<!-- Hero Section -->
<section class="hero-gradient text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight">
            🔁 Welcome to the
            <span class="block text-[#000080]">Duplication Hub</span>
        </h1>
        <p class="text-xl md:text-2xl mb-8 max-w-4xl mx-auto text-[#FFFFFF]">
            <span class="font-semibold text-[#00FF00]">Duplicate and Win.</span>
        </p>
        <p class="text-lg md:text-xl mb-8 max-w-4xl mx-auto text-[#FFFFFF]">
            Are you tired of joining platforms and not knowing what to do next?
        </p>
        <p class="text-lg md:text-xl mb-8 max-w-4xl mx-auto text-[#FFFFFF]">
            At Duplication Hub, we remove the confusion. We give you a clear, simple path to follow — 
            so you can start earning faster, help others do the same, and build a duplicating team with ease.
        </p>
        <p class="text-lg md:text-xl mb-8 max-w-4xl mx-auto text-[#FFFFFF]">
            This isn't about hype or hard selling. It's about using a working system to 
            <span class="font-semibold text-[#00FF00]">Duplicate and Win.</span>
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a onclick="openPageRequestModal()"  class="bg-[#00FF00] text-[#FFFFFF] px-8 py-4 rounded-xl font-bold text-lg hover:bg-green-600 transition-all duration-200 transform hover:scale-105 shadow-lg">
                Ask for Pages
            </a>
        </div>
    </div>
</section>

<!-- What Is Duplication Hub Section -->
<section id="what-is" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-[#000080] mb-6">
                💡 What Is <span class="gradient-text">Duplication Hub?</span>
            </h2>
            <p class="text-xl text-[#808080] max-w-4xl mx-auto">
                Duplication Hub is your step-by-step success system for multiple online platforms.
            </p>
        </div>
        
        <div class="text-center mb-12">
            <p class="text-xl text-[#808080] max-w-4xl mx-auto mb-8">
                Instead of struggling to explain how things work, you simply:
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <div class="text-center">
                    <div class="w-16 h-16 bg-[#87CEEB] rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-[#FFFFFF] font-bold text-xl">1</span>
                    </div>
                    <h3 class="text-xl font-bold text-[#000080] mb-2">Choose a platform</h3>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-[#00FF00] rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-[#FFFFFF] font-bold text-xl">2</span>
                    </div>
                    <h3 class="text-xl font-bold text-[#000080] mb-2">Follow the setup guide</h3>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-[#000080] rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-[#FFFFFF] font-bold text-xl">3</span>
                    </div>
                    <h3 class="text-xl font-bold text-[#000080] mb-2">Share your own personalized duplication page</h3>
                </div>
            </div>
        </div>
        
        <div class="text-center">
            <p class="text-lg text-[#808080] max-w-4xl mx-auto">
                Whether you're promoting KhayaCONNECT, InovoCB, Spark Agro Life, LiveGood, or other income opportunities, 
                the Duplication Hub helps you build with confidence — no experience needed.
            </p>
        </div>
    </div>
</section>

<!-- Why This Works Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-[#000080] mb-6">
                🔥 Why This <span class="gradient-text">Works</span>
            </h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="value-card bg-white p-8 rounded-2xl text-center">
                <div class="w-16 h-16 bg-[#87CEEB] rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-[#FFFFFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-[#000080] mb-4">✅ No explaining</h3>
                <p class="text-[#808080]">
                    We provide ready-made pages that explain everything for you.
                </p>
            </div>
            
            <div class="value-card bg-white p-8 rounded-2xl text-center">
                <div class="w-16 h-16 bg-[#00FF00] rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-[#FFFFFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-[#000080] mb-4">✅ No chasing people</h3>
                <p class="text-[#808080]">
                    Just plug in your link, follow the steps, and share — it's that easy.
                </p>
            </div>
            
            <div class="value-card bg-white p-8 rounded-2xl text-center">
                <div class="w-16 h-16 bg-[#000080] rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-[#FFFFFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-[#000080] mb-4">✅ No complicated tools</h3>
                <p class="text-[#808080]">
                    Because here, it's not about effort alone — it's about learning to Duplicate and Win.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Trust Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-[#000080] mb-6">
                🔒 Why You Can <span class="gradient-text">Trust</span> Duplication Hub
            </h2>
            <p class="text-xl text-[#808080] max-w-4xl mx-auto">
                Your trust is everything — and we don't take it lightly.
            </p>
        </div>
        
        <div class="max-w-4xl mx-auto">
            <p class="text-lg text-[#808080] mb-6">
                At Duplication Hub, we go the extra mile to thoroughly research, test, and validate every opportunity 
                before it's shared on this platform. We understand how many people have been misled or scammed online, 
                and we're here to do the opposite — to protect, guide, and empower you.
            </p>
            <p class="text-lg text-[#808080] mb-6">
                We only feature platforms that we or our close partners have personally used, with real results. 
                Every link, page, and guide you find here is designed to help you move forward with clarity, safety, and confidence.
            </p>
            <p class="text-lg text-[#808080]">
                You're not just clicking random links — you're following a system built on integrity, transparency, and your success.
            </p>
        </div>
    </div>
</section>



<!-- Who This Is For Section -->
<section id="who-this-is-for" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-[#000080] mb-6">
                📣 Who This <span class="gradient-text">Is For</span>
            </h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-[#87CEEB] rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-[#FFFFFF] text-2xl">🔰</span>
                </div>
                <h3 class="text-xl font-bold text-[#000080] mb-2">Beginners</h3>
                <p class="text-[#808080]">who need simple guidance</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-[#00FF00] rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-[#FFFFFF] text-2xl">🤝</span>
                </div>
                <h3 class="text-xl font-bold text-[#000080] mb-2">Leaders</h3>
                <p class="text-[#808080]">who want duplicating teams</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-[#000080] rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-[#FFFFFF] text-2xl">⏱</span>
                </div>
                <h3 class="text-xl font-bold text-[#000080] mb-2">Busy people</h3>
                <p class="text-[#808080]">who want results without pressure</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-[#87CEEB] rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-[#FFFFFF] text-2xl">🔄</span>
                </div>
                <h3 class="text-xl font-bold text-[#000080] mb-2">Anyone tired</h3>
                <p class="text-[#808080]">of starting over with each platform</p>
            </div>
        </div>
    </div>
</section>

<!-- Get Your Own Page Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-[#000080] mb-6">
                📲 Want Your Own <span class="gradient-text">Duplication Page?</span>
            </h2>
            <p class="text-xl text-[#808080] max-w-4xl mx-auto">
                If you'd like to use this exact system to grow your team, we'll customize it for you — 
                branded with your name and your personal referral link.
            </p>
        </div>
        
        <div class="max-w-4xl mx-auto">
            <div class="bg-white p-8 rounded-2xl shadow-lg mb-8">
                <h3 class="text-2xl font-bold text-[#000080] mb-6">Here's how to get yours:</h3>
                <ol class="text-lg text-[#808080] space-y-4">
                    <li class="flex items-start space-x-3">
                        <span class="w-8 h-8 bg-[#87CEEB] text-[#FFFFFF] rounded-full flex items-center justify-center flex-shrink-0">1</span>
                        <span>Choose the platform you like</span>
                    </li>
                    <li class="flex items-start space-x-3">
                        <span class="w-8 h-8 bg-[#00FF00] text-[#FFFFFF] rounded-full flex items-center justify-center flex-shrink-0">2</span>
                        <span>Register and activate your account</span>
                    </li>
                    <li class="flex items-start space-x-3">
                        <span class="w-8 h-8 bg-[#000080] text-[#FFFFFF] rounded-full flex items-center justify-center flex-shrink-0">3</span>
                        <span>Get your unique referral link</span>
                    </li>
                    <li class="flex items-start space-x-3">
                        <span class="w-8 h-8 bg-[#87CEEB] text-[#FFFFFF] rounded-full flex items-center justify-center flex-shrink-0">4</span>
                        <span>Submit your details using the form below</span>
                    </li>
                </ol>
            </div>
            
            <p class="text-lg text-[#808080] text-center mb-8">
                We'll create a personal duplication page for you — ready to share, easy to duplicate.
            </p>
            
            <div class="text-center">
                <button onclick="openPageRequestModal()" class="inline-flex items-center px-8 py-4 bg-[#000080] text-[#FFFFFF] font-bold rounded-xl hover:bg-blue-700 transition-colors duration-200">
                    📩 Click here to request for pages
                </button>
                <p class="text-sm text-[#808080] mt-2">Request a custom duplication page for your business</p>
            </div>
        </div>
    </div>
</section>

<!-- Final CTA Section -->
<section class="cta-gradient text-white py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl md:text-5xl font-bold mb-6">
            🚀 Ready to Duplicate and Win?
        </h2>
        <p class="text-xl mb-8 text-[#FFFFFF]">
            Don't try to reinvent the wheel. Just follow the steps, share your page, and let the system do the explaining for you.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="#who-this-is-for" class="bg-[#00FF00] text-[#FFFFFF] px-8 py-4 rounded-xl font-bold text-lg hover:bg-green-600 transition-all duration-200 transform hover:scale-105 shadow-lg">
                Get Started Now
            </a>
        </div>
        <p class="text-lg mt-6 text-[#FFFFFF]">
            👉 Get started now by choosing your platform above — and let's Duplicate and Win together!
        </p>
    </div>
</section>

<!-- Page Request Modal -->
<div id="pageRequestModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Request a Page</h3>
                <button onclick="closePageRequestModal()" class="text-gray-500 hover:text-gray-700 text-2xl font-bold">
                    ×
                </button>
            </div>
            
            <!-- Modal Form -->
            <form id="pageRequestForm" class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" id="name" name="name" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                           placeholder="Enter your full name">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                           placeholder="Enter your email address">
                </div>
                
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">WhatsApp Number (with country code) *</label>
                    <input type="tel" id="phone_number" name="phone_number" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                           placeholder="+1234567890">
                </div>
                
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                    <textarea id="message" name="message" rows="4" required 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none"
                              placeholder="Tell us about your business and what kind of duplication page you need..."></textarea>
                </div>
                
                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" id="submitBtn" 
                            class="w-full bg-[#000080] text-white py-3 px-6 rounded-lg font-bold hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center">
                        <span id="submitText">Submit Request</span>
                        <svg id="loadingSpinner" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
            
            <!-- Success/Error Messages -->
            <div id="messageContainer" class="mt-4 hidden">
                <div id="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg hidden">
                    <strong>Success!</strong> Your page request has been submitted successfully. We'll get back to you soon!
                </div>
                <div id="errorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg hidden">
                    <strong>Error!</strong> <span id="errorText"></span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('additional_js')
<script>
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

function openPageRequestModal() {
    document.getElementById('pageRequestModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closePageRequestModal() {
    document.getElementById('pageRequestModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    resetForm();
}

function resetForm() {
    document.getElementById('pageRequestForm').reset();
    document.getElementById('messageContainer').classList.add('hidden');
    document.getElementById('successMessage').classList.add('hidden');
    document.getElementById('errorMessage').classList.add('hidden');
    document.getElementById('submitBtn').disabled = false;
    document.getElementById('submitText').textContent = 'Submit Request';
    document.getElementById('loadingSpinner').classList.add('hidden');
}

function showMessage(type, message = '') {
    const container = document.getElementById('messageContainer');
    const successMsg = document.getElementById('successMessage');
    const errorMsg = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');
    
    container.classList.remove('hidden');
    
    if (type === 'success') {
        successMsg.classList.remove('hidden');
        errorMsg.classList.add('hidden');
    } else {
        errorMsg.classList.remove('hidden');
        successMsg.classList.add('hidden');
        errorText.textContent = message;
    }
}

// Handle form submission
document.getElementById('pageRequestForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingSpinner = document.getElementById('loadingSpinner');
    
    // Validate phone number format
    const phoneInput = document.getElementById('phone_number');
    const phoneValue = phoneInput.value.trim();
    
    // Check if phone number starts with + (international format)
    if (phoneValue && !phoneValue.startsWith('+')) {
        showToast('Please enter a valid WhatsApp phone number starting with + (e.g., +27732050995, +1234567890, +44123456789)', 'warning', 6000);
        phoneInput.focus();
        return;
    }
    
    // Basic validation for phone number length (minimum 10 digits after country code)
    if (phoneValue && phoneValue.length < 10) {
        showToast('Please enter a complete WhatsApp phone number with country code (minimum 10 digits)', 'warning', 5000);
        phoneInput.focus();
        return;
    }
    
    // Show loading state
    submitBtn.disabled = true;
    submitText.textContent = 'Submitting...';
    loadingSpinner.classList.remove('hidden');
    
    // Hide any previous messages
    document.getElementById('messageContainer').classList.add('hidden');
    
    // Get form data
    const formData = new FormData(this);
    const data = {
        name: formData.get('name'),
        email: formData.get('email'),
        phone_number: formData.get('phone_number'),
        message: formData.get('message')
    };
    
    // Debug: Log the form data being sent
    console.log('Form data being sent:', data);
    console.log('Phone number value:', formData.get('phone_number'));
    
    try {
        const response = await fetch('/api/page-requests/submit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (response.ok && result.success) {
            showToast('Thank you! Your page request has been submitted successfully. We\'ll contact you soon!', 'success', 8000);
            showMessage('success');
            // Reset form after successful submission
            setTimeout(() => {
                closePageRequestModal();
            }, 2000);
        } else {
            const errorMsg = result.message || 'Failed to submit request. Please try again.';
            showToast(errorMsg, 'error', 6000);
            showMessage('error', errorMsg);
        }
    } catch (error) {
        console.error('Error:', error);
        const errorMsg = 'Network error. Please check your connection and try again.';
        showToast(errorMsg, 'error', 6000);
        showMessage('error', errorMsg);
    } finally {
        // Reset button state
        submitBtn.disabled = false;
        submitText.textContent = 'Submit Request';
        loadingSpinner.classList.add('hidden');
    }
});

// Close modal when clicking outside
document.getElementById('pageRequestModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePageRequestModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePageRequestModal();
    }
});
</script>
@endsection 