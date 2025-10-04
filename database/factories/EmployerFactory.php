<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employer>
 */
class EmployerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     
    public function definition(): array
    {
        // Tunisian destinations
        $destinations = [
            'Tunis', 'Sousse', 'Hammamet', 'Djerba', 
            'Tozeur', 'Sfax', 'Kairouan', 'Bizerte', 
            'Monastir', 'Mahdia', 'Tabarka', 'Nabeul', 
            'El Jem', 'Carthage', 'Zarzis', 'Douz'
        ];
        return [
            'user_id' => User::factory(),
            'employer_name' => fake()->company(),
            'employer_email' => fake()->unique()->companyEmail(),
            'employer_logo' => fake()->imageUrl(),
            'industry' => fake()->randomElement([
                'Tour Operators',
                'Travel Agencies',
                'Hotels & Resorts',
                'Transportation Services',
                'Restaurant & Food Services',
                'Adventure & Outdoor Activities',
                'Cultural & Heritage Tours',
                'Eco-tourism Operators',
                'Luxury Travel Services',
                'Budget Travel Agencies',
                'Family Travel Specialists',
                'Business Travel Services'
            ]),
            'location' => fake()->randomElement($destinations),
            'description' => fake()->paragraphs(2, true),
            'website' => fake()->url(),
            'phone' => fake()->phoneNumber(),
        ];
    }
    
    /**
     * Configure the model factory to create an employer without a logo.
     *
     * @return $this
     */
    public function withoutLogo()
    {
        return $this->state(function (array $attributes) {
            return [
                'employer_logo' => null,
            ];
        });
    }
    
    /**
     * Configure the model factory to create an employer with a specific industry.
     *
     * @param string $industry
     * @return $this
     */
    public function industry(string $industry)
    {
        return $this->state(function (array $attributes) use ($industry) {
            return [
                'industry' => $industry,
            ];
        });
    }
}