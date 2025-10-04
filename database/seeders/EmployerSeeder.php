<?php

namespace Database\Seeders;

use App\Models\Employer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating specific employers...');

        // Create 2 specific employers
        $employers = [
            [
                'name' => 'Ahmed Tourism Services',
                'email' => 'ahmed.tourism@tunisian-tours.com',
                'employer_name' => 'Ahmed Tourism Services',
                'employer_email' => 'ahmed.tourism@tunisian-tours.com',
                'industry' => 'Tourism & Travel',
                'location' => 'Tunis, Tunisia',
                'description' => 'Specialized in authentic Tunisian cultural tours and desert adventures.',
                'website' => 'https://ahmed-tours.tn',
                'phone' => '+216 50 123 456',
            ],
            [
                'name' => 'Sahara Adventures Co',
                'email' => 'contact@sahara-adventures.com',
                'employer_name' => 'Sahara Adventures Co',
                'employer_email' => 'contact@sahara-adventures.com',
                'industry' => 'Adventure Tourism',
                'location' => 'Tozeur, Tunisia',
                'description' => 'Leading provider of desert safaris and southern Tunisia explorations.',
                'website' => 'https://sahara-adventures.tn',
                'phone' => '+216 51 234 567',
            ],
        ];

        foreach ($employers as $employerData) {
            // Check if user already exists
            $user = User::where('email', $employerData['email'])->first();

            if (!$user) {
                // Create user
                $user = User::create([
                    'name' => $employerData['name'],
                    'email' => $employerData['email'],
                    'password' => Hash::make('employer123'),
                    'email_verified_at' => now(),
                ]);
            }

            // Check if employer profile already exists
            if (!$user->employer) {
                // Create employer profile
                Employer::create([
                    'user_id' => $user->id,
                    'employer_name' => $employerData['employer_name'],
                    'employer_email' => $employerData['employer_email'],
                    'industry' => $employerData['industry'],
                    'location' => $employerData['location'],
                    'description' => $employerData['description'],
                    'website' => $employerData['website'],
                    'phone' => $employerData['phone'],
                ]);
            }
        }

        $this->command->info('Created 2 specific employers successfully!');
    }
}