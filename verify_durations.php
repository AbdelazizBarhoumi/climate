<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tour;

echo "\n=== Verifying Duration Alignment ===\n\n";

// Check a few tours of different durations
$durations = ['3 days', '5 days', '7 days', '10 days', '14 days'];

foreach ($durations as $duration) {
    $tour = Tour::where('duration', $duration)->first();
    
    if ($tour) {
        $destinations = $tour->getDestinations();
        $totalDaysFromDestinations = array_sum(array_column($destinations, 'days'));
        $durationDays = $tour->getTotalDays();
        
        echo "Tour: {$tour->title}\n";
        echo "Duration field: {$tour->duration} ({$durationDays} days)\n";
        echo "Number of destinations: " . count($destinations) . "\n";
        echo "Total days from destinations: {$totalDaysFromDestinations} days\n";
        echo "Match: " . ($durationDays == $totalDaysFromDestinations ? "✓ YES" : "✗ NO") . "\n";
        echo "Route: " . $tour->getDestinationsString() . "\n";
        echo str_repeat("-", 60) . "\n\n";
    }
}

echo "=== Verification Complete ===\n\n";
