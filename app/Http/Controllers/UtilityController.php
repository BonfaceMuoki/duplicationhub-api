<?php

namespace App\Http\Controllers;

use App\Http\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class UtilityController extends Controller
{
    protected $fileUploadService;
    
    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Test email sending functionality
     */
    public function testEmail(Request $request)
    {
        try {
            $request->validate([
                'to' => 'required|email',
                'subject' => 'string|max:255',
                'message' => 'string|max:1000',
            ]);

            $to = $request->input('to');
            $subject = $request->input('subject', 'Test Email from DuplicationHub API');
            $message = $request->input('message', 'This is a test email to verify that the email system is working correctly.');

            // Send the beautiful test email template
            Mail::send('emails.test-email', [
                'to' => $to,
                'subject' => $subject,
                'message' => $message
            ], function ($message) use ($to, $subject) {
                $message->to($to)
                        ->subject($subject);
            });

            Log::info('Test email sent successfully', [
                'to' => $to,
                'subject' => $subject,
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully',
                'data' => [
                    'to' => $to,
                    'subject' => $subject,
                    'sent_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Test email failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
