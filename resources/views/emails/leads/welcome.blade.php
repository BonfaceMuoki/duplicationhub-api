@extends('emails.master')

@section('title', 'Welcome to ' . ($page_title ?? 'DuplicationHub'))

@section('header_title', 'Welcome to ' . ($page_title ?? 'DuplicationHub'))
@section('header_subtitle', $page_headline ?? 'Your Journey Begins Here')

@section('content')
<div class="content-section">
    <h2 class="content-title">🎉 Thank You for Your Interest!</h2>
    
    <p class="content-text">
        Hi {{ $name }}, thank you for your interest in <strong>{{ $page_title }}</strong>! We're excited to have you on board.
    </p>
    
    @if($page_summary)
    <div class="info-card">
        <h3 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 18px;">📋 About {{ $page_title }}</h3>
        <p style="margin: 0; color: #555;">{{ $page_summary }}</p>
    </div>
    @endif

    <div class="success-card">
        <h3 style="color: #155724; margin: 0 0 15px 0; font-size: 18px;">✅ Your Interest Has Been Logged</h3>
        <p style="margin: 0 0 20px 0; color: #155724;">
            We have successfully received your interest and someone from our team will get back to you soon through the WhatsApp number you provided.
        </p>
        
        @if($whatsapp_number)
        <div class="info-card" style="background-color: #ffffff; border: 2px dashed #28a745;">
            <p style="margin: 0; color: #333; font-weight: bold; margin-bottom: 10px; text-align: center;">We'll contact you at:</p>
            <p style="margin: 0; color: #667eea; text-align: center; font-family: monospace; font-size: 14px;">{{ $whatsapp_number }}</p>
        </div>
        @endif
    </div>
    
    @if($is_new_user)
    <div class="warning-card">
        <h4 style="color: #856404; margin: 0 0 15px 0; font-size: 16px;">🔐 Your Account Details</h4>
        <p style="margin: 0 0 15px 0; color: #856404;">
            Since this is your first time with us, we've created an account for you. You can use your email address to log in.
        </p>
        <p style="margin: 0 0 15px 0; color: #856404; font-size: 14px;">
            <strong>Email:</strong> {{ $email ?? 'Your email address' }}
        </p>
        
        @if(isset($reset_url))
        <div class="info-card" style="background-color: #ffffff; border: 2px solid #667eea; margin-top: 15px;">
            <h5 style="color: #667eea; margin: 0 0 10px 0; font-size: 16px;">🔑 Set Your Password</h5>
            <p style="margin: 0 0 15px 0; color: #555; font-size: 14px;">
                Click the button below to set a secure password for your account. This link will expire in 24 hours.
            </p>
            <div style="text-align: center;">
                <a href="{{ $reset_url }}" class="btn" style="background-color: #667eea; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: bold;">
                    Set My Password
                </a>
            </div>
            <p style="margin: 15px 0 0 0; color: #856404; font-size: 12px; text-align: center;">
                If the button doesn't work, copy and paste this link: <br>
                <span style="color: #667eea; word-break: break-all; font-family: monospace;">{{ $reset_url }}</span>
            </p>
        </div>
        @endif
    </div>
    @endif
    
    <p class="content-text">
        We look forward to helping you achieve your goals! If you have any questions, feel free to reach out to our support team.
    </p>
    
    <div class="btn-container">
        <a href="mailto:info@duplicationhub.ac.ke" class="btn btn-secondary">
            Contact Support
        </a>
    </div>
</div>
@endsection 