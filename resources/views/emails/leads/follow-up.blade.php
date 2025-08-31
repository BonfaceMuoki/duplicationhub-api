@extends('emails.master')

@section('title', 'Update from ' . ($page_title ?? 'DuplicationHub'))

@section('header_title', 'Update from ' . ($page_title ?? 'DuplicationHub'))
@section('header_subtitle', 'Stay in the Loop')

@section('content')
<div class="content-section">
    <h2 class="content-title">📢 Important Update</h2>
    
    <p class="content-text">
        Hi {{ $name }}, we have an update for you! We wanted to keep you informed about the latest developments with <strong>{{ $page_title }}</strong>.
    </p>
    
    @if(isset($message))
    <div class="info-card">
        <h3 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 18px;">📋 Latest News</h3>
        <p style="margin: 0; color: #555;">{{ $message }}</p>
    </div>
    @endif

    <div class="btn-container">
        <a href="{{ url('/' . $page_title) }}" class="btn" target="_blank">
            Learn More
        </a>
    </div>
    
    <div class="success-card">
        <h3 style="color: #155724; margin: 0 0 15px 0; font-size: 18px;">🚀 Keep Sharing Your Link</h3>
        <p style="margin: 0 0 20px 0; color: #155724;">
            Don't forget to continue sharing your referral link with friends and family. Every referral helps you grow!
        </p>
        
        <div class="info-card" style="background-color: #ffffff; border: 2px dashed #28a745;">
            <p style="margin: 0; color: #333; font-weight: bold; margin-bottom: 10px; text-align: center;">Your Referral Link:</p>
            <a href="{{ url('/' . $page_title . '?ref=' . $lead_id) }}" style="color: #667eea; text-decoration: none; word-break: break-all; font-family: monospace; font-size: 14px;">
                {{ url('/' . $page_title . '?ref=' . $lead_id) }}
            </a>
        </div>
        
        <p style="color: #155724; font-size: 14px; margin-top: 15px; text-align: center;">
            Copy and share this link to continue growing your network!
        </p>
    </div>
    
    <div class="warning-card">
        <h4 style="color: #856404; margin: 0 0 15px 0; font-size: 16px;">💡 Sharing Tips:</h4>
        <ul style="margin: 0; padding-left: 20px; color: #856404;">
            <li style="margin: 8px 0;">Post updates on your social media</li>
            <li style="margin: 8px 0;">Send personalized messages to friends</li>
            <li style="margin: 8px 0;">Join relevant online communities</li>
            <li style="margin: 8px 0;">Create engaging content around your link</li>
        </ul>
    </div>
    
    <p class="content-text">
        Questions about this update? Our support team is here to help!
    </p>
    
    <div class="btn-container">
        <a href="mailto:info@duplicationhub.ac.ke" class="btn btn-secondary">
            Contact Support
        </a>
    </div>
</div>
@endsection 