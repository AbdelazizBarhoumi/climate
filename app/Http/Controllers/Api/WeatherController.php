<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherController extends Controller
{
    public function getWeather($city)
    {
        // Validate city name (only allow Tunisian cities for security)
        $allowedCities = [
            'Tunis', 'Sousse', 'Hammamet', 'Djerba', 'Tozeur', 'Kairouan',
            'Bizerte', 'Monastir', 'Mahdia', 'Tabarka', 'Nabeul', 'El Jem',
            'Douz', 'Sfax', 'Gabes', 'Gafsa', 'Sidi Bou Said', 'Carthage'
        ];

        if (!in_array($city, $allowedCities)) {
            return response()->json([
                'error' => 'City not found',
                'message' => 'Weather data is only available for Tunisian cities'
            ], 404);
        }

        // Check cache first (cache for 30 minutes)
        $cacheKey = "weather_{$city}";
        $cachedData = Cache::get($cacheKey);

        if ($cachedData) {
            return response()->json($cachedData);
        }

        try {
            // Get API key from environment
            $apiKey = config('services.openweather.key') ?: env('OPENWEATHER_API_KEY');

            if (!$apiKey) {
                return response()->json([
                    'error' => 'API key not configured',
                    'message' => 'Weather service is temporarily unavailable. Please try again later.'
                ], 503);
            }

            // Fetch current weather
            $response = Http::get("https://api.openweathermap.org/data/2.5/weather", [
                'q' => $city . ',TN', // TN for Tunisia
                'appid' => $apiKey,
                'units' => 'metric'
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'error' => 'Weather service unavailable',
                    'message' => 'Unable to fetch weather data at this time.'
                ], 503);
            }

            $data = $response->json();

            // Format the response
            $weatherData = [
                'temperature' => round($data['main']['temp']),
                'condition' => ucfirst($data['weather'][0]['description']),
                'humidity' => $data['main']['humidity'],
                'wind_speed' => round($data['wind']['speed'] * 3.6), // Convert m/s to km/h
                'icon' => $data['weather'][0]['icon'],
                'updated_at' => now()->format('H:i'),
                'city' => $data['name'],
                'country' => $data['sys']['country']
            ];

            // Try to get forecast (optional)
            try {
                $forecastResponse = Http::get("https://api.openweathermap.org/data/2.5/forecast", [
                    'q' => $city . ',TN',
                    'appid' => $apiKey,
                    'units' => 'metric',
                    'cnt' => 8 // Next 24 hours (3-hour intervals)
                ]);

                if ($forecastResponse->successful()) {
                    $forecastData = $forecastResponse->json();
                    $forecastText = 'Next 24h: ';

                    // Get temperature for next 6 hours
                    for ($i = 0; $i < min(6, count($forecastData['list'])); $i++) {
                        $item = $forecastData['list'][$i];
                        $time = date('H:i', $item['dt']);
                        $temp = round($item['main']['temp']);
                        $forecastText .= "{$time} - {$temp}°C | ";
                    }

                    $weatherData['forecast'] = rtrim($forecastText, ' | ');
                }
            } catch (\Exception $e) {
                // Forecast is optional, continue without it
                $weatherData['forecast'] = 'Forecast unavailable';
            }

            // Cache for 30 minutes
            Cache::put($cacheKey, $weatherData, now()->addMinutes(30));

            return response()->json($weatherData);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Service error',
                'message' => 'Weather service is temporarily unavailable.'
            ], 503);
        }
    }
}
