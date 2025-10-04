<?php

namespace Database\Seeders;

use App\Models\Tour;
use App\Models\Tag;
use App\Models\Employer;
use App\Models\User;
use Illuminate\Database\Seeder;

class TourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create predefined tags instead of using factory with unique constraint
        $this->command->info('Creating predefined tags...');
        
        // Define tag categories (tourism in Tunisia)
        // These replace the previous internship-oriented tags and map to the
        // existing created* variables used later in the seeder.
        $destinationsTags = [
            // Destinations / major cities & regions
            'Tunis', 'Sousse', 'Hammamet', 'Djerba', 'Tozeur', 'Sfax', 'Kairouan',
            'Bizerte', 'Monastir', 'Mahdia', 'Tabarka', 'Nabeul', 'El Jem', 'Carthage',
            'Zarzis', 'Douz', 'Kerkennah', 'Medenine', 'Gabes', 'Gafsa'
        ];

        $attractionsTags = [
            // Attractions & cultural sites
            'Medina of Tunis', 'El Djem Amphitheatre', 'Bardo Museum', 'Carthage Ruins',
            'Sidi Bou Said', 'Ribat of Monastir', 'Kairouan Mosque', 'Chebika Oasis',
            'Chott el Jerid (salt lake)', 'Djerba Island', 'Roman Villas', 'Sahara Dunes',
            'Cap Bon Peninsula', 'Traditional Souks', 'Local Handicrafts'
        ];

        $activitiesTags = [
            // Activities & experiences
            'Beach Activities', 'Scuba Diving', 'Kite Surfing', 'Sailing', 'Desert Excursions',
            'Camel Trekking', 'Cultural Tours', 'Food Tours', 'Olive Oil Tasting', 'Wine Tours',
            'Birdwatching', 'Hiking', 'Local Festivals', 'Wellness & Spas', 'Market (Souk) Visits'
        ];

        $servicesTags = [
            // Services & facilities useful for tourists
            'Hotels', 'Guesthouses', 'Guided Tours', 'Local Cuisine', 'Transportation',
            'Car Hire', 'Tourist Information', 'Handicrafts & Souvenirs', 'Eco-Tourism', 'Safety & Assistance'
        ];
        
        // Create the tags directly in database
        $createdDestinationsTags = collect();
        foreach ($destinationsTags as $name) {
            $createdDestinationsTags->push(Tag::firstOrCreate(['name' => $name]));
        }
        
        $createdAttractionsTags = collect();
        foreach ($attractionsTags as $name) {
            $createdAttractionsTags->push(Tag::firstOrCreate(['name' => $name]));
        }
        
        $createdActivitiesTags = collect();
        foreach ($activitiesTags as $name) {
            $createdActivitiesTags->push(Tag::firstOrCreate(['name' => $name]));
        }
        
        $createdServicesTags = collect();
        foreach ($servicesTags as $name) {
            $createdServicesTags->push(Tag::firstOrCreate(['name' => $name]));
        }
        
        // Combine all tags
        $allTags = $createdDestinationsTags->concat($createdAttractionsTags)
                                        ->concat($createdActivitiesTags)
                                        ->concat($createdServicesTags);
        
        $this->command->info('Created ' . $allTags->count() . ' tags in different categories');
        
        // Create some employers if none exist
        $employerCount = Employer::count();
        if ($employerCount < 15) {
            $usersWithoutEmployers = User::whereDoesntHave('employer')->take(15 - $employerCount)->get();
            
            foreach ($usersWithoutEmployers as $user) {
                Employer::factory()->create(['user_id' => $user->id]);
            }
            
            // If still not enough employers, create new ones
            if (Employer::count() < 15) {
                Employer::factory(15 - Employer::count())->withoutLogo()->create();
            }
        }
        
        // Get all employer IDs
        $employerIds = Employer::pluck('id')->toArray();
        
        // Create a variety of multi-destination tours
        $this->command->info('Creating multi-destination tours...');
        
        // Helper function to randomly apply states to a factory
        $applyRandomStates = function($factory) {
            // Randomly apply activity state (90% active by default, but we'll be explicit sometimes)
            if (rand(0, 150) < 20) {
                $factory = $factory->inactive();
            } elseif (rand(0, 150) < 15) {
                $factory = $factory->active();
            }
            
            // 20% chance of being featured (aligns with factory default)
            if (rand(0, 150) < 20) {
                $factory = $factory->featured();
            }
            
            // 15% chance of being urgent
            if (rand(0, 150) < 15) {
                $factory = $factory->urgent();
            }
            
            // 15% chance of being popular with high view count
            if (rand(0, 150) < 15) {
                $factory = $factory->popular();
            }
            
            return $factory;
        };
        
        // Create tours with varied durations (1-60 days) - 135 tours
        $this->command->info('Creating tours with varied durations (1-60 days)...');
        
        for ($i = 0; $i < 135; $i++) {
            // Generate random duration between 1 and 60 days
            // Weight towards shorter trips (more realistic)
            $rand = rand(1, 100);
            if ($rand <= 40) {
                // 40% chance: 1-7 days (short trips)
                $days = fake()->numberBetween(1, 7);
            } elseif ($rand <= 70) {
                // 30% chance: 8-14 days (medium trips)
                $days = fake()->numberBetween(8, 14);
            } elseif ($rand <= 90) {
                // 20% chance: 15-30 days (long trips)
                $days = fake()->numberBetween(15, 30);
            } else {
                // 10% chance: 31-60 days (extended trips)
                $days = fake()->numberBetween(31, 60);
            }
            
            $factory = Tour::factory()
                ->withDuration($days)
                ->state(['employer_id' => $employerIds[array_rand($employerIds)]]);
            
            // Apply random states
            $factory = $applyRandomStates($factory);
            
            // Create the tour with applied states
            $tour = $factory->create();
            
            // Attach relevant tags based on actual destinations
            $destinations = $tour->getDestinations();
            if (!empty($destinations)) {
                // Collect all cities, attractions, and activities from the actual tour destinations
                $tourCities = [];
                $tourAttractions = [];
                $tourActivities = [];
                
                foreach ($destinations as $dest) {
                    $tourCities[] = $dest['city'];
                    
                    // Collect attractions from this destination
                    if (!empty($dest['attractions'])) {
                        foreach ($dest['attractions'] as $attraction) {
                            $tourAttractions[] = $attraction;
                        }
                    }
                    
                    // Collect activities from this destination
                    if (!empty($dest['activities'])) {
                        foreach ($dest['activities'] as $activity) {
                            $tourActivities[] = $activity;
                        }
                    }
                }
                
                // Attach destination city tags (only cities in the tour)
                foreach (array_unique($tourCities) as $city) {
                    $tag = Tag::firstOrCreate(['name' => $city]);
                    $tour->tags()->attach($tag->id);
                }
                
                // Attach attraction tags based on what's actually in the tour
                $relevantAttractionTags = $createdAttractionsTags->filter(function($tag) use ($tourAttractions) {
                    // Check if any tour attraction contains this tag name
                    foreach ($tourAttractions as $attraction) {
                        if (stripos($attraction, $tag->name) !== false || stripos($tag->name, $attraction) !== false) {
                            return true;
                        }
                    }
                    return false;
                });
                
                if ($relevantAttractionTags->count() > 0) {
                    $tour->tags()->attach($relevantAttractionTags->pluck('id')->toArray());
                }
                
                // Attach activity tags based on what's actually in the tour
                $relevantActivityTags = $createdActivitiesTags->filter(function($tag) use ($tourActivities) {
                    // Check if any tour activity matches or contains this tag
                    foreach ($tourActivities as $activity) {
                        if (stripos($activity, $tag->name) !== false || stripos($tag->name, $activity) !== false) {
                            return true;
                        }
                    }
                    return false;
                });
                
                if ($relevantActivityTags->count() > 0) {
                    $tour->tags()->attach($relevantActivityTags->pluck('id')->toArray());
                }
                
                // Add some general service tags based on trip length
                if ($days >= 7) {
                    $tour->tags()->attach(
                        $createdServicesTags->shuffle()->take(rand(1, 2))->pluck('id')->toArray()
                    );
                }
            }
        }
        
        $this->command->info('Created ' . Tour::count() . ' tours with varied durations (1-60 days)!');
    }
}