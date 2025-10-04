<?php

// Quick test script to verify multi-destination tours
// Run with: php artisan tinker < test_destinations.php

use App\Models\Tour;

echo "\n=== Testing Multi-Destination Tours ===\n\n";

// Get a random tour
$tour = Tour::with('employer')->inRandomOrder()->first();

if (!$tour) {
    echo "No tours found. Please run: php artisan migrate:fresh --seed\n";
    exit;
}

echo "Tour: {$tour->title}\n";
echo "Duration: {$tour->duration}\n";
echo "Price: {$tour->price}\n";
echo "\n";

// Test destination methods
echo "Starting Location: " . $tour->getStartingLocation() . "\n";
echo "Total Days: " . $tour->getTotalDays() . "\n";
echo "Destinations String: " . $tour->getDestinationsString() . "\n";
echo "\n";

// Display full destinations
$destinations = $tour->getDestinations();
if (!empty($destinations)) {
    echo "Full Itinerary:\n";
    echo str_repeat("-", 60) . "\n";
    
    foreach ($destinations as $index => $dest) {
        echo "\nStop " . ($index + 1) . ": {$dest['city']} ({$dest['region']})\n";
        echo "Days: {$dest['days']}\n";
        
        if (!empty($dest['attractions'])) {
            echo "Attractions: " . implode(", ", $dest['attractions']) . "\n";
        }
        
        if (!empty($dest['activities'])) {
            echo "Activities: " . implode(", ", $dest['activities']) . "\n";
        }
        
        if (!empty($dest['description'])) {
            echo "Description: {$dest['description']}\n";
        }
    }
    
    echo "\n" . str_repeat("-", 60) . "\n";
    echo "\nRoute: " . implode(' → ', array_column($destinations, 'city')) . "\n";
} else {
    echo "This tour has a single location: {$tour->location}\n";
}

echo "\n=== Test Complete ===\n\n";
