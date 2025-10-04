<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Tour;
use App\Models\User;
use App\Models\Tourist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating applications with various statuses...');
        
        // First, check if we have tourists and tours
        $touristCount = Tourist::count();
        $tourCount = Tour::where('is_active', true)->count();
        
        if ($touristCount < 10) {
            $this->command->info('Creating additional tourists for applications...');
            User::factory()
                ->count(max(10 - $touristCount, 0))
                ->has(Tourist::factory())
                ->create();
        }
        
        if ($tourCount < 5) {
            $this->command->info('Creating additional tours for applications...');
            Tour::factory()
                ->count(max(5 - $tourCount, 0))
                ->active()
                ->create();
        }
        
        // Get active tours
        $activeTours = Tour::where('is_active', true)->get();
        
        // Get tourists with their users
        $tourists = Tourist::with('user')->get();
        
        // Ensure we don't create duplicate applications for the same user-tour pair
        $existingPairs = DB::table('applications')
            ->select('user_id', 'tour_id')
            ->get()
            ->map(function ($item) {
                return $item->user_id . '-' . $item->tour_id;
            })
            ->toArray();
        
        $this->command->info('Creating pending applications...');
        // Create 40 pending applications
        $this->createApplicationsWithStatus($tourists, $activeTours, 'pending', 40, $existingPairs);
        
        $this->command->info('Creating reviewing applications...');
        // Create 30 reviewing applications
        $this->createApplicationsWithStatus($tourists, $activeTours, 'reviewing', 30, $existingPairs);
        
        $this->command->info('Creating interviewed applications...');
        // Create 20 interviewed applications
        $this->createApplicationsWithStatus($tourists, $activeTours, 'interviewed', 20, $existingPairs);
        
        $this->command->info('Creating accepted applications...');
        // Create 15 accepted applications
        $this->createApplicationsWithStatus($tourists, $activeTours, 'accepted', 15, $existingPairs);
        
        $this->command->info('Creating rejected applications...');
        // Create 25 rejected applications
        $this->createApplicationsWithStatus($tourists, $activeTours, 'rejected', 25, $existingPairs);
        
        $applicationCount = Application::count();
        $this->command->info("Successfully created a total of {$applicationCount} applications!");
    }
    
    /**
     * Create applications with a specific status
     * 
     * @param \Illuminate\Support\Collection $tourists
     * @param \Illuminate\Support\Collection $tours
     * @param string $status
     * @param int $count
     * @param array $existingPairs
     */
    private function createApplicationsWithStatus($tourists, $tours, $status, $count, &$existingPairs)
    {
        $createdCount = 0;
        $maxAttempts = $count * 3; // Prevent infinite loop
        $attempts = 0;
        
        while ($createdCount < $count && $attempts < $maxAttempts) {
            $attempts++;
            
            // Get random tourist and tour
            $tourist = $tourists->random();
            $tour = $tours->random();
            
            // Check if this pair already exists
            $pair = $tourist->user_id . '-' . $tour->id;
            if (in_array($pair, $existingPairs)) {
                continue; // Skip this pair
            }
            
            // Add to existing pairs to prevent duplicates
            $existingPairs[] = $pair;
            
            // Create application with specified status
            $factory = Application::factory()
                ->forUser($tourist->user_id)
                ->fortour($tour->id);
            
            // Apply status-specific factory method
            switch ($status) {
                case 'pending':
                    $factory = $factory->pending();
                    break;
                case 'reviewing':
                    $factory = $factory->reviewing();
                    break;
                case 'interviewed':
                    $factory = $factory->interviewed();
                    break;
                case 'accepted':
                    $factory = $factory->accepted();
                    break;
                case 'rejected':
                    $factory = $factory->rejected();
                    break;
            }
            
            // Create the application
            $factory->create();
            $createdCount++;
        }
        
        if ($createdCount < $count) {
            $this->command->warn("Could only create {$createdCount} of {$count} requested {$status} applications due to unique constraints.");
        } else {
            $this->command->info("Created {$createdCount} {$status} applications.");
        }
    }
}