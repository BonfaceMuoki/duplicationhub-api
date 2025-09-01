<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Page;
use App\Models\Lead;
use App\Models\PageInvite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_user_gets_password_reset_link_in_welcome_email()
    {
        // Mock the mail facade
        Mail::fake();

        // Create a test page
        $page = Page::factory()->create([
            'title' => 'Test Page',
            'headline' => 'Test Headline',
            'summary' => 'Test Summary'
        ]);

        // Create a referrer invite
        $referrerInvite = PageInvite::create([
            'page_id' => $page->id,
            'user_id' => $page->user_id,
            'handle' => 'test-ref',
            'clicks' => 0,
            'leads_count' => 0,
            'is_active' => true,
        ]);

        // Test lead submission data
        $leadData = [
            'page_id' => $page->id,
            'ref' => 'test-ref',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'whatsapp_number' => '+1234567890',
        ];

        // Submit lead
        $response = $this->postJson('/api/leads/submit', $leadData);

        // Assert response is successful
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Check that a password reset token was created
        $resetRecord = DB::table('password_resets')
            ->where('email', 'john.doe@example.com')
            ->first();

        $this->assertNotNull($resetRecord);
        $this->assertEquals('john.doe@example.com', $resetRecord->email);
        $this->assertNotNull($resetRecord->token);

        // Check that the user was created
        $user = User::where('email', 'john.doe@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('John', $user->first_name);
        $this->assertEquals('Doe', $user->last_name);
    }

    public function test_existing_user_does_not_get_password_reset_link()
    {
        // Mock the mail facade
        Mail::fake();

        // Create an existing user
        $existingUser = User::factory()->create([
            'email' => 'existing@example.com',
            'first_name' => 'Existing',
            'last_name' => 'User'
        ]);

        // Create a test page
        $page = Page::factory()->create([
            'title' => 'Test Page',
            'headline' => 'Test Headline',
            'summary' => 'Test Summary'
        ]);

        // Create a referrer invite
        $referrerInvite = PageInvite::create([
            'page_id' => $page->id,
            'user_id' => $page->user_id,
            'handle' => 'test-ref',
            'clicks' => 0,
            'leads_count' => 0,
            'is_active' => true,
        ]);

        // Test lead submission data
        $leadData = [
            'page_id' => $page->id,
            'ref' => 'test-ref',
            'first_name' => 'Existing',
            'last_name' => 'User',
            'email' => 'existing@example.com',
            'whatsapp_number' => '+1234567890',
        ];

        // Submit lead
        $response = $this->postJson('/api/leads/submit', $leadData);

        // Assert response is successful
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Check that no new password reset token was created
        $resetRecord = DB::table('password_resets')
            ->where('email', 'existing@example.com')
            ->first();

        // Should not have a new reset token since user already exists
        $this->assertNull($resetRecord);
    }
} 