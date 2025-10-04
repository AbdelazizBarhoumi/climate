<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherController extends Controller
{
    /**
     * Get weather data for a city
     */
    public function getWeather($city)
    {
        try {
            $apiKey = config('services.openweather.key') ?: env('WEATHER_API_KEY');

            if (!$apiKey) {
                return response()->json([
                    'error' => 'Weather API key not configured'
                ], 500);
            }

            // Cache weather data for 30 minutes to reduce API calls
            $cacheKey = "weather_{$city}";
            $weatherData = Cache::remember($cacheKey, 1800, function () use ($city, $apiKey) {
                // Current weather
                $currentResponse = Http::get("https://api.openweathermap.org/data/2.5/weather", [
                    'q' => $city . ',TN',
                    'appid' => $apiKey,
                    'units' => 'metric'
                ]);

                if (!$currentResponse->successful()) {
                    throw new \Exception('Failed to fetch current weather');
                }

                $currentData = $currentResponse->json();

                // Forecast data
                $forecastResponse = Http::get("https://api.openweathermap.org/data/2.5/forecast", [
                    'q' => $city . ',TN',
                    'appid' => $apiKey,
                    'units' => 'metric',
                    'cnt' => 8
                ]);

                $forecastData = $forecastResponse->successful() ? $forecastResponse->json() : null;

                return [
                    'current' => $currentData,
                    'forecast' => $forecastData
                ];
            });

            // Format the response for the frontend
            $current = $weatherData['current'];
            $forecast = $weatherData['forecast'];

            $response = [
                'temperature' => round($current['main']['temp']),
                'condition' => ucfirst($current['weather'][0]['description']),
                'humidity' => $current['main']['humidity'],
                'wind_speed' => round($current['wind']['speed'] * 3.6), // Convert m/s to km/h
                'icon' => $current['weather'][0]['icon'],
                'updated_at' => now()->format('H:i'),
            ];

            // Add forecast if available
            if ($forecast && isset($forecast['list'])) {
                $forecastText = 'Next 24h: ';
                $forecasts = array_slice($forecast['list'], 0, 6);

                foreach ($forecasts as $index => $item) {
                    if ($index % 2 === 0) { // Every 6 hours
                        $time = date('H', $item['dt']);
                        $temp = round($item['main']['temp']);
                        $forecastText .= "{$time}:00 - {$temp}°C | ";
                    }
                }

                $response['forecast'] = rtrim($forecastText, '| ');
            }

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Weather data unavailable',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get weather API configuration
     */
    public function getConfig()
    {
        $apiKey = config('services.openweather.key');
        
        if (!$apiKey) {
            $apiKey = env('WEATHER_API_KEY');
        }

        return response()->json([
            'api_available' => !empty($apiKey),
            'api_key_configured' => !empty($apiKey),
            'api_key' => $apiKey
        ]);
    }
}