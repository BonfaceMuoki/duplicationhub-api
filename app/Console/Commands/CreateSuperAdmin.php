<?php

namespace App\Console\Commands;

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateSuperAdmin extends Command
{
    protected $signature = 'create:super-admin';
    protected $description = 'Create a super admin user';

    public function handle()
    {
        $this->info('Creating Super Admin User...');

       
        $firstName = $this->ask('First Name');
        $lastName = $this->ask('Last Name');
        $email = $this->ask('Email');
        $password = $this->secret('Password');
        $gender = $this->ask('Gender');


        $user = User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'middle_name' => '', 
            'email' => $email,
            'password' => Hash::make($password),
            'account_status' => 'ACTIVE',
            'date_of_birth' => now(), 
            'gender' => 'Male', 
        ]);

    
        $role = Role::firstOrCreate(['name' => 'super-admin']);

        $user->assignRole($role);

        $this->info('Super Admin user created successfully!');
        $this->table(
            ['Name', 'Email', 'Role'],
            [[$user->first_name . ' ' . $user->last_name, $user->email, 'super-admin']]
        );
    }

}
