@extends('emails.master')

@section('content')
<div style="max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="color: #333; margin-bottom: 10px;">Welcome to {{ $page_title }}!</h1>
        <p style="color: #666; font-size: 18px;">{{ $page_headline }}</p>
    </div>

    <div style="background: #f8f9fa; padding: 25px; border-radius: 10px; margin-bottom: 25px;">
        <h2 style="color: #333; margin-bottom: 15px;">🎉 You're All Set!</h2>
        <p style="color: #555; line-height: 1.6; margin-bottom: 20px;">
            Hi {{ $name }}, thank you for joining {{ $page_title }}! We're excited to have you on board.
        </p>
        
        @if($page_summary)
        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h3 style="color: #333; margin-bottom: 10px;">About {{ $page_title }}</h3>
            <p style="color: #555; line-height: 1.6;">{{ $page_summary }}</p>
        </div>
        @endif

        <div style="text-align: center; margin: 25px 0;">
            <a href="{{ $redirect_url }}" style="background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; display: inline-block; font-weight: bold; font-size: 16px;">
                {{ $cta_text ?? 'Get Started Now' }}
            </a>
            @if($cta_subtext)
            <p style="color: #666; margin-top: 10px; font-size: 14px;">{{ $cta_subtext }}</p>
            @endif
        </div>
    </div>

    <div style="background: #e8f5e8; padding: 25px; border-radius: 10px; margin-bottom: 25px;">
        <h2 style="color: #2d5a2d; margin-bottom: 15px;">🚀 Your Referral Link</h2>
        <p style="color: #2d5a2d; line-height: 1.6; margin-bottom: 20px;">
            Share your personal referral link with friends and earn rewards! Every person who signs up through your link helps you grow.
        </p>
        
        <div style="background: white; padding: 20px; border-radius: 8px; text-align: center;">
            <p style="color: #333; font-weight: bold; margin-bottom: 10px;">Your Personal Link:</p>
            <a href="{{ $my_link }}" style="color: #007bff; text-decoration: none; word-break: break-all;">{{ $my_link }}</a>
        </div>
        
        <p style="color: #2d5a2d; font-size: 14px; margin-top: 15px; text-align: center;">
            Copy and share this link on social media, email, or any platform you prefer!
        </p>
    </div>

    <div style="background: #fff3cd; padding: 20px; border-radius: 10px; margin-bottom: 25px;">
        <h3 style="color: #856404; margin-bottom: 15px;">💡 Pro Tips:</h3>
        <ul style="color: #856404; line-height: 1.6; padding-left: 20px;">
            <li>Share your link on social media platforms</li>
            <li>Include it in your email signature</li>
            <li>Mention it in conversations with friends</li>
            <li>Post about it in relevant online communities</li>
        </ul>
    </div>

    <div style="text-align: center; padding: 20px; border-top: 1px solid #eee;">
        <p style="color: #666; margin-bottom: 10px;">Need help? Contact our support team</p>
        <p style="color: #999; font-size: 12px;">
            This email was sent to {{ $email }}. If you have any questions, please reply to this email.
        </p>
    </div>
</div>
@endsection 