<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    /**
     * Tourism-related categories and tags for tour listings.
     */
    protected $tourCategories = [
        'destinations' => [
            'Tunisia', 'Carthage', 'Sidi Bou Said', 'Bizerte', 'Hammamet', 'Sousse', 'Monastir',
            'Mahdia', 'Sfax', 'Gabes', 'Medenine', 'Tataouine', 'Tozeur', 'Nefta', 'Douz',
            'Matmata', 'Chenini', 'Ksar Ghilane', 'Chott el Djerid', 'Gafsa', 'Kairouan',
            'El Jem', 'Nabeul', 'Kelibia', 'Tabarka', 'Ain Draham', 'Le Kef', 'Siliana'
        ],
        'attractions' => [
            'Ancient Ruins', 'Roman Sites', 'Medinas', 'Souks', 'Beaches', 'Desert', 'Oases',
            'Mountains', 'Star Wars Locations', 'Mosques', 'Museums', 'Fortresses', 'Thermal Springs',
            'Waterfalls', 'Canyons', 'Caves', 'Lakes', 'Islands', 'Archaeological Sites',
            'Historical Monuments', 'Natural Reserves', 'National Parks', 'Zoos', 'Aquariums'
        ],
        'activities' => [
            'Hiking', 'Trekking', 'Camel Riding', 'Quad Biking', 'Sandboarding', 'Desert Safari',
            'Beach Activities', 'Swimming', 'Diving', 'Snorkeling', 'Fishing', 'Boating', 'Sailing',
            'Kayaking', 'Canoeing', 'Surfing', 'Kitesurfing', 'Windsurfing', 'Scuba Diving',
            'Cultural Tours', 'Historical Tours', 'Food Tours', 'Wine Tasting', 'Cooking Classes',
            'Photography Tours', 'Bird Watching', 'Wildlife Safari', 'Hot Air Balloon', 'Paragliding'
        ],
        'services' => [
            'Guided Tours', 'Private Tours', 'Group Tours', 'Family Tours', 'Adventure Tours',
            'Luxury Tours', 'Budget Tours', 'Day Trips', 'Multi-Day Tours', 'Weekend Tours',
            'Custom Tours', 'Transportation', 'Accommodation', 'Meals Included', 'All Inclusive',
            'WiFi Available', 'Photography Services', 'Translation Services', 'Medical Assistance',
            'Insurance Included', '24/7 Support', 'Local Guides', 'Expert Guides', 'Certified Instructors'
        ]
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Flatten array of all tour tags
        $allTourTags = array_merge(...array_values($this->tourCategories));
        
        return [
            'name' => fake()->unique()->randomElement($allTourTags),
        ];
    }
    
    /**
     * Create a destinations-related tag.
     */
    public function destinations()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => fake()->unique()->randomElement($this->tourCategories['destinations']),
            ];
        });
    }
    
    /**
     * Create an attractions-related tag.
     */
    public function attractions()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => fake()->unique()->randomElement($this->tourCategories['attractions']),
            ];
        });
    }
    
    /**
     * Create an activities-related tag.
     */
    public function activities()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => fake()->unique()->randomElement($this->tourCategories['activities']),
            ];
        });
    }
    
    /**
     * Create a services-related tag.
     */
    public function services()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => fake()->unique()->randomElement($this->tourCategories['services']),
            ];
        });
    }
}