<?php

namespace App\Http\Controllers;

use App\Http\Services\AuthenticationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class AuthenticationController extends Controller
{
    protected AuthenticationService $authservice;

    public function __construct(AuthenticationService $authservice)
    {
        $this->auth = $authservice;
    }

    public function assignUserAdmin(Request $request)
    {
        $user = $this->auth->createAdmin($request);

        return response()->json([
            'message' => 'admin created successfully',
            'user' => $user
        ], 201);
    }

    public function registerUser(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'middle_name' => 'required|string',
            'last_name' => 'required|string',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'phone_number' => 'nullable|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        try {
            $result = $this->auth->registerUser($request);
            return response()->json([
                'message' => 'User registered successfully',
                'data' => $result
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $result = $this->auth->login($request);

       return $result;
    }
    
    public function refresh(Request $request)
    {
        $result = $this->auth->refresh($request);
        return $result;
    }
    
    public function verifyToken(){
        return response()->json(['message' => 'Token verified']);
    }

    /**
     * Verify if the authenticated user is an admin
     */
    public function verifyAdmin()
    {
        try {
            $user = auth()->user();
            
            // Since this endpoint is protected by HasRoleMiddleware with 'super admin' role,
            // we know the user has admin role
            $roles = $user->getRoleNames();
            $permissions = $user->getAllPermissions()->pluck('name');

            return response()->json([
                'success' => true,
                'message' => 'Admin verification completed',
                'is_admin' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->full_name,
                    'email' => $user->email,
                    'account_status' => $user->account_status,
                    'roles' => $roles,
                    'permissions' => $permissions
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Admin verification failed',
                'error' => $e->getMessage(),
                'is_admin' => false
            ], 500);
        }
    }

    /**
     * Verify the authenticated user's role and permissions
     */
    public function verifyRole()
    {
        try {
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                    'has_role' => false
                ], 401);
            }

            // Get user's roles and permissions
            $roles = $user->getRoleNames();
            $permissions = $user->getAllPermissions()->pluck('name');
            $isAdmin = $user->hasRole('super admin');

            return response()->json([
                'success' => true,
                'message' => 'Role verification completed',
                'has_role' => true,
                'is_admin' => $isAdmin,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->full_name,
                    'email' => $user->email,
                    'account_status' => $user->account_status,
                    'roles' => $roles,
                    'permissions' => $permissions
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Role verification failed',
                'error' => $e->getMessage(),
                'has_role' => false
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $result = $this->auth->logout($request);
        return $result;
    }

    /**
     * Expire only the access token
     */
    public function expireAccessToken(Request $request): JsonResponse
    {
        $result = $this->auth->expireAccessToken($request);
        return $result;
    }

    /**
     * Refresh the access token
     */

    public function health()
    {
        return response()->json(['message' => "running"]);
    }

    public function testPushNotifications(Request $request)
    {
        $tokens = $request->input('tokens', []);

        try {
            $report = $this->auth->testPushNotifications($tokens);
            return response()->json([
                'successCount' => $report->successes()->count(),
                'failureCount' => $report->failures()->count(),
                'failures' => array_map(
                    fn($failure) => $failure->error()->getMessage(),
                    $report->failures()
                ),
            ]);
        } catch (\Throwable $e) {
            Log::error('Push notification failed: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Send password reset email
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'frontend_url' => 'required|url'
        ]);

        try {
            $result = $this->auth->forgotPassword($request->email, $request->frontend_url);
            
            return response()->json([
                'success' => true,
                'message' => 'Password reset email sent successfully',
                'data' => [
                    'email' => $request->email,
                    'sent_at' => now()->toISOString()
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Password reset email failed', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send password reset email',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset password using token
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed'
        ]);

        try {
            $result = $this->auth->resetPassword(
                $request->email,
                $request->token,
                $request->password
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully',
                'data' => [
                    'email' => $request->email,
                    'reset_at' => now()->toISOString()
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Password reset failed', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
