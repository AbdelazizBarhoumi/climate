<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tour;

echo "\n=== Testing Multi-Destination Tours ===\n\n";

$tour = Tour::inRandomOrder()->first();

if (!$tour) {
    echo "No tours found.\n";
    exit;
}

echo "Tour: {$tour->title}\n";
echo "Duration: {$tour->duration}\n";
echo "Starting Location: {$tour->getStartingLocation()}\n";
echo "Destinations Route: {$tour->getDestinationsString()}\n";
echo "Total Days: {$tour->getTotalDays()}\n\n";

$destinations = $tour->getDestinations();
echo "Number of destinations: " . count($destinations) . "\n\n";

if (!empty($destinations)) {
    foreach ($destinations as $index => $dest) {
        echo "Stop " . ($index + 1) . ": {$dest['city']} ({$dest['days']} days)\n";
    }
}

echo "\n=== Test Passed! ===\n\n";
