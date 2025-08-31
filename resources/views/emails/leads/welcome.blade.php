@extends('emails.master')

@section('title', 'Welcome to ' . ($page_title ?? 'DuplicationHub'))

@section('header_title', 'Welcome to ' . ($page_title ?? 'DuplicationHub'))
@section('header_subtitle', $page_headline ?? 'Your Journey Begins Here')

@section('content')
<div class="content-section">
    <h2 class="content-title">🎉 You're All Set!</h2>
    
    <p class="content-text">
        Hi {{ $name }}, thank you for joining <strong>{{ $page_title }}</strong>! We're excited to have you on board.
    </p>
    
    @if($page_summary)
    <div class="info-card">
        <h3 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 18px;">📋 About {{ $page_title }}</h3>
        <p style="margin: 0; color: #555;">{{ $page_summary }}</p>
    </div>
    @endif

    <div class="btn-container">
        <a href="{{ $redirect_url }}" class="btn" target="_blank">
            {{ $cta_text ?? 'Get Started Now' }}
        </a>
        @if($cta_subtext)
        <p style="color: #666; margin-top: 15px; font-size: 14px; text-align: center;">{{ $cta_subtext }}</p>
        @endif
    </div>
    
    <div class="success-card">
        <h3 style="color: #155724; margin: 0 0 15px 0; font-size: 18px;">🚀 Your Referral Link</h3>
        <p style="margin: 0 0 20px 0; color: #155724;">
            Share your personal referral link with friends and earn rewards! Every person who signs up through your link helps you grow.
        </p>
        
        <div class="info-card" style="background-color: #ffffff; border: 2px dashed #28a745;">
            <p style="margin: 0; color: #333; font-weight: bold; margin-bottom: 10px; text-align: center;">Your Personal Link:</p>
            <a href="{{ $my_link }}" style="color: #667eea; text-decoration: none; word-break: break-all; font-family: monospace; font-size: 14px;">{{ $my_link }}</a>
        </div>
        
        <p style="color: #155724; font-size: 14px; margin-top: 15px; text-align: center;">
            Copy and share this link on social media, email, or any platform you prefer!
        </p>
    </div>
    
    <div class="warning-card">
        <h4 style="color: #856404; margin: 0 0 15px 0; font-size: 16px;">💡 Pro Tips:</h4>
        <ul style="margin: 0; padding-left: 20px; color: #856404;">
            <li style="margin: 8px 0;">Share your link on social media platforms</li>
            <li style="margin: 8px 0;">Include it in your email signature</li>
            <li style="margin: 8px 0;">Mention it in conversations with friends</li>
            <li style="margin: 8px 0;">Post about it in relevant online communities</li>
        </ul>
    </div>
    
    <p class="content-text">
        Need help getting started? Our support team is here to assist you every step of the way.
    </p>
    
    <div class="btn-container">
        <a href="mailto:info@duplicationhub.ac.ke" class="btn btn-secondary">
            Contact Support
        </a>
    </div>
</div>
@endsection 