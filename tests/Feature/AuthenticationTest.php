<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_get_refresh_token()
    {
        // Create a test user
        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'account_status' => 'active',
        ]);

        // Test login
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Login successful',
                ])
                ->assertCookie('duplication_auth_token')
                ->assertCookie('duplication_refresh_token');

        // Verify response structure
        $response->assertJsonStructure([
            'success',
            'message',
            'role',
            'permissions',
            'user' => [
                'id',
                'first_name',
                'last_name',
                'email',
            ],
            'token_expires_in',
            'refresh_token_expires_in',
        ]);
    }

    public function test_user_can_refresh_token()
    {
        // Create a test user
        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'account_status' => 'active',
        ]);

        // Login first
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $loginResponse->assertStatus(200);

        // Get cookies from login response
        $cookies = $loginResponse->headers->getCookies();
        $refreshCookie = collect($cookies)->firstWhere('name', 'duplication_refresh_token');

        // Test refresh token
        $response = $this->withCookie('duplication_refresh_token', $refreshCookie->getValue())
                        ->postJson('/api/auth/refresh');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Token refreshed successfully',
                ])
                ->assertCookie('duplication_auth_token')
                ->assertCookie('duplication_refresh_token');
    }

    public function test_user_can_logout()
    {
        // Create a test user
        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'account_status' => 'active',
        ]);

        // Login first
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $loginResponse->assertStatus(200);

        // Get cookies from login response
        $cookies = $loginResponse->headers->getCookies();
        $accessCookie = collect($cookies)->firstWhere('name', 'duplication_auth_token');
        $refreshCookie = collect($cookies)->firstWhere('name', 'duplication_refresh_token');

        // Test logout
        $response = $this->withCookie('duplication_auth_token', $accessCookie->getValue())
                        ->withCookie('duplication_refresh_token', $refreshCookie->getValue())
                        ->postJson('/api/auth/logout');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Logged out successfully',
                ]);
    }
}
