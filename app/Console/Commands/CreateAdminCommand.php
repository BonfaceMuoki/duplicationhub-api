<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {--name=} {--email=} {--password=} {--phone=} {--interactive}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating new admin user...');

        // Check if interactive mode is requested
        if ($this->option('interactive') || !$this->option('name') || !$this->option('email')) {
            return $this->createInteractive();
        }

        return $this->createFromOptions();
    }

    /**
     * Create admin interactively
     */
    private function createInteractive()
    {
        $this->info('Please provide the admin details:');

        $firstName = $this->ask('First Name');
        $middleName = $this->ask('Middle Name (optional)', '');
        $lastName = $this->ask('Last Name');
        $email = $this->ask('Email');
        $phoneNumber = $this->ask('Phone Number (optional)', '');
        $dateOfBirth = $this->ask('Date of Birth (YYYY-MM-DD) (optional)', '1990-01-01');
        $gender = $this->choice('Gender', ['Male', 'Female', 'Other'], 'Other');
        $password = $this->secret('Password');
        $passwordConfirm = $this->secret('Confirm Password');

        // Validate password confirmation
        if ($password !== $passwordConfirm) {
            $this->error('Passwords do not match!');
            return 1;
        }

        // Validate input
        $validator = Validator::make([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => $password,
            'phone_number' => $phoneNumber,
            'date_of_birth' => $dateOfBirth,
            'gender' => $gender,
        ], [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone_number' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'required|in:Male,Female,Other',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        return $this->createUser([
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'email' => $email,
            'phone_number' => $phoneNumber,
            'date_of_birth' => $dateOfBirth,
            'gender' => $gender,
            'password' => $password,
        ]);
    }

    /**
     * Create admin from command options
     */
    private function createFromOptions()
    {
        $name = $this->option('name');
        $email = $this->option('email');
        $password = $this->option('password');
        $phone = $this->option('phone');

        // Validate required options
        if (!$name || !$email || !$password) {
            $this->error('Missing required options. Use --name, --email, and --password or use --interactive mode.');
            return 1;
        }

        // Parse name (assuming format: "First Middle Last" or "First Last")
        $nameParts = explode(' ', trim($name));
        $firstName = $nameParts[0];
        $lastName = end($nameParts);
        $middleName = count($nameParts) > 2 ? implode(' ', array_slice($nameParts, 1, -1)) : '';

        // Validate input
        $validator = Validator::make([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => $password,
            'phone_number' => $phone,
        ], [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone_number' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        return $this->createUser([
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'email' => $email,
            'phone_number' => $phone,
            'date_of_birth' => '1990-01-01', // Default date for admin users
            'gender' => 'Other', // Default gender for admin users
            'password' => $password,
        ]);
    }

    /**
     * Create the user and assign admin role
     */
    private function createUser(array $data)
    {
        try {
            $user = User::create([
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone_number' => $data['phone_number'],
                'date_of_birth' => $data['date_of_birth'],
                'gender' => $data['gender'],
                'password' => Hash::make($data['password']),
                'account_status' => 'ACTIVE',
            ]);

            $user->assignRole('super admin');

            $this->info('✅ Admin user created successfully!');
            $this->table(
                ['Field', 'Value'],
                [
                    ['ID', $user->id],
                    ['Name', $user->full_name],
                    ['Email', $user->email],
                    ['Phone', $user->phone_number ?: 'N/A'],
                    ['Date of Birth', $user->date_of_birth],
                    ['Gender', $user->gender],
                    ['Role', 'super admin'],
                    ['Status', $user->account_status],
                    ['Password', $data['password']],
                ]
            );

            // Test login functionality
            $this->info('🔐 Testing login functionality...');
            if ($this->testLogin($user->email, $data['password'])) {
                $this->info('✅ Login test successful! Admin user can authenticate properly.');
            } else {
                $this->warn('⚠️  Login test failed. Please check the credentials.');
            }

            return 0;

        } catch (\Exception $e) {
            $this->error('Failed to create admin user: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Test login functionality for the created user
     */
    private function testLogin(string $email, string $password): bool
    {
        try {
            // Attempt to authenticate the user using JWT
            $credentials = [
                'email' => $email,
                'password' => $password
            ];

            if ($token = JWTAuth::attempt($credentials)) {
                $user = JWTAuth::user();
                
                // Check if user has the correct role
                if ($user->hasRole('super admin')) {
                    $this->info("   - User authenticated successfully with JWT");
                    $this->info("   - JWT Token generated: " . substr($token, 0, 20) . "...");
                    $this->info("   - Role verification: super admin ✓");
                    $this->info("   - User ID: {$user->id}");
                    $this->info("   - Account Status: {$user->account_status}");
                    
                    // Invalidate the token after test (only if token exists)
                    if ($token) {
                        try {
                            JWTAuth::invalidate($token);
                            $this->info("   - JWT Token invalidated successfully");
                        } catch (\Exception $e) {
                            $this->warn("   - Token invalidation warning: " . $e->getMessage());
                        }
                    }
                    return true;
                } else {
                    $this->warn("   - User authenticated but missing 'super admin' role");
                    $this->warn("   - Current roles: " . implode(', ', $user->getRoleNames()->toArray()));
                    
                    // Invalidate the token after test (only if token exists)
                    if ($token) {
                        try {
                            JWTAuth::invalidate($token);
                        } catch (\Exception $e) {
                            // Silent fail for token invalidation
                        }
                    }
                    return false;
                }
            } else {
                $this->error("   - JWT Authentication failed with provided credentials");
                return false;
            }

        } catch (\Exception $e) {
            $this->error("   - Login test error: " . $e->getMessage());
            return false;
        }
    }
} 