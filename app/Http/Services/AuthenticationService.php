<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Spatie\Permission\Models\Role;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;

class AuthenticationService
{
    public function createAdmin(Request $request)
    {
        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'account_status' => 'ACTIVE',
        ]);

        $user->assignRole('super admin');

        return $user;
    }

    public function registerUser(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $user = User::create([
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'account_status' => 'ACTIVE'
            ]);

            $user->assignRole(Role::findByName('Normal User'));  

            return [
                'user' => $user
            ];
        });
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
    
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed. Invalid credentials.'
            ], 401);
        }
    
        $user = auth()->user();
        
        // Generate refresh token with long TTL (14 days)
        JWTAuth::factory()->setTTL((int) env('JWT_REFRESH_TTL', 20160));
        $refreshToken = JWTAuth::fromUser($user);
        
        // Set TTL for access token to 5 seconds
        JWTAuth::factory()->setTTL(5);
        
        $permissions = $user->getAllPermissions()
            ->merge($user->getPermissionsViaRoles())
            ->pluck('name')
            ->unique();
    
        $role = $user->roles->isNotEmpty() ? $user->roles[0]->name : '';
    
        // Create secure HttpOnly cookies for both access and refresh tokens
        $accessCookieName = env('COOKIE_NAME', 'duplication_auth_token');
        $refreshCookieName = env('REFRESH_COOKIE_NAME', 'duplication_refresh_token');

        $accessCookie = cookie(
            name: $accessCookieName,
            value: $token,
            minutes: 5, // 5 seconds
            path: '/',
            domain: null,
            secure: true,     // true in production
            httpOnly: true,
            sameSite: 'Lax'
        );
        
        $refreshCookie = cookie(
            name: $refreshCookieName,
            value: $refreshToken,
            minutes: (int) env('JWT_REFRESH_TTL', 20160), // 14 days default
            path: '/',
            domain: null,
            secure: true,
            httpOnly: true,
            sameSite: 'Lax'
        );
    
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'refresh_token' => $refreshToken, // Return refresh token for frontend storage
            'role' => $role,
            'permissions' => $permissions,
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'middle_name' => $user->middle_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
            ],
            'token_expires_in' => 5, // 5 seconds
            'refresh_token_expires_in' => (int) env('JWT_REFRESH_TTL', 20160) * 60, // Convert to seconds
        ])->withCookie($accessCookie)->withCookie($refreshCookie);
    }
    
    /**
     * Expire only the access token (keep refresh token active)
     */
    public function expireAccessToken(Request $request)
    {
        try {
            // Get access token from cookie
            $accessCookieName = env('COOKIE_NAME', 'duplication_auth_token');
            $accessToken = $request->cookie($accessCookieName);
            
            if (!$accessToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access token not found'
                ], 401);
            }
            
            // Blacklist the access token
            try {
                JWTAuth::setToken($accessToken);
                JWTAuth::invalidate();
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid access token'
                ], 401);
            }
            
            // Create expired cookie to clear the access token
            $expiredAccessCookie = cookie(
                name: $accessCookieName,
                value: '',
                minutes: -1, // Expired
                path: '/',
                domain: null,
                secure: true,
                httpOnly: true,
                sameSite: 'Lax'
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Access token expired successfully. Refresh token remains active.'
            ])->withCookie($expiredAccessCookie);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to expire access token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout user and expire all tokens
     */
    public function logout(Request $request)
    {
        try {
            // Get tokens from cookies
            $accessCookieName = env('COOKIE_NAME', 'duplication_auth_token');
            $refreshCookieName = env('REFRESH_COOKIE_NAME', 'duplication_refresh_token');
            
            $accessToken = $request->cookie($accessCookieName);
            $refreshToken = $request->cookie($refreshCookieName);
            
            // Blacklist both tokens if they exist
            if ($accessToken) {
                try {
                    JWTAuth::setToken($accessToken);
                    JWTAuth::invalidate();
                } catch (\Exception $e) {
                    // Token might already be invalid, continue
                }
            }
            
            if ($refreshToken) {
                try {
                    JWTAuth::setToken($refreshToken);
                    JWTAuth::invalidate();
                } catch (\Exception $e) {
                    // Token might already be invalid, continue
                }
            }
            
            // Create expired cookies to clear them
            $expiredAccessCookie = cookie(
                name: $accessCookieName,
                value: '',
                minutes: -1, // Expired
                path: '/',
                domain: null,
                secure: true,
                httpOnly: true,
                sameSite: 'Lax'
            );
            
            $expiredRefreshCookie = cookie(
                name: $refreshCookieName,
                value: '',
                minutes: -1, // Expired
                path: '/',
                domain: null,
                secure: true,
                httpOnly: true,
                sameSite: 'Lax'
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out. All tokens expired.'
            ])->withCookie($expiredAccessCookie)->withCookie($expiredRefreshCookie);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh the access token using a refresh token
     */
    public function refresh(Request $request)
    {
        try {
            // Get refresh token from cookie
            $refreshCookieName = env('REFRESH_COOKIE_NAME', 'duplication_refresh_token');
            $refreshToken = $request->cookie($refreshCookieName);
    
            
            if (!$refreshToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Refresh token not found'
                ], 401);
            }
            
            // Parse the refresh token to get user information
            try {
                // JWT tokens are NOT encrypted, just base64 encoded
                $tokenParts = explode('.', $refreshToken);
                if (count($tokenParts) !== 3) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid token format - expected 3 parts'
                    ], 401);
                }
                
                // Decode the payload (second part) - this is just base64, not encryption
                $payloadJson = base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1]));
                $payload = json_decode($payloadJson, true);
                
                if (!$payload || !isset($payload['sub'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid token payload - missing user ID'
                    ], 401);
                }
                
                // Get user ID from token payload
                $userId = $payload['sub'];
                
                // Verify token hasn't expired
                if (isset($payload['exp']) && $payload['exp'] < time()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Refresh token has expired'
                    ], 401);
                }
                
                // Get user from database
                $user = User::find($userId);
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User not found in database'
                    ], 401);
                }
                
                // Log token parsing info for debugging
                // \Log::info('JWT Token parsed successfully', [
                //     'user_id' => $userId,
                //     'token_exp' => $payload['exp'] ?? 'not_set',
                //     'current_time' => time(),
                //     'token_iat' => $payload['iat'] ?? 'not_set',
                //     'token_iss' => $payload['iss'] ?? 'not_set',
                //     'raw_payload' => $payloadJson,
                //     'decoded_payload' => $payload
                // ]);
                
            } catch (\Exception $e) {
                \Log::error('JWT Token parsing failed', [
                    'error' => $e->getMessage(),
                    'token_length' => strlen($refreshToken),
                    'token_preview' => substr($refreshToken, 0, 50) . '...',
                    'token_parts_count' => count(explode('.', $refreshToken))
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to parse refresh token: ' . $e->getMessage()
                ], 401);
            }
            
            // Generate new access token with 5 seconds TTL
            JWTAuth::factory()->setTTL(5);
            $newToken = JWTAuth::fromUser($user);
            
            // Generate new refresh token with long TTL (14 days)
            JWTAuth::factory()->setTTL((int) env('JWT_REFRESH_TTL', 20160));
            $newRefreshToken = JWTAuth::fromUser($user);
            
            $permissions = $user->getAllPermissions()
                ->merge($user->getPermissionsViaRoles())
                ->pluck('name')
                ->unique();
            
            $role = $user->roles->isNotEmpty() ? $user->roles[0]->name : '';
            
            // Create new secure HttpOnly cookies
            $accessCookieName = env('COOKIE_NAME', 'duplication_auth_token');
            $refreshCookieName = env('REFRESH_COOKIE_NAME', 'duplication_refresh_token');

            $accessCookie = cookie(
                name: $accessCookieName,
                value: $newToken,
                minutes: 5, // 5 seconds
                path: '/',
                domain: null,
                secure: true,
                httpOnly: true,
                sameSite: 'Lax'
            );
            
            $refreshCookie = cookie(
                name: $refreshCookieName,
                value: $newRefreshToken,
                minutes: (int) env('JWT_REFRESH_TTL', 20160),
                path: '/',
                domain: null,
                secure: true,
                httpOnly: true,
                sameSite: 'Lax'
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'refresh_token' => $newRefreshToken, // Return new refresh token for frontend storage
                'role' => $role,
                'permissions' => $permissions,
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'middle_name' => $user->middle_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                ],
                'token_expires_in' => 5, // 5 seconds
                'refresh_token_expires_in' => (int) env('JWT_REFRESH_TTL', 20160) * 60,
            ])->withCookie($accessCookie)->withCookie($refreshCookie);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token refresh failed: ' . $e->getMessage()
            ], 401);
        }
    }

    public function testPushNotifications(array $tokens)
    {
        $messaging = app('firebase.messaging');

        $notification = Notification::create("Hello from Laravel", "This is a test notification");
        $cloudMessage = CloudMessage::new()
            ->withNotification($notification)
            ->withData(['custom_key' => 'custom_value']);

        return $messaging->sendMulticast($cloudMessage, $tokens);
    }

    /**
     * Send password reset email
     */
    public function forgotPassword(string $email, string $frontendUrl)
    {
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            throw new \Exception('User not found');
        }

        // Generate password reset token
        $token = \Str::random(64);
        
        // Store token in password_resets table (Laravel's built-in table)
        \DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            [
                'email' => $email,
                'token' => $token,
                'created_at' => now()
            ]
        );

        // Build reset URL
        $resetUrl = rtrim($frontendUrl, '/') . '/reset-password?token=' . $token . '&email=' . urlencode($email);

        // Send email using Laravel's built-in mail system
        \Mail::send('emails.password-reset', [
            'user' => $user,
            'resetUrl' => $resetUrl,
            'token' => $token
        ], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Password Reset Request - DuplicationHub');
        });

        return true;
    }

    /**
     * Reset password using token
     */
    public function resetPassword(string $email, string $token, string $password)
    {
        // Find the password reset record
        $resetRecord = \DB::table('password_resets')
            ->where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$resetRecord) {
            throw new \Exception('Invalid or expired reset token');
        }

        // Check if token is expired (24 hours)
        if (now()->diffInHours($resetRecord->created_at) > 24) {
            // Clean up expired token
            \DB::table('password_resets')->where('email', $email)->delete();
            throw new \Exception('Reset token has expired');
        }

        // Update user password
        $user = User::where('email', $email)->first();
        if (!$user) {
            throw new \Exception('User not found');
        }

        $user->update([
            'password' => Hash::make($password)
        ]);

        // Clean up used token
        \DB::table('password_resets')->where('email', $email)->delete();

        return true;
    }
}
