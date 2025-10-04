<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Employer;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tour>
 */
class TourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Create employer with specific timestamp
        $employerCreatedAt = fake()->dateTimeBetween('2024-12-01', '-1 day');
        $tourCreatedAt = fake()->dateTimeBetween($employerCreatedAt, 'now');
        
        // Create employer with its own timestamp
        $employer = Employer::factory()
            ->state(function (array $attributes) use ($employerCreatedAt) {
                return [
                    'created_at' => $employerCreatedAt,
                    'updated_at' => $employerCreatedAt
                ];
            })
            ->withoutLogo()
            ->create();
        
        // Tunisian destinations with detailed information
        $tunisianCities = [
            [
                'city' => 'Tunis',
                'region' => 'North',
                'attractions' => ['Medina of Tunis', 'Bardo Museum', 'Carthage Ruins', 'Sidi Bou Said'],
                'activities' => ['Cultural tours', 'Museum visits', 'Historical sites', 'Shopping in souks']
            ],
            [
                'city' => 'Sousse',
                'region' => 'Center-East',
                'attractions' => ['Sousse Medina', 'Port El Kantaoui', 'Great Mosque', 'Archaeological Museum'],
                'activities' => ['Beach activities', 'Water sports', 'Historical tours', 'Shopping']
            ],
            [
                'city' => 'Hammamet',
                'region' => 'North-East',
                'attractions' => ['Medina of Hammamet', 'Yasmine Hammamet', 'Beaches', 'Golf courses'],
                'activities' => ['Beach relaxation', 'Golf', 'Spa treatments', 'Water sports']
            ],
            [
                'city' => 'Djerba',
                'region' => 'South-East',
                'attractions' => ['El Ghriba Synagogue', 'Guellala Museum', 'Beaches', 'Flamingo Island'],
                'activities' => ['Beach activities', 'Cultural tours', 'Traditional pottery', 'Island exploration']
            ],
            [
                'city' => 'Tozeur',
                'region' => 'South-West',
                'attractions' => ['Chott el Djerid', 'Ong Jemal', 'Palm grove', 'Old Medina'],
                'activities' => ['Desert safari', 'Oasis tours', '4x4 adventures', 'Star Wars locations']
            ],
            [
                'city' => 'Kairouan',
                'region' => 'Center',
                'attractions' => ['Great Mosque', 'Aghlabid Basins', 'Medina', 'Carpet workshops'],
                'activities' => ['Religious tours', 'Historical sites', 'Craft workshops', 'Cultural experiences']
            ],
            [
                'city' => 'Bizerte',
                'region' => 'North',
                'attractions' => ['Old Port', 'Spanish Fort', 'Beaches', 'Ichkeul National Park'],
                'activities' => ['Beach activities', 'Bird watching', 'Historical tours', 'Seafood dining']
            ],
            [
                'city' => 'Monastir',
                'region' => 'Center-East',
                'attractions' => ['Ribat Fortress', 'Bourguiba Mausoleum', 'Marina', 'Beaches'],
                'activities' => ['Historical tours', 'Beach activities', 'Marina walks', 'Water sports']
            ],
            [
                'city' => 'Mahdia',
                'region' => 'Center-East',
                'attractions' => ['Old City', 'Skifa el Kahla', 'Beaches', 'Cap Afrique'],
                'activities' => ['Beach relaxation', 'Historical tours', 'Fishing', 'Scuba diving']
            ],
            [
                'city' => 'Tabarka',
                'region' => 'North-West',
                'attractions' => ['Coral Coast', 'Genoese Fort', 'Jazz Festival venue', 'Beaches'],
                'activities' => ['Scuba diving', 'Hiking', 'Beach activities', 'Music festivals']
            ],
            [
                'city' => 'Nabeul',
                'region' => 'North-East',
                'attractions' => ['Pottery market', 'Beaches', 'Friday market', 'Archaeological sites'],
                'activities' => ['Pottery shopping', 'Beach relaxation', 'Market tours', 'Cultural experiences']
            ],
            [
                'city' => 'El Jem',
                'region' => 'Center-East',
                'attractions' => ['Roman Amphitheater', 'Archaeological Museum'],
                'activities' => ['Historical tours', 'Photography', 'Cultural experiences']
            ],
            [
                'city' => 'Douz',
                'region' => 'South',
                'attractions' => ['Sahara Desert', 'Desert markets', 'Ksar Ghilane oasis'],
                'activities' => ['Camel trekking', 'Desert camping', '4x4 tours', 'Quad biking']
            ],
            [
                'city' => 'Sfax',
                'region' => 'Center-East',
                'attractions' => ['Medina', 'Archaeological Museum', 'Kerkennah Islands'],
                'activities' => ['City tours', 'Island excursions', 'Shopping', 'Seafood dining']
            ],
        ];
        
        // Generate random trip duration between 1 and 60 days
        $totalDays = fake()->numberBetween(1, 60);
        
        // Calculate number of destinations based on duration
        // Limit to maximum of 14 destinations (number of available cities)
        // 1 day = 1 destination
        // 2-4 days = 1-2 destinations
        // 5-9 days = 2-4 destinations
        // 10-19 days = 4-7 destinations
        // 20-39 days = 7-12 destinations
        // 40-60 days = 12-14 destinations
        if ($totalDays == 1) {
            $numDestinations = 1;
        } elseif ($totalDays <= 4) {
            $numDestinations = fake()->numberBetween(1, 2);
        } elseif ($totalDays <= 9) {
            $numDestinations = fake()->numberBetween(2, 4);
        } elseif ($totalDays <= 19) {
            $numDestinations = fake()->numberBetween(4, 7);
        } elseif ($totalDays <= 39) {
            $numDestinations = fake()->numberBetween(7, 12);
        } else {
            $numDestinations = fake()->numberBetween(12, 14); // Max 14 cities available
        }
        
        // Generate multi-destination itinerary
        $selectedCities = fake()->randomElements($tunisianCities, $numDestinations);
        $destinations = [];
        $daysPerDestination = floor($totalDays / $numDestinations);
        $extraDays = $totalDays % $numDestinations;
        
        foreach ($selectedCities as $index => $cityData) {
            $daysInCity = $daysPerDestination + ($index < $extraDays ? 1 : 0);
            
            // Select random attractions and activities
            $numAttractions = min(fake()->numberBetween(2, 4), count($cityData['attractions']));
            $numActivities = min(fake()->numberBetween(2, 4), count($cityData['activities']));
            
            $destinations[] = [
                'city' => $cityData['city'],
                'region' => $cityData['region'],
                'days' => $daysInCity,
                'attractions' => fake()->randomElements($cityData['attractions'], $numAttractions),
                'activities' => fake()->randomElements($cityData['activities'], $numActivities),
                'description' => $this->generateDestinationDescription($cityData['city'], $daysInCity)
            ];
        }
        
        // Starting location is the first destination
        $startingLocation = $destinations[0]['city'];
        
        // Set deadline date to be between the tour creation date and 3 months after
        $deadlineDate = fake()->dateTimeBetween(
            Carbon::instance($tourCreatedAt)->addDays(7),
            Carbon::instance($tourCreatedAt)->addMonths(3)
        );
        
        // Tour titles based on destination types
        $tourTitles = [
            'Grand Tunisia Discovery', 'Cultural Heritage Journey', 'Desert & Coast Adventure', 
            'Historical Tunisia Explorer', 'Mediterranean & Sahara Tour', 'Ancient Civilizations Trail',
            'Tunisia Highlights Tour', 'Coastal & Desert Experience', 'Tunisia Complete Circuit',
            'Best of Tunisia', 'Tunisia Cultural Immersion', 'North to South Tunisia',
            'Tunisia Heritage & Nature', 'Discover Tunisia Tour', 'Tunisia Adventure Package'
        ];
        
        // Generate comprehensive description
        $description = $this->generateTourDescription($destinations, $totalDays);
        
        // Format duration label
        $durationLabel = $totalDays == 1 ? '1 day' : "{$totalDays} days";
        
        // Calculate realistic price based on duration and destinations
        // Base price per day: 200-400 TND
        // Additional cost per destination: 50-150 TND
        // Longer trips get slight discounts per day
        $basePricePerDay = $totalDays <= 7 ? fake()->numberBetween(250, 400) :
                          ($totalDays <= 14 ? fake()->numberBetween(220, 350) :
                          ($totalDays <= 30 ? fake()->numberBetween(200, 320) :
                           fake()->numberBetween(180, 300)));
        
        $destinationBonus = count($destinations) * fake()->numberBetween(50, 150);
        $totalPrice = ($basePricePerDay * $totalDays) + $destinationBonus;
        
        return [
            'employer_id' => $employer->id,
            'title' => fake()->randomElement($tourTitles),
            'price' => 'TN ' . number_format($totalPrice, 0, '.', ''),
            'location' => $startingLocation,
            'destinations' => $destinations,
            'schedule' => 'Multi-Day Tour',
            'description' => $description,
            'duration' => $durationLabel,
            'deadline_date' => $deadlineDate,
            'featured' => fake()->boolean(20),
            'is_active' => fake()->boolean(90),
            'view_count' => fake()->numberBetween(0, 500),
            'created_at' => $tourCreatedAt,
            'updated_at' => $tourCreatedAt,
        ];
    }
    
    /**
     * Generate destination description.
     */
    private function generateDestinationDescription(string $city, int $days): string
    {
        $descriptions = [
            "Explore the vibrant city of {$city} for {$days} " . ($days > 1 ? 'days' : 'day') . ", discovering its rich cultural heritage and local attractions.",
            "Spend {$days} unforgettable " . ($days > 1 ? 'days' : 'day') . " in {$city}, immersing yourself in authentic Tunisian experiences.",
            "Experience the unique charm of {$city} during your {$days}-" . ($days > 1 ? 'day' : 'day') . " stay, with guided tours and free time to explore.",
            "Discover the hidden gems of {$city} over {$days} " . ($days > 1 ? 'days' : 'day') . " of adventure and cultural exploration."
        ];
        
        return fake()->randomElement($descriptions);
    }
    
    /**
     * Generate comprehensive tour description.
     */
    private function generateTourDescription(array $destinations, int $totalDays): string
    {
        $cities = array_map(fn($d) => $d['city'], $destinations);
        $cityList = count($cities) > 2 
            ? implode(', ', array_slice($cities, 0, -1)) . ' and ' . end($cities)
            : implode(' and ', $cities);
        
        $intro = "Embark on an unforgettable {$totalDays}-day journey through Tunisia, visiting {$cityList}. ";
        $intro .= "This carefully curated tour combines cultural heritage, natural beauty, and authentic experiences. ";
        
        $highlights = "\n\nHighlights include:\n";
        foreach ($destinations as $dest) {
            $highlights .= "• {$dest['city']}: " . implode(', ', array_slice($dest['attractions'], 0, 2)) . "\n";
        }
        
        $conclusion = "\n\nThis tour includes professional guides, comfortable accommodation, transportation between destinations, and selected meals. ";
        $conclusion .= "Perfect for travelers seeking an authentic Tunisian experience with a perfect balance of organized activities and free time.";
        
        return $intro . $highlights . $conclusion;
    }
    
    /**
     * Create an tour with immediate deadline_date.
     */
    public function urgent()
    {
        return $this->state(function (array $attributes) {
            // Get the tour creation date and add 2-7 days for urgent deadlines
            $creationDate = $attributes['created_at'] instanceof \DateTime 
                ? Carbon::instance($attributes['created_at']) 
                : new Carbon($attributes['created_at']);
                
            return [
                'deadline_date' => $creationDate->copy()->addDays(fake()->numberBetween(2, 7)),
            ];
        });
    }
    
    /**
     * Create a tour with a specific duration configuration.
     * This regenerates destinations to match the specified duration.
     */
    public function withDuration(int $days)
    {
        return $this->state(function (array $attributes) use ($days) {
            // Calculate number of destinations based on duration dynamically
            // Limit to maximum of 14 destinations (number of available cities)
            if ($days == 1) {
                $numDestinations = 1;
            } elseif ($days <= 4) {
                $numDestinations = fake()->numberBetween(1, 2);
            } elseif ($days <= 9) {
                $numDestinations = fake()->numberBetween(2, 4);
            } elseif ($days <= 19) {
                $numDestinations = fake()->numberBetween(4, 7);
            } elseif ($days <= 39) {
                $numDestinations = fake()->numberBetween(7, 12);
            } else {
                $numDestinations = fake()->numberBetween(12, 14); // Max 14 cities available
            }
            
            // Tunisian cities data
            $tunisianCities = [
                ['city' => 'Tunis', 'region' => 'North', 'attractions' => ['Medina of Tunis', 'Bardo Museum', 'Carthage Ruins', 'Sidi Bou Said'], 'activities' => ['Cultural tours', 'Museum visits', 'Historical sites', 'Shopping in souks']],
                ['city' => 'Sousse', 'region' => 'Center-East', 'attractions' => ['Sousse Medina', 'Port El Kantaoui', 'Great Mosque', 'Archaeological Museum'], 'activities' => ['Beach activities', 'Water sports', 'Historical tours', 'Shopping']],
                ['city' => 'Hammamet', 'region' => 'North-East', 'attractions' => ['Medina of Hammamet', 'Yasmine Hammamet', 'Beaches', 'Golf courses'], 'activities' => ['Beach relaxation', 'Golf', 'Spa treatments', 'Water sports']],
                ['city' => 'Djerba', 'region' => 'South-East', 'attractions' => ['El Ghriba Synagogue', 'Guellala Museum', 'Beaches', 'Flamingo Island'], 'activities' => ['Beach activities', 'Cultural tours', 'Traditional pottery', 'Island exploration']],
                ['city' => 'Tozeur', 'region' => 'South-West', 'attractions' => ['Chott el Djerid', 'Ong Jemal', 'Palm grove', 'Old Medina'], 'activities' => ['Desert safari', 'Oasis tours', '4x4 adventures', 'Star Wars locations']],
                ['city' => 'Kairouan', 'region' => 'Center', 'attractions' => ['Great Mosque', 'Aghlabid Basins', 'Medina', 'Carpet workshops'], 'activities' => ['Religious tours', 'Historical sites', 'Craft workshops', 'Cultural experiences']],
                ['city' => 'Bizerte', 'region' => 'North', 'attractions' => ['Old Port', 'Spanish Fort', 'Beaches', 'Ichkeul National Park'], 'activities' => ['Beach activities', 'Bird watching', 'Historical tours', 'Seafood dining']],
                ['city' => 'Monastir', 'region' => 'Center-East', 'attractions' => ['Ribat Fortress', 'Bourguiba Mausoleum', 'Marina', 'Beaches'], 'activities' => ['Historical tours', 'Beach activities', 'Marina walks', 'Water sports']],
                ['city' => 'Mahdia', 'region' => 'Center-East', 'attractions' => ['Old City', 'Skifa el Kahla', 'Beaches', 'Cap Afrique'], 'activities' => ['Beach relaxation', 'Historical tours', 'Fishing', 'Scuba diving']],
                ['city' => 'Tabarka', 'region' => 'North-West', 'attractions' => ['Coral Coast', 'Genoese Fort', 'Jazz Festival venue', 'Beaches'], 'activities' => ['Scuba diving', 'Hiking', 'Beach activities', 'Music festivals']],
                ['city' => 'Nabeul', 'region' => 'North-East', 'attractions' => ['Pottery market', 'Beaches', 'Friday market', 'Archaeological sites'], 'activities' => ['Pottery shopping', 'Beach relaxation', 'Market tours', 'Cultural experiences']],
                ['city' => 'El Jem', 'region' => 'Center-East', 'attractions' => ['Roman Amphitheater', 'Archaeological Museum'], 'activities' => ['Historical tours', 'Photography', 'Cultural experiences']],
                ['city' => 'Douz', 'region' => 'South', 'attractions' => ['Sahara Desert', 'Desert markets', 'Ksar Ghilane oasis'], 'activities' => ['Camel trekking', 'Desert camping', '4x4 tours', 'Quad biking']],
                ['city' => 'Sfax', 'region' => 'Center-East', 'attractions' => ['Medina', 'Archaeological Museum', 'Kerkennah Islands'], 'activities' => ['City tours', 'Island excursions', 'Shopping', 'Seafood dining']],
            ];
            
            // Generate destinations for this duration
            $selectedCities = fake()->randomElements($tunisianCities, $numDestinations);
            $destinations = [];
            $daysPerDestination = floor($days / $numDestinations);
            $extraDays = $days % $numDestinations;
            
            foreach ($selectedCities as $index => $cityData) {
                $daysInCity = $daysPerDestination + ($index < $extraDays ? 1 : 0);
                
                $numAttractions = min(fake()->numberBetween(2, 4), count($cityData['attractions']));
                $numActivities = min(fake()->numberBetween(2, 4), count($cityData['activities']));
                
                $destinations[] = [
                    'city' => $cityData['city'],
                    'region' => $cityData['region'],
                    'days' => $daysInCity,
                    'attractions' => fake()->randomElements($cityData['attractions'], $numAttractions),
                    'activities' => fake()->randomElements($cityData['activities'], $numActivities),
                    'description' => $this->generateDestinationDescription($cityData['city'], $daysInCity)
                ];
            }
            
            $startingLocation = $destinations[0]['city'];
            $description = $this->generateTourDescription($destinations, $days);
            
            // Format duration label
            $durationLabel = $days == 1 ? '1 day' : "{$days} days";
            
            // Calculate realistic price based on duration and destinations
            $basePricePerDay = $days <= 7 ? fake()->numberBetween(250, 400) :
                              ($days <= 14 ? fake()->numberBetween(220, 350) :
                              ($days <= 30 ? fake()->numberBetween(200, 320) :
                               fake()->numberBetween(180, 300)));
            
            $destinationBonus = count($destinations) * fake()->numberBetween(50, 150);
            $totalPrice = ($basePricePerDay * $days) + $destinationBonus;
            
            return [
                'duration' => $durationLabel,
                'destinations' => $destinations,
                'location' => $startingLocation,
                'description' => $description,
                'price' => 'TN ' . number_format($totalPrice, 0, '.', ''),
            ];
        });
    }
    
    /**
     * Indicate that the tour is featured.
     */
    public function featured()
    {
        return $this->state(function (array $attributes) {
            return [
                'featured' => true,
            ];
        });
    }

    
    /**
     * Create an active tour.
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => true,
            ];
        });
    }
    
    /**
     * Create an inactive tour.
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }
    
    /**
     * Create a popular tour with high view count.
     */
    public function popular()
    {
        return $this->state(function (array $attributes) {
            return [
                'view_count' => fake()->numberBetween(1000, 5000),
            ];
        });
    }
    
        /**
     * Set a custom date range for tour creation
     * while ensuring the employer was created before the tour
     */
    public function createdBetween(string $startDate, string $endDate): static
    {
        return $this->state(function (array $attributes) use ($startDate, $endDate) {
            // Get the employer
            $employer = Employer::find($attributes['employer_id']);
            
            // Get the employer created_at date
            $employerCreatedAt = $employer->created_at;
            
            // Ensure tour date is after employer creation date
            $startDateObj = new Carbon($startDate);
            $effectiveStartDate = $employerCreatedAt->gt($startDateObj) 
                ? $employerCreatedAt->format('Y-m-d') 
                : $startDate;
                
            $createdAt = fake()->dateTimeBetween($effectiveStartDate, $endDate);
            
            return [
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        });
    }
    
    /**
     * Create an tour with a recent creation date (last 30 days)
     * while ensuring the employer was created before the tour
     */
    public function recent(): static
    {
        return $this->state(function (array $attributes) {
            // Get the employer
            $employer = Employer::find($attributes['employer_id']);
            
            // Get the employer created_at date
            $employerCreatedAt = $employer->created_at;
            
            // Determine the effective start date (max of employer creation and 30 days ago)
            $thirtyDaysAgo = now()->subDays(30);
            $effectiveStartDate = $employerCreatedAt->gt($thirtyDaysAgo) 
                ? $employerCreatedAt->format('Y-m-d') 
                : $thirtyDaysAgo->format('Y-m-d');
                
            $createdAt = fake()->dateTimeBetween($effectiveStartDate, now());
            
            return [
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        });
    }
    
    /**
     * Create an tour for an existing employer
     */
    public function forEmployer(int $employerId): static
    {
        return $this->state(function (array $attributes) use ($employerId) {
            $employer = Employer::find($employerId);
            
            if (!$employer) {
                throw new \InvalidArgumentException("Employer with ID {$employerId} not found");
            }
            
            return [
                'employer_id' => $employerId,
                // Ensure tour is created after employer
                'created_at' => fake()->dateTimeBetween($employer->created_at, 'now'),
                'updated_at' => function (array $attributes) {
                    return $attributes['created_at'];
                },
            ];
        });
    }
}