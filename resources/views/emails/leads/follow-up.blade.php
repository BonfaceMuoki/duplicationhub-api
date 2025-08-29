@extends('emails.master')

@section('content')
<div style="max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="color: #333; margin-bottom: 10px;">Update from {{ $page_title }}</h1>
        <p style="color: #666; font-size: 16px;">Hi {{ $name }}, we have an update for you!</p>
    </div>

    <div style="background: #f8f9fa; padding: 25px; border-radius: 10px; margin-bottom: 25px;">
        <h2 style="color: #333; margin-bottom: 15px;">📢 Important Update</h2>
        <p style="color: #555; line-height: 1.6; margin-bottom: 20px;">
            We wanted to keep you informed about the latest developments with {{ $page_title }}.
        </p>
        
        @if(isset($message))
        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <p style="color: #333; line-height: 1.6;">{{ $message }}</p>
        </div>
        @endif

        <div style="text-align: center; margin: 25px 0;">
            <a href="{{ url('/' . $page_title) }}" style="background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; display: inline-block; font-weight: bold; font-size: 16px;">
                Learn More
            </a>
        </div>
    </div>

    <div style="background: #e8f5e8; padding: 25px; border-radius: 10px; margin-bottom: 25px;">
        <h2 style="color: #2d5a2d; margin-bottom: 15px;">🚀 Keep Sharing Your Link</h2>
        <p style="color: #2d5a2d; line-height: 1.6; margin-bottom: 20px;">
            Don't forget to continue sharing your referral link with friends and family. Every referral helps you grow!
        </p>
        
        <div style="background: white; padding: 20px; border-radius: 8px; text-align: center;">
            <p style="color: #333; font-weight: bold; margin-bottom: 10px;">Your Referral Link:</p>
            <a href="{{ url('/' . $page_title . '?ref=' . $lead_id) }}" style="color: #007bff; text-decoration: none; word-break: break-all;">
                {{ url('/' . $page_title . '?ref=' . $lead_id) }}
            </a>
        </div>
    </div>

    <div style="text-align: center; padding: 20px; border-top: 1px solid #eee;">
        <p style="color: #666; margin-bottom: 10px;">Questions? Contact our support team</p>
        <p style="color: #999; font-size: 12px;">
            This email was sent to you as a lead of {{ $page_title }}. If you have any questions, please reply to this email.
        </p>
    </div>
</div>
@endsection 