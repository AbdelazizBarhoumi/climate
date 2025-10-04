<?php

namespace Database\Seeders;

use App\Models\Tourist;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TouristSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating specific tourists...');

        // Create 2 specific tourists (students)
        $tourists = [
            [
                'name' => 'Maria Garcia',
                'email' => 'maria.garcia@student.university.edu',
                'phone' => '+34 612 345 678',
                'education_level' => 'bachelor',
                'institution' => 'University of Barcelona',
                'field_of_study' => 'Tourism Management',
                'graduation_date' => '2025-06-15',
                'skills' => 'Customer Service, Spanish, English, French, Cultural Tourism',
                'bio' => 'Passionate tourism student interested in cultural heritage and sustainable travel.',
                'linkedin_url' => 'https://linkedin.com/in/maria-garcia-tourism',
            ],
            [
                'name' => 'John Smith',
                'email' => 'john.smith@university.ca',
                'phone' => '+1 416 555 0123',
                'education_level' => 'bachelor',
                'institution' => 'University of Toronto',
                'field_of_study' => 'International Relations',
                'graduation_date' => '2025-04-30',
                'skills' => 'Photography, Public Speaking, Research, Cultural Studies',
                'bio' => 'International relations student with a passion for global cultures and adventure travel.',
                'linkedin_url' => 'https://linkedin.com/in/john-smith-ir',
            ],
        ];

        foreach ($tourists as $touristData) {
            // Check if user already exists
            $user = User::where('email', $touristData['email'])->first();

            if (!$user) {
                // Create user
                $user = User::create([
                    'name' => $touristData['name'],
                    'email' => $touristData['email'],
                    'password' => Hash::make('tourist123'),
                    'email_verified_at' => now(),
                ]);
            }

            // Check if tourist profile already exists
            if (!$user->tourist) {
                // Create tourist profile
                Tourist::create([
                    'user_id' => $user->id,
                    'phone' => $touristData['phone'],
                    'education_level' => $touristData['education_level'],
                    'institution' => $touristData['institution'],
                    'field_of_study' => $touristData['field_of_study'],
                    'graduation_date' => $touristData['graduation_date'],
                    'skills' => $touristData['skills'],
                    'bio' => $touristData['bio'],
                    'linkedin_url' => $touristData['linkedin_url'],
                ]);
            }
        }

        $this->command->info('Created 2 specific tourists successfully!');
    }
}