@extends('emails.master')

@section('title', 'Page Created Successfully - DuplicationHub')

@section('header_title', 'Page Created Successfully! 🎉')
@section('header_subtitle', 'Your new page is ready to go live')

@section('content')
<div class="content-section">
    <h2 class="content-title">Congratulations {{ $user->first_name }}! 🚀</h2>
    
    <p class="content-text">
        Your new page <strong>"{{ $page->title }}"</strong> has been created successfully and is now ready for you to manage and share with your audience.
    </p>
    
    <div class="info-card">
        <h3 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 18px;">📋 Page Details</h3>
        <p style="margin: 8px 0; color: #555;">
            <strong>Title:</strong> {{ $page->title }}
        </p>
        <p style="margin: 8px 0; color: #555;">
            <strong>Slug:</strong> {{ $page->slug }}
        </p>
        <p style="margin: 8px 0; color: #555;">
            <strong>Status:</strong> <span style="color: #667eea; font-weight: 600;">{{ ucfirst($page->status) }}</span>
        </p>
        <p style="margin: 8px 0; color: #555;">
            <strong>Privacy:</strong> <span style="color: {{ $page->is_public ? '#28a745' : '#dc3545' }}; font-weight: 600;">{{ $page->is_public ? 'Public' : 'Private' }}</span>
        </p>
        @if($page->headline)
        <p style="margin: 8px 0; color: #555;">
            <strong>Headline:</strong> {{ $page->headline }}
        </p>
        @endif
    </div>
    
    <div class="btn-container">
        <a href="{{ url('/admin/pages/' . $page->id . '/edit') }}" class="btn" target="_blank">
            Edit Page
        </a>
    </div>
    
    <div class="success-card">
        <h3 style="color: #155724; margin: 0 0 15px 0; font-size: 18px;">✅ What's Next?</h3>
        <ol style="margin: 0; padding-left: 20px; color: #155724;">
            <li style="margin: 8px 0;">Review and edit your page content</li>
            <li style="margin: 8px 0;">Upload or update your page image</li>
            <li style="margin: 8px 0;">Set your call-to-action text and links</li>
            <li style="margin: 8px 0;">Configure your page settings</li>
            <li style="margin: 8px 0;">Publish your page when ready</li>
        </ol>
    </div>
    
    @if($page->status === 'draft')
    <div class="warning-card">
        <h4 style="color: #856404; margin: 0 0 15px 0; font-size: 16px;">📝 Current Status: Draft</h4>
        <p style="margin: 0; color: #856404;">
            Your page is currently in draft mode and won't be visible to the public. 
            Once you're satisfied with the content, you can publish it to make it live.
        </p>
    </div>
    @endif
    
    @if(!$page->is_public)
    <div class="warning-card">
        <h4 style="color: #856404; margin: 0 0 15px 0; font-size: 16px;">🔒 Privacy Setting: Private</h4>
        <p style="margin: 0; color: #856404;">
            Your page is currently set to private. Only invited users can access it. 
            You can make it public later if you want it to be discoverable by everyone.
        </p>
    </div>
    @endif
    
    <div class="info-card">
        <h3 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 18px;">🔗 Quick Actions</h3>
        <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
            <a href="{{ url('/admin/pages/' . $page->id . '/invites') }}" class="btn btn-secondary" style="padding: 10px 20px; font-size: 14px;">
                Manage Invites
            </a>
            <a href="{{ url('/admin/pages/' . $page->id . '/analytics') }}" class="btn btn-secondary" style="padding: 10px 20px; font-size: 14px;">
                View Analytics
            </a>
            <a href="{{ url('/admin/pages/' . $page->id . '/leads') }}" class="btn btn-secondary" style="padding: 10px 20px; font-size: 14px;">
                View Leads
            </a>
        </div>
    </div>
    
    <p class="content-text">
        Need help getting your page set up? Our support team is here to assist you with any questions or technical issues.
    </p>
    
    <div class="btn-container">
        <a href="mailto:info@duplicationhub.ac.ke" class="btn btn-secondary">
            Get Support
        </a>
    </div>
</div>
@endsection 