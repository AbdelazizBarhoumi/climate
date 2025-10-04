<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tour;

// Find a 14-day tour
$tour = Tour::where('duration', '14 days')->first();

if ($tour) {
    echo "Tour: {$tour->title}\n";
    echo "Route: {$tour->getDestinationsString()}\n";
    echo "Total destinations: " . count($tour->getDestinations()) . " cities\n";
    echo "Total days: {$tour->getTotalDays()}\n\n";
    
    echo "Full itinerary:\n";
    foreach ($tour->getDestinations() as $i => $dest) {
        echo ($i + 1) . ". {$dest['city']} - {$dest['days']} days\n";
    }
}
