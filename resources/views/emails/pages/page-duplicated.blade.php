@extends('emails.master')

@section('title', 'Page Duplicated - DuplicationHub')

@section('header_title', 'Page Duplicated Successfully! 🔄')
@section('header_subtitle', 'Your page has been cloned and is ready for customization')

@section('content')
<div class="content-section">
    <h2 class="content-title">Great job {{ $user->first_name }}! 🎯</h2>
    
    <p class="content-text">
        Your page <strong>"{{ $original_page->title }}"</strong> has been successfully duplicated as 
        <strong>"{{ $new_page->title }}"</strong>. The new page is now ready for you to customize and modify.
    </p>
    
    <div class="info-card">
        <h3 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 18px;">📋 New Page Details</h3>
        <p style="margin: 8px 0; color: #555;">
            <strong>New Title:</strong> {{ $new_page->title }}
        </p>
        <p style="margin: 8px 0; color: #555;">
            <strong>New Slug:</strong> {{ $new_page->slug }}
        </p>
        <p style="margin: 8px 0; color: #555;">
            <strong>Status:</strong> <span style="color: #667eea; font-weight: 600;">Draft</span>
        </p>
        <p style="margin: 8px 0; color: #555;">
            <strong>Privacy:</strong> <span style="color: #dc3545; font-weight: 600;">Private</span>
        </p>
        <p style="margin: 8px 0; color: #555;">
            <strong>Original Page:</strong> {{ $original_page->title }}
        </p>
    </div>
    
    <div class="btn-container">
        <a href="{{ url('/admin/pages/' . $new_page->id . '/edit') }}" class="btn" target="_blank">
            Edit New Page
        </a>
    </div>
    
    <div class="success-card">
        <h3 style="color: #155724; margin: 0 0 15px 0; font-size: 18px;">✅ What Was Copied?</h3>
        <ul style="margin: 0; padding-left: 20px; color: #155724;">
            <li style="margin: 8px 0;">Page content and structure</li>
            <li style="margin: 8px 0;">Images and media files</li>
            <li style="margin: 8px 0;">Call-to-action settings</li>
            <li style="margin: 8px 0;">Page configuration</li>
            <li style="margin: 8px 0;">Form fields and settings</li>
        </ul>
    </div>
    
    <div class="warning-card">
        <h4 style="color: #856404; margin: 0 0 15px 0; font-size: 16px;">⚠️ Important Notes</h4>
        <ul style="margin: 0; padding-left: 20px; color: #856404;">
            <li style="margin: 8px 0;">The new page starts with 0 views and leads</li>
            <li style="margin: 8px 0;">All invites and referrals are reset</li>
            <li style="margin: 8px 0;">The page is set to draft status by default</li>
            <li style="margin: 8px 0;">Privacy is set to private for security</li>
        </ul>
    </div>
    
    <div class="info-card">
        <h3 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 18px;">🔗 Quick Actions</h3>
        <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
            <a href="{{ url('/admin/pages/' . $new_page->id . '/edit') }}" class="btn btn-secondary" style="padding: 10px 20px; font-size: 14px;">
                Customize Page
            </a>
            <a href="{{ url('/admin/pages/' . $new_page->id . '/invites') }}" class="btn btn-secondary" style="padding: 10px 20px; font-size: 14px;">
                Set Up Invites
            </a>
            <a href="{{ url('/admin/pages/' . $original_page->id) }}" class="btn btn-secondary" style="padding: 10px 20px; font-size: 14px;">
                View Original
            </a>
        </div>
    </div>
    
    <p class="content-text">
        Need help customizing your duplicated page? Our support team is here to assist you with any questions or modifications.
    </p>
    
    <div class="btn-container">
        <a href="mailto:info@duplicationhub.ac.ke" class="btn btn-secondary">
            Get Support
        </a>
    </div>
</div>
@endsection 