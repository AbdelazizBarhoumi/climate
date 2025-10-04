<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tour;

echo "\n=== Sample Tour Breakdown ===\n\n";

$tour = Tour::where('duration', '7 days')->first();

echo "Title: {$tour->title}\n";
echo "Duration: {$tour->duration}\n";
echo "Price: {$tour->price}\n";
echo "Starting Location: {$tour->getStartingLocation()}\n\n";

echo "Full Itinerary:\n";
echo str_repeat("=", 70) . "\n";

$destinations = $tour->getDestinations();
$totalDays = 0;

foreach ($destinations as $index => $dest) {
    $totalDays += $dest['days'];
    echo "\nDay " . ($totalDays - $dest['days'] + 1);
    if ($dest['days'] > 1) {
        echo "-{$totalDays}";
    }
    echo ": {$dest['city']} ({$dest['region']}) - {$dest['days']} " . ($dest['days'] > 1 ? 'days' : 'day') . "\n";
    echo str_repeat("-", 70) . "\n";
    
    if (!empty($dest['attractions'])) {
        echo "Attractions: " . implode(", ", $dest['attractions']) . "\n";
    }
    
    if (!empty($dest['activities'])) {
        echo "Activities: " . implode(", ", $dest['activities']) . "\n";
    }
    
    if (!empty($dest['description'])) {
        echo "\n" . wordwrap($dest['description'], 70) . "\n";
    }
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "\nComplete Route: " . implode(' → ', array_column($destinations, 'city')) . "\n";
echo "Total Duration: {$totalDays} days\n";
echo "\n=== End of Tour ===\n\n";
