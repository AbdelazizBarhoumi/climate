<x-layout :showHero="false">
  <x-page-heading>Create New Tour</x-page-heading>

  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  
  @if ($errors->any())
    <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
      <div class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zm-1 9a1 1 0 01-1-1v-4a1 1 0 112 0v4a1 1 0 01-1 1z" clip-rule="evenodd"></path>
        </svg>
        <p class="font-bold">Please fix the following errors:</p>
      </div>
      <ul class="list-disc ml-8 mt-2">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div id="tourCreationApp">
    <!-- Progress Steps -->
    <div class="mb-8">
      <nav aria-label="Progress">
        <ol class="flex items-center">
          <li class="relative pr-8 sm:pr-20">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
              <div class="h-0.5 w-full bg-gray-200"></div>
            </div>
            <div class="relative w-8 h-8 flex items-center justify-center bg-blue-600 rounded-full">
              <span class="text-white text-sm font-medium">1</span>
            </div>
            <span class="absolute top-10 left-0 text-xs font-medium text-gray-500">Basic Info</span>
          </li>
          <li class="relative pr-8 sm:pr-20">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
              <div class="h-0.5 w-full bg-gray-200"></div>
            </div>
            <div class="relative w-8 h-8 flex items-center justify-center bg-gray-300 rounded-full">
              <span class="text-gray-600 text-sm font-medium">2</span>
            </div>
            <span class="absolute top-10 left-0 text-xs font-medium text-gray-500">Destinations</span>
          </li>
          <li class="relative">
            <div class="relative w-8 h-8 flex items-center justify-center bg-gray-300 rounded-full">
              <span class="text-gray-600 text-sm font-medium">3</span>
            </div>
            <span class="absolute top-10 left-0 text-xs font-medium text-gray-500">Review</span>
          </li>
        </ol>
      </nav>
    </div>

    <x-forms.form method="POST" action="{{ route('tour.store') }}" id="tourForm" novalidate>
      <!-- Step 1: Basic Information -->
      <div id="step1" class="step-content">
        <div class="bg-white shadow-md rounded-lg p-6 mb-6 border border-gray-100 hover:shadow-lg transition-shadow duration-300">
          <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path>
              <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"></path>
            </svg>
            Basic Tour Information
          </h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-forms.input name="title" label="Tour Title" placeholder="e.g. Tunisia Cultural Heritage Discovery"/>
            <x-forms.input name="base_price" label="Base Price per Day (TND)" placeholder="e.g. 350" type="number"/>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
            <x-forms.select label="Tour Type" name="schedule">
              <option value="" @selected(!old('schedule'))>Select tour type</option>
              <option value="Cultural" @selected(old('schedule') == "Cultural")>Cultural Heritage</option>
              <option value="Adventure" @selected(old('schedule') == "Adventure")>Adventure & Nature</option>
              <option value="Beach" @selected(old('schedule') == "Beach")>Beach & Coastal</option>
              <option value="Desert" @selected(old('schedule') == "Desert")>Desert Safari</option>
              <option value="Historical" @selected(old('schedule') == "Historical")>Historical Sites</option>
              <option value="Mixed" @selected(old('schedule') == "Mixed")>Mixed Experience</option>
            </x-forms.select>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Total Duration (Days)</label>
              <input type="number" name="total_days" id="total_days"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                placeholder="e.g. 7" value="{{ old('total_days') }}">
              <p class="text-xs text-gray-500 mt-1">Choose between 1-60 days</p>
            </div>
          </div>

          <div class="mt-4">
            <x-forms.textarea name="description" label="Tour Overview" rows="4"
              placeholder="Provide an engaging description of your tour experience..."></x-forms.textarea>
            <p class="text-xs text-gray-500 mt-1">Describe the highlights and unique features of your tour</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
            <x-forms.input type="date" name="start_date" label="Tour Start Date" 
              min="{{ date('Y-m-d', strtotime('+7 days')) }}"
              title="When does the tour begin?"/>
            <x-forms.input type="date" name="deadline_date" label="Booking Deadline"
              title="Last day for bookings (optional)"/>
          </div>

          <div class="mt-4">
            <x-forms.input name="tags" label="Tour Tags (comma separated)" 
              placeholder="e.g. Cultural Heritage, Museums, Local Cuisine, Photography"
              title="These tags help travelers find your tour"/>
            <p class="text-xs text-gray-500 mt-1">Add relevant tags to increase visibility</p>
          </div>

          <div class="mt-6 bg-blue-50 p-3 rounded-md border border-blue-100">
            <x-forms.checkbox label="Feature this tour (Premium placement)" name="featured"/>
            <p class="text-sm text-gray-600 mt-1">Featured tours appear at the top of search results</p>
          </div>
        </div>

        <div class="flex justify-between">
          <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Cancel
          </a>
          <button type="button" onclick="nextStep()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md flex items-center">
            Next: Add Destinations
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Step 2: Destinations -->
      <div id="step2" class="step-content hidden">
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
          <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
            </svg>
            Tour Destinations & Itinerary
          </h2>

          <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex">
              <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
              </svg>
              <div>
                <p class="text-sm text-yellow-700">
                  <strong>Total duration:</strong> <span id="durationDisplay">0 days</span>
                </p>
                <p class="text-xs text-yellow-600 mt-1">Add destinations and adjust days to match your total tour duration</p>
              </div>
            </div>
          </div>

          <div id="destinations-container">
            <!-- Destinations will be added here dynamically -->
          </div>

          <div class="mb-4 flex gap-3">
            <button type="button" onclick="addDestination()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium">
              Add Destination
            </button>
            
            <button type="button" 
              onclick="showOverallRecommendations()" 
              class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-4 py-2 rounded-md flex items-center transition-all hover:scale-105 shadow-md"
              title="Get AI-powered recommendations for optimal destination ordering">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
              </svg>
              Optimize Route
            </button>
          </div>

          <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-700">
              <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
              </svg>
              Select a city from the dropdown to see real-time weather information and an interactive map
            </p>
          </div>
        </div>

        <div class="flex justify-between">
          <button type="button" onclick="prevStep()" class="text-gray-600 hover:text-gray-900 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Previous
          </button>
          <button type="button" onclick="nextStep()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md flex items-center">
            Next: Review & Submit
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Step 3: Review -->
      <div id="step3" class="step-content hidden">
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
          <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
            Review Your Tour
          </h2>

          <div id="tour-review">
            <!-- Review content will be populated by JavaScript -->
          </div>
        </div>

        <div class="flex justify-between">
          <button type="button" onclick="prevStep()" class="text-gray-600 hover:text-gray-900 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Previous
          </button>
          <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Create Tour
          </button>
        </div>
      </div>

      <!-- Hidden fields for destinations data -->
      <input type="hidden" name="destinations" id="destinations-data">
      <input type="hidden" name="location" id="location-field">
      <input type="hidden" name="duration" id="duration-field">
      <input type="hidden" name="price" id="price-field">
    </x-forms.form>
  </div>

  <!-- AI Recommendations Modal -->
  <div id="ai-modal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 items-center justify-center p-4" style="display: none;" onclick="closeAIModal(event)">
    <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden" onclick="event.stopPropagation()">
      <!-- Modal Header -->
      <div class="bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600 p-6 text-white">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-2xl font-bold">AI Climate Insights</h3>
              <p class="text-purple-100 text-sm">Powered by 20 years of historical data</p>
            </div>
          </div>
          <button onclick="closeAIModal()" class="text-white hover:bg-white/20 rounded-full p-2 transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Modal Body -->
      <div class="p-6 overflow-y-auto max-h-[calc(90vh-180px)]">
        <!-- Destination Info -->
        <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg border border-purple-200">
          <div class="flex items-center gap-3 mb-2">
            <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
            </svg>
            <h4 class="font-bold text-gray-800" id="ai-destination-name">Destination</h4>
          </div>
          <p class="text-sm text-gray-600">Analyzing climate patterns and historical trends...</p>
        </div>

        <!-- Loading State -->
        <div id="ai-loading" class="flex flex-col items-center justify-center py-12">
          <div class="relative w-20 h-20 mb-4">
            <div class="absolute inset-0 border-4 border-purple-200 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-purple-600 rounded-full border-t-transparent animate-spin"></div>
            <svg class="absolute inset-0 m-auto w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
          </div>
          <p class="text-gray-600 font-medium animate-pulse">AI is analyzing climate data...</p>
          <p class="text-sm text-gray-500 mt-2">Processing 20 years of historical patterns</p>
        </div>

        <!-- AI Recommendations Content -->
        <div id="ai-content" class="hidden space-y-4">
          <!-- Climate Overview -->
          <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-500">
            <div class="flex items-center gap-2 mb-2">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
              </svg>
              <h5 class="font-bold text-blue-900">Climate Overview (2005-2025)</h5>
            </div>
            <p class="text-sm text-blue-800" id="ai-overview">Loading...</p>
          </div>

          <!-- Seasonal Insights -->
          <div class="bg-green-50 rounded-lg p-4 border-l-4 border-green-500">
            <div class="flex items-center gap-2 mb-2">
              <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
              <h5 class="font-bold text-green-900">Best Seasons to Visit</h5>
            </div>
            <div class="text-sm text-green-800" id="ai-seasons">Loading...</div>
          </div>

          <!-- Weather Patterns -->
          <div class="bg-yellow-50 rounded-lg p-4 border-l-4 border-yellow-500">
            <div class="flex items-center gap-2 mb-2">
              <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
              </svg>
              <h5 class="font-bold text-yellow-900">Historical Trends</h5>
            </div>
            <p class="text-sm text-yellow-800" id="ai-trends">Loading...</p>
          </div>

          <!-- Recommendations -->
          <div class="bg-purple-50 rounded-lg p-4 border-l-4 border-purple-500">
            <div class="flex items-center gap-2 mb-2">
              <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <h5 class="font-bold text-purple-900">AI Recommendations</h5>
            </div>
            <ul class="text-sm text-purple-800 space-y-2" id="ai-recommendations">
              <li>Loading...</li>
            </ul>
          </div>

          <!-- Extreme Weather Alerts -->
          <div class="bg-red-50 rounded-lg p-4 border-l-4 border-red-500">
            <div class="flex items-center gap-2 mb-2">
              <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
              </svg>
              <h5 class="font-bold text-red-900">Weather Warnings</h5>
            </div>
            <p class="text-sm text-red-800" id="ai-warnings">Loading...</p>
          </div>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="p-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-gray-500">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <span>Demo: Based on simulated historical data analysis</span>
        </div>
        <button onclick="closeAIModal()" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:from-purple-700 hover:to-pink-700 transition-all font-medium shadow-md hover:shadow-lg">
          Got it!
        </button>
      </div>
    </div>
  </div>
<!-- Overall Itinerary Optimizer Modal -->
  <div id="overall-ai-modal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm z-[9999] items-center justify-center p-4" style="display: none;" onclick="closeOverallAIModal(event)">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden" onclick="event.stopPropagation()">
      <!-- Modal Header -->
      <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-6 text-white">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm animate-pulse">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-2xl font-bold"> AI Itinerary Optimizer</h3>
              <p class="text-purple-100 text-sm">Optimal destination ordering based on climate intelligence</p>
            </div>
          </div>
          <button onclick="closeOverallAIModal()" class="text-white hover:bg-white/20 rounded-full p-2 transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Modal Body -->
      <div class="p-6 overflow-y-auto max-h-[calc(90vh-180px)]">
        <!-- Loading State -->
        <div id="overall-ai-loading" class="flex flex-col items-center justify-center py-12">
          <div class="relative w-24 h-24 mb-4">
            <div class="absolute inset-0 border-4 border-indigo-200 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-indigo-600 rounded-full border-t-transparent animate-spin"></div>
            <svg class="absolute inset-0 m-auto w-12 h-12 text-indigo-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
          </div>
          <p class="text-gray-600 font-medium text-lg animate-pulse">AI is optimizing your itinerary...</p>
          <p class="text-sm text-gray-500 mt-2">Analyzing climate patterns, travel flow, and seasonal conditions</p>
          <div class="mt-4 flex gap-2">
            <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs animate-pulse">Climate Analysis</span>
            <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs animate-pulse">Route Optimization</span>
            <span class="px-3 py-1 bg-pink-100 text-pink-700 rounded-full text-xs animate-pulse">Seasonal Timing</span>
          </div>
        </div>

        <!-- AI Recommendations Content -->
        <div id="overall-ai-content" class="hidden space-y-6">
          <!-- Current vs Recommended -->
          <div class="grid md:grid-cols-2 gap-4">
            <!-- Current Order -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-300">
              <h5 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                  <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                </svg>
                Current Order
              </h5>
              <div id="overall-current-order" class="space-y-2">
                <!-- Will be populated -->
              </div>
            </div>
            
            <!-- Recommended Order -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-4 border-2 border-green-400 shadow-lg">
              <h5 class="font-bold text-green-900 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                ✨ AI-Optimized Order
              </h5>
              <div id="overall-recommended-order" class="space-y-2">
                <!-- Will be populated -->
              </div>
            </div>
          </div>

          <!-- Optimization Insights -->
          <div class="bg-blue-50 rounded-lg p-5 border-l-4 border-blue-500">
            <div class="flex items-center gap-2 mb-3">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
              </svg>
              <h5 class="font-bold text-blue-900 text-lg">Why This Order?</h5>
            </div>
            <div class="text-sm text-blue-800 space-y-2" id="overall-insights">
              <!-- Will be populated -->
            </div>
          </div>

          <!-- Climate Considerations -->
          <div class="bg-yellow-50 rounded-lg p-5 border-l-4 border-yellow-500">
            <div class="flex items-center gap-2 mb-3">
              <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
              <h5 class="font-bold text-yellow-900 text-lg">Climate Considerations</h5>
            </div>
            <ul class="text-sm text-yellow-800 space-y-2" id="overall-climate-notes">
              <!-- Will be populated -->
            </ul>
          </div>

          <!-- Travel Tips -->
          <div class="bg-purple-50 rounded-lg p-5 border-l-4 border-purple-500">
            <div class="flex items-center gap-2 mb-3">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <h5 class="font-bold text-purple-900 text-lg">Smart Travel Tips</h5>
            </div>
            <ul class="text-sm text-purple-800 space-y-2" id="overall-tips">
              <!-- Will be populated -->
            </ul>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-3 pt-4 border-t">
            <button onclick="applyRecommendedOrder()" class="flex-1 px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all font-medium shadow-md hover:shadow-lg flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              Apply Recommended Order
            </button>
            <button onclick="closeOverallAIModal()" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all font-medium">
              Keep Current
            </button>
          </div>
        </div>

        <!-- Footer Note -->
        <div class="mt-4 text-xs text-gray-500 text-center flex items-center justify-center gap-2">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
          </svg>
          <span>AI recommendations based on climate patterns, regional geography, and travel efficiency</span>
        </div>
      </div>
    </div>
  </div>

<script>
  /**
   * TOUR CREATION SYSTEM WITH ADVANCED WEATHER INTEGRATION
   * ======================================================
   * 
   * FEATURES:
   * - Multi-step tour creation wizard
   * - Real-time weather data integration
   * - Interactive weather maps with Leaflet.js
   * - Advanced time-travel slider (hourly/daily granularity)
   * - Weather comparison mode
   * - Date picker for specific dates
   * - Weather alerts for extreme conditions
   * - Animated transitions and smooth UX
   * 
   * WEATHER TIME TRAVEL FEATURES:
   * 1. Hourly Mode: View weather up to 120 hours (5 days) in past/future
   * 2. Daily Mode: Traditional 5-day past/future view
   * 3. Compare Mode: Side-by-side comparison of two time periods
   * 4. Date Picker: Jump directly to any specific date
   * 5. Weather Alerts: Automatic alerts for significant weather changes
   * 
   * API INTEGRATIONS:
   * - OpenWeatherMap API for current weather & forecasts
   * - Leaflet.js for interactive maps
   * - OpenWeatherMap tile layers for weather overlays
   */
  
  let currentStep = 1;
  let destinations = [];
  let destinationCounter = 0;
  const mapInstances = {};
  const currentLayers = {};

  // Tunisian cities coordinates
  const tunisianCities = {
    'Tunis': { lat: 36.8065, lng: 10.1815, region: 'North' },
    'Sousse': { lat: 35.8256, lng: 10.6369, region: 'Center' },
    'Hammamet': { lat: 36.4000, lng: 10.6167, region: 'North' },
    'Djerba': { lat: 33.8075, lng: 10.8451, region: 'South' },
    'Tozeur': { lat: 33.9197, lng: 8.1335, region: 'South' },
    'Kairouan': { lat: 35.6781, lng: 10.0963, region: 'Center' },
    'Bizerte': { lat: 37.2744, lng: 9.8739, region: 'North' },
    'Monastir': { lat: 35.7780, lng: 10.8262, region: 'Center' },
    'Mahdia': { lat: 35.5047, lng: 11.0622, region: 'Center' },
    'Tabarka': { lat: 36.9544, lng: 8.7581, region: 'North' },
    'Nabeul': { lat: 36.4531, lng: 10.7376, region: 'North' },
    'El Jem': { lat: 35.2933, lng: 10.7075, region: 'Center' },
    'Douz': { lat: 33.4667, lng: 9.0167, region: 'South' },
    'Sfax': { lat: 34.7406, lng: 10.7603, region: 'Center' },
    'Gabes': { lat: 33.8815, lng: 10.0982, region: 'South' },
    'Gafsa': { lat: 34.4250, lng: 8.7842, region: 'Center' },
    'Sidi Bou Said': { lat: 36.8686, lng: 10.3417, region: 'North' },
    'Carthage': { lat: 36.8528, lng: 10.3233, region: 'North' }
  };

  // Step navigation
  function nextStep() {
    if (currentStep >= 3) return;

    const currentStepElement = document.getElementById(`step${currentStep}`);
    const nextStepElement = document.getElementById(`step${currentStep + 1}`);

    if (currentStepElement && nextStepElement) {
      currentStepElement.classList.add('hidden');
      currentStep++;
      nextStepElement.classList.remove('hidden');
      updateProgressBar();

      if (currentStep === 3) {
        prepareReviewStep();
      }
    }
  }

  function prevStep() {
    if (currentStep <= 1) return;

    const currentStepElement = document.getElementById(`step${currentStep}`);
    const prevStepElement = document.getElementById(`step${currentStep - 1}`);

    if (currentStepElement && prevStepElement) {
      currentStepElement.classList.add('hidden');
      currentStep--;
      prevStepElement.classList.remove('hidden');
      updateProgressBar();
    }
  }

  function prepareReviewStep() {
    const totalDays = parseInt(document.getElementById('total_days').value) || 0;
    
    // Update hidden form fields
    document.getElementById('destinations-data').value = JSON.stringify(destinations);
    document.getElementById('location-field').value = destinations[0]?.city || '';
    document.getElementById('duration-field').value = `${totalDays} days`;

    // Calculate total price
    const basePrice = parseFloat(document.querySelector('[name="base_price"]').value) || 0;
    const totalPrice = Math.round(basePrice * totalDays);
    document.getElementById('price-field').value = `TN ${totalPrice}`;

    generateReview();
  }

  function updateProgressBar() {
    const steps = document.querySelectorAll('nav ol li');
    if (steps.length === 0) {
      console.warn('Progress bar steps not found');
      return;
    }

    steps.forEach((step, index) => {
      const circle = step.querySelector('.rounded-full');
      if (circle) {
        const span = circle.querySelector('span');
        if (index + 1 <= currentStep) {
          circle.classList.remove('bg-gray-300');
          circle.classList.add('bg-blue-600');
          if (span) {
            span.classList.remove('text-gray-600');
            span.classList.add('text-white');
          }
        } else {
          circle.classList.remove('bg-blue-600');
          circle.classList.add('bg-gray-300');
          if (span) {
            span.classList.remove('text-white');
            span.classList.add('text-gray-600');
          }
        }
      }
    });
  }

  function updateDurationDisplay() {
    const totalDays = parseInt(document.getElementById('total_days').value) || 0;
    const usedDays = destinations.reduce((sum, dest) => sum + (dest.days || 0), 0);
    const remainingDays = Math.max(0, totalDays - usedDays);

    let displayText = `${totalDays} days total`;
    if (destinations.length > 0) {
      displayText += ` (${usedDays} allocated, ${remainingDays} remaining)`;
    }

    const durationDisplay = document.getElementById('durationDisplay');
    if (durationDisplay) {
      durationDisplay.textContent = displayText;
    }
  }

  function addDestination() {
    destinationCounter++;
    const totalDays = parseInt(document.getElementById('total_days').value) || 1;
    const usedDays = destinations.reduce((sum, dest) => sum + (dest.days || 0), 0);
    const remainingDays = Math.max(1, totalDays - usedDays);
    
    const destinationHtml = createDestinationHtml(destinationCounter, remainingDays);
    
    document.getElementById('destinations-container').insertAdjacentHTML('beforeend', destinationHtml);
    
    // Add to destinations array
    destinations.push({
      id: destinationCounter,
      city: '',
      days: Math.min(remainingDays, 2),
      region: '',
      attractions: [],
      activities: [],
      description: ''
    });
    
    updateDurationDisplay();
  }

  function createDestinationHtml(id, remainingDays) {
    return `
      <div class="destination-item border border-gray-200 rounded-lg p-4 mb-4" data-index="${id}">
        <div class="flex justify-between items-center mb-3">
          <h4 class="text-md font-medium text-gray-900">Destination ${id}</h4>
          <button type="button" onclick="removeDestination(${id})" 
            class="text-red-600 hover:text-red-800 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
          </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
            <select class="destination-city mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
              onchange="updateDestination(${id})">
              <option value="">Select city</option>
              ${Object.keys(tunisianCities).map(city => 
                `<option value="${city}">${city}</option>`
              ).join('')}
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Days</label>
            <input type="number" class="destination-days mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
              placeholder="Days" value="${Math.min(remainingDays, 2)}" min="1" onchange="updateDestination(${id})">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Region</label>
            <select class="destination-region mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" onchange="updateDestination(${id})">
              <option value="">Select region</option>
              <option value="North">North</option>
              <option value="Center">Center</option>
              <option value="South">South</option>
              <option value="East">East</option>
              <option value="West">West</option>
            </select>
          </div>
        </div>
        
        <!-- Weather Information -->
        <div class="climate-widget mt-4 hidden" id="climate-${id}">
          <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <div class="flex items-center justify-between mb-2">
              <h5 class="text-sm font-semibold text-gray-900">Current Weather</h5>
              <span class="text-xs text-gray-600 climate-update-time">Loading...</span>
            </div>
            <div class="climate-data flex items-center justify-between">
              <div class="flex items-center space-x-4">
                <div class="climate-temp text-2xl font-bold text-gray-900">--°C</div>
                <div class="climate-condition text-sm text-gray-700">--</div>
              </div>
              <div class="text-right">
                <div class="climate-humidity text-sm text-gray-600">Humidity: --%</div>
                <div class="climate-wind text-sm text-gray-600">Wind: -- km/h</div>
              </div>
            </div>
            <div class="mt-2 pt-2 border-t border-gray-300">
              <div class="climate-forecast text-xs text-gray-600">Loading forecast...</div>
            </div>
          </div>
        </div>
        
        <!-- Interactive Weather Map Widget -->
        <div class="map-widget mt-4 hidden" id="map-${id}">
          <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
              <h5 class="text-sm font-semibold text-gray-900">Weather Map</h5>
              <div class="flex gap-2">
                <button type="button" class="text-xs text-gray-500 hover:text-gray-700" onclick="toggleMapFullscreen(${id})" title="Toggle fullscreen">
                  Fullscreen
                </button>
                <button type="button" 
                  class="text-xs bg-gray-600 hover:bg-gray-700 text-white px-2 py-1 rounded" 
                  onclick="showAIRecommendations(${id})"
                  title="Climate insights">
                  Climate Info
                </button>
              </div>
            </div>
            
            <!-- Map Layer Controls -->
            <div class="mb-3 flex flex-wrap gap-2">
              <button type="button" class="map-layer-btn px-3 py-1 text-xs rounded border bg-blue-600 text-white" 
                onclick="changeMapLayer(${id}, 'temp')" data-layer="temp">
                Temperature
              </button>
              <button type="button" class="map-layer-btn px-3 py-1 text-xs rounded border border-gray-300 text-gray-700 hover:bg-gray-100" 
                onclick="changeMapLayer(${id}, 'wind')" data-layer="wind">
                Wind
              </button>
              <button type="button" class="map-layer-btn px-3 py-1 text-xs rounded border border-gray-300 text-gray-700 hover:bg-gray-100" 
                onclick="changeMapLayer(${id}, 'rain')" data-layer="rain">
                Precipitation
              </button>
              <button type="button" class="map-layer-btn px-3 py-1 text-xs rounded border border-gray-300 text-gray-700 hover:bg-gray-100" 
                onclick="changeMapLayer(${id}, 'clouds')" data-layer="clouds">
                Clouds
              </button>
              <button type="button" class="map-layer-btn px-3 py-1 text-xs rounded border border-gray-300 text-gray-700 hover:bg-gray-100" 
                onclick="changeMapLayer(${id}, 'pressure')" data-layer="pressure">
                Pressure
              </button>
            </div>
            
            <!-- Map Container -->
            <div class="map-container bg-gray-100 rounded border border-gray-300 overflow-hidden" style="height: 300px; position: relative; z-index: 1;">
              <div id="weather-map-${id}" class="w-full h-full"></div>
              <div class="absolute top-2 left-2 bg-blue-600/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs text-white font-medium shadow-lg" id="layer-indicator-${id}">
                 Temperature Layer
              </div>
              <div class="absolute bottom-2 right-2 bg-white/90 backdrop-blur-sm px-2 py-1 rounded text-xs text-gray-600 shadow">
                <span class="map-coordinates">--</span>
              </div>
            </div>
            
            <!-- Weather Time Controls -->
            <div class="mt-3 p-3 bg-gray-50 rounded-lg border border-gray-300">
              <div class="flex items-center justify-between mb-2">
                <label class="text-xs font-medium text-gray-800">Weather Timeline</label>
                <div class="flex gap-2">
                  <button type="button" 
                    onclick="resetWeatherTime(${id})"
                    class="px-2 py-1 bg-gray-500 text-white rounded text-xs hover:bg-gray-600"
                    title="Reset to current time">
                    Reset
                  </button>
                  <button type="button" 
                    onclick="toggleCompareMode(${id})"
                    class="px-2 py-1 bg-gray-500 text-white rounded text-xs hover:bg-gray-600"
                    id="compare-btn-${id}"
                    title="Compare time periods">
                    Compare
                  </button>
                </div>
                <span class="text-xs text-gray-700 px-2 py-1 bg-white rounded border" id="time-display-${id}">
                  Current
                </span>
              </div>
              
              <!-- Time Controls -->
              <div class="mb-2 flex gap-2">
                <button type="button" onclick="setTimeGranularity(${id}, 'hours')" 
                  class="time-granularity-btn text-xs px-2 py-1 rounded bg-blue-600 text-white border" 
                  data-dest="${id}" data-type="hours">
                  Hourly
                </button>
                <button type="button" onclick="setTimeGranularity(${id}, 'days')" 
                  class="time-granularity-btn text-xs px-2 py-1 rounded border border-gray-300 text-gray-700" 
                  data-dest="${id}" data-type="days">
                  Daily
                </button>
                <input type="date" 
                  class="text-xs px-2 py-1 rounded border border-gray-300 ml-auto"
                  id="date-picker-${id}"
                  onchange="jumpToDate(${id}, this.value)"
                  title="Jump to specific date">
              </div>
              
              <!-- Main Time Slider -->
              <div class="flex items-center gap-2 mb-2">
                <span class="text-xs text-gray-600 font-medium">Past</span>
                <input type="range" 
                  class="weather-time-slider flex-1 h-3 bg-gray-200 rounded-lg appearance-none cursor-pointer transition-all"
                  id="time-slider-${id}"
                  min="-120"
                  max="120"
                  value="0"
                  step="3"
                  data-granularity="hours"
                  oninput="updateWeatherTime(${id}, this.value)">
                <span class="text-xs text-gray-600 font-medium">Future</span>
              </div>
              
              <!-- Time Markers -->
              <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                <span id="past-marker-${id}">5 days ago</span>
                <span class="text-blue-600 font-bold">⬤ NOW</span>
                <span id="future-marker-${id}">5 days ahead</span>
              </div>
              
              <!-- Compare Mode (Hidden by default) -->
              <div class="hidden mt-3 p-2 bg-purple-50 rounded border border-purple-200" id="compare-mode-${id}">
                <div class="text-xs font-semibold text-purple-800 mb-2">📊 Comparison Mode</div>
                <div class="flex gap-2 items-center">
                  <div class="flex-1">
                    <label class="text-xs text-gray-600">Time A:</label>
                    <input type="range" 
                      class="weather-time-slider-compare w-full h-2 bg-purple-200 rounded-lg appearance-none cursor-pointer"
                      id="compare-slider-a-${id}"
                      min="-120"
                      max="120"
                      value="-24"
                      step="3"
                      oninput="updateCompareView(${id}, 'a', this.value)">
                    <div class="text-xs text-purple-600 mt-1" id="compare-time-a-${id}">1 day ago</div>
                  </div>
                  <span class="text-purple-600 font-bold">vs</span>
                  <div class="flex-1">
                    <label class="text-xs text-gray-600">Time B:</label>
                    <input type="range" 
                      class="weather-time-slider-compare w-full h-2 bg-purple-200 rounded-lg appearance-none cursor-pointer"
                      id="compare-slider-b-${id}"
                      min="-120"
                      max="120"
                      value="24"
                      step="3"
                      oninput="updateCompareView(${id}, 'b', this.value)">
                    <div class="text-xs text-purple-600 mt-1" id="compare-time-b-${id}">+1 day</div>
                  </div>
                </div>
                <button type="button" onclick="applyComparison(${id})" 
                  class="mt-2 w-full px-2 py-1 bg-purple-600 text-white rounded text-xs hover:bg-purple-700">
                  Apply Comparison
                </button>
              </div>
              
              <!-- Weather Alerts -->
              <div class="hidden mt-2 p-2 bg-red-50 border-l-4 border-red-500 rounded text-xs" id="weather-alert-${id}">
                <div class="flex items-center gap-2">
                  <span class="text-red-600 font-bold text-sm">⚠️</span>
                  <div>
                    <div class="text-red-800 font-bold">Weather Alert</div>
                    <div class="text-red-700" id="alert-message-${id}">Extreme weather expected</div>
                  </div>
                </div>
              </div>
              
              <div class="mt-2 p-2 bg-gray-50 rounded text-xs text-gray-600 border border-gray-200">
                <strong>Tips:</strong> Use hourly mode for precise forecasts, daily for trends. Compare mode analyzes weather changes between time periods.
              </div>
            </div>
            
            <div class="mt-2 text-xs text-gray-500 flex items-center justify-between">
              <span>Powered by OpenWeatherMap</span>
              <span class="map-address text-gray-600">--</span>
            </div>
            <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-xs text-yellow-700">
              ℹ️ <strong>Note:</strong> Weather overlays may appear faint or take time to load. The free API tier has limited tile access.
            </div>
          </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Attractions (comma separated)</label>
            <textarea class="destination-attractions mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
              rows="2" placeholder="e.g. Medina, Bardo Museum, Carthage Ruins" onchange="updateDestination(${id})"></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Activities (comma separated)</label>
            <textarea class="destination-activities mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
              rows="2" placeholder="e.g. Guided tours, Local cuisine, Shopping" onchange="updateDestination(${id})"></textarea>
          </div>
        </div>
        
        <div class="mt-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
          <textarea class="destination-description mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
            rows="2" placeholder="Describe what travelers will experience in this destination..." onchange="updateDestination(${id})"></textarea>
        </div>
      </div>
    `;
  }

  function removeDestination(id) {
    const element = document.querySelector(`[data-index="${id}"]`);
    if (element) {
      element.remove();
    }
    destinations = destinations.filter(dest => dest.id !== id);
    
    // Clean up map instance
    if (mapInstances[id]) {
      mapInstances[id].remove();
      delete mapInstances[id];
    }
    delete currentLayers[id];
    
    updateDurationDisplay();
  }

  function updateDestination(id) {
    const element = document.querySelector(`[data-index="${id}"]`);
    const dest = destinations.find(d => d.id === id);
    
    if (!dest || !element) return;

    const citySelect = element.querySelector('.destination-city');
    const newCity = citySelect?.value || '';
    const daysInput = element.querySelector('.destination-days');
    const daysValue = parseInt(daysInput?.value) || 1;
    
    dest.city = newCity;
    dest.days = daysValue > 0 ? daysValue : 1;
    dest.region = element.querySelector('.destination-region')?.value || '';
    
    const attractions = element.querySelector('.destination-attractions')?.value || '';
    dest.attractions = attractions ? attractions.split(',').map(s => s.trim()).filter(s => s) : [];
    
    const activities = element.querySelector('.destination-activities')?.value || '';
    dest.activities = activities ? activities.split(',').map(s => s.trim()).filter(s => s) : [];
    
    dest.description = element.querySelector('.destination-description')?.value || '';
    
    // Update climate data and map when city is selected
    if (newCity) {
      fetchClimateData(id, newCity);
      initializeWeatherMap(id, newCity);
    } else {
      // Hide widgets if no city selected
      const climateWidget = document.getElementById(`climate-${id}`);
      const mapWidget = document.getElementById(`map-${id}`);
      if (climateWidget) climateWidget.classList.add('hidden');
      if (mapWidget) mapWidget.classList.add('hidden');
    }
    
    updateDurationDisplay();
  }

  // Fetch real-time climate data for a city
  async function fetchClimateData(destinationId, cityName) {
    const climateWidget = document.getElementById(`climate-${destinationId}`);
    if (!climateWidget) return;

    climateWidget.classList.remove('hidden');
    climateWidget.querySelector('.climate-update-time').textContent = 'Loading...';
    
    try {
      const response = await fetch(`/api/weather/${encodeURIComponent(cityName)}`);
      
      if (!response.ok) {
        throw new Error('Weather data not available');
      }
      
      const data = await response.json();
      
      if (data.error) {
        throw new Error(data.message || data.error);
      }
      
      // Update the widget with real data
      const tempElement = climateWidget.querySelector('.climate-temp');
      const conditionElement = climateWidget.querySelector('.climate-condition');
      const humidityElement = climateWidget.querySelector('.climate-humidity');
      const windElement = climateWidget.querySelector('.climate-wind');
      const updateTimeElement = climateWidget.querySelector('.climate-update-time');
      const forecastElement = climateWidget.querySelector('.climate-forecast');
      
      if (tempElement) {
        tempElement.innerHTML = `<img src="https://openweathermap.org/img/wn/${data.icon}.png" class="inline w-12 h-12" alt="weather icon"> ${data.temperature}°C`;
      }
      if (conditionElement) conditionElement.textContent = data.condition;
      if (humidityElement) humidityElement.textContent = `Humidity: ${data.humidity}%`;
      if (windElement) windElement.textContent = `Wind: ${data.wind_speed} km/h`;
      if (updateTimeElement) updateTimeElement.textContent = `Updated at ${data.updated_at}`;
      if (forecastElement) {
        forecastElement.textContent = data.forecast || 'Forecast unavailable';
      }
      
    } catch (error) {
      console.error('Error fetching climate data:', error);
      // Fallback to generic message
      const climateWidget = document.getElementById(`climate-${destinationId}`);
      if (climateWidget) {
        climateWidget.querySelector('.climate-temp').textContent = 'N/A';
        climateWidget.querySelector('.climate-condition').textContent = 'Weather data unavailable';
        climateWidget.querySelector('.climate-humidity').textContent = 'Humidity: --';
        climateWidget.querySelector('.climate-wind').textContent = 'Wind: --';
        climateWidget.querySelector('.climate-forecast').textContent = 'Please check your internet connection';
        climateWidget.querySelector('.climate-update-time').textContent = 'Error loading data';
      }
    }
  }

  // Initialize interactive weather map for a city
  async function initializeWeatherMap(destinationId, cityName) {
    const mapWidget = document.getElementById(`map-${destinationId}`);
    const mapContainer = document.getElementById(`weather-map-${destinationId}`);
    
    if (!mapWidget || !mapContainer || !tunisianCities[cityName]) {
      console.warn('Map container or city not found:', cityName);
      return;
    }

    mapWidget.classList.remove('hidden');
    
    const cityData = tunisianCities[cityName];
    const { lat, lng } = cityData;

    // Update coordinates display
    const coordinatesElement = mapWidget.querySelector('.map-coordinates');
    const addressElement = mapWidget.querySelector('.map-address');
    if (coordinatesElement) coordinatesElement.textContent = `${lat.toFixed(4)}°N, ${lng.toFixed(4)}°E`;
    if (addressElement) addressElement.textContent = `${cityName}, ${cityData.region} Tunisia`;

    // Clear existing map if any
    if (mapInstances[destinationId]) {
      mapInstances[destinationId].remove();
    }

    // Wait for Leaflet to be loaded
    if (typeof L === 'undefined') {
      console.warn('Leaflet not loaded yet, waiting...');
      setTimeout(() => initializeWeatherMap(destinationId, cityName), 500);
      return;
    }

    try {
      // Create new map
      const map = L.map(`weather-map-${destinationId}`, {
        center: [lat, lng],
        zoom: 8, // Reduced from 10 to show wider area for better weather overlay visibility
        zoomControl: true,
        scrollWheelZoom: true
      });

      // Add base map (OpenStreetMap)
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
      }).addTo(map);

      // Add location marker
      const marker = L.marker([lat, lng]).addTo(map);
      marker.bindPopup(`<b>${cityName}</b><br>${cityData.region} Region`).openPopup();

      // Store map instance
      mapInstances[destinationId] = map;
      currentLayers[destinationId] = 'temp';

      // Add weather layer (temperature by default)
      await addWeatherLayer(destinationId, 'temp');

      // Force map to resize properly
      setTimeout(() => {
        map.invalidateSize();
      }, 100);

    } catch (error) {
      console.error('Error initializing map:', error);
      const addressElement = mapWidget.querySelector('.map-address');
      if (addressElement) addressElement.textContent = 'Map initialization failed';
    }
  }

  // Add weather overlay layer to map
  async function addWeatherLayer(destinationId, layerType) {
    const map = mapInstances[destinationId];
    if (!map) return;

    try {
      // Show loading indicator
      const indicator = document.getElementById(`layer-indicator-${destinationId}`);
      if (indicator) {
        indicator.innerHTML = '⏳ Loading...';
        indicator.classList.add('animate-pulse');
      }

      // Get API key from backend
      const response = await fetch('/api/weather/config');
      if (!response.ok) {
        throw new Error('Failed to get API configuration');
      }
      
      const config = await response.json();
      const apiKey = config.api_key;
      
      if (!apiKey || apiKey === 'demo') {
        throw new Error('Valid API key not configured');
      }

      // Remove existing weather layer if any
      if (map.weatherLayer) {
        map.removeLayer(map.weatherLayer);
      }

      // OpenWeatherMap layer types
      const layerUrls = {
        'temp': `https://tile.openweathermap.org/map/temp_new/{z}/{x}/{y}.png?appid=${apiKey}`,
        'wind': `https://tile.openweathermap.org/map/wind_new/{z}/{x}/{y}.png?appid=${apiKey}`,
        'rain': `https://tile.openweathermap.org/map/precipitation_new/{z}/{x}/{y}.png?appid=${apiKey}`,
        'clouds': `https://tile.openweathermap.org/map/clouds_new/{z}/{x}/{y}.png?appid=${apiKey}`,
        'pressure': `https://tile.openweathermap.org/map/pressure_new/{z}/{x}/{y}.png?appid=${apiKey}`
      };

    // Layer names for indicator
    const layerNames = {
      'temp': 'Temperature',
      'wind': 'Wind',
      'rain': 'Precipitation',
      'clouds': 'Cloud Cover',
      'pressure': 'Pressure'
    };      // Add new weather layer
      map.weatherLayer = L.tileLayer(layerUrls[layerType], {
        attribution: 'Weather data © OpenWeatherMap',
        opacity: 0.8, // Increased from 0.6 for better visibility
        maxZoom: 19
      }).addTo(map);

      currentLayers[destinationId] = layerType;
      
      // Update indicator on success
      if (indicator) {
        indicator.classList.remove('animate-pulse');
        indicator.textContent = layerNames[layerType] || 'Weather Layer';
      }
      
    } catch (error) {
      console.error('Error adding weather layer:', error);
      
      // Show error in indicator
      const indicator = document.getElementById(`layer-indicator-${destinationId}`);
      if (indicator) {
        indicator.classList.remove('animate-pulse');
        indicator.innerHTML = '⚠️ Layer Unavailable';
        indicator.classList.remove('bg-blue-600/90');
        indicator.classList.add('bg-red-600/90');
        
        // Reset after 3 seconds
        setTimeout(() => {
          indicator.classList.remove('bg-red-600/90');
          indicator.classList.add('bg-blue-600/90');
        }, 3000);
      }
    }
  }

  // Change map layer
  function changeMapLayer(destinationId, layerType) {
    const mapWidget = document.getElementById(`map-${destinationId}`);
    if (!mapWidget) return;

    // Update button styles
    const buttons = mapWidget.querySelectorAll('.map-layer-btn');
    buttons.forEach(btn => {
      if (btn.dataset.layer === layerType) {
        btn.className = 'map-layer-btn px-3 py-1 text-xs rounded-full border transition-all bg-blue-500 text-white border-blue-500';
      } else {
        btn.className = 'map-layer-btn px-3 py-1 text-xs rounded-full border transition-all border-gray-300 text-gray-700 hover:border-blue-300';
      }
    });

    // Update layer indicator
    const layerNames = {
      'temp': ' Temperature Layer',
      'wind': '💨 Wind Layer',
      'rain': '🌧️ Precipitation Layer',
      'clouds': '☁️ Cloud Cover',
      'pressure': '🔽 Pressure Layer'
    };
    
    const indicator = document.getElementById(`layer-indicator-${destinationId}`);
    if (indicator) {
      indicator.textContent = layerNames[layerType] || 'Weather Layer';
    }

    // Change the weather layer
    addWeatherLayer(destinationId, layerType);
  }

  // Toggle fullscreen map
  function toggleMapFullscreen(destinationId) {
    const mapWidget = document.getElementById(`map-${destinationId}`);
    if (!mapWidget) return;

    const mapContainer = mapWidget.querySelector('.map-container');
    const map = mapInstances[destinationId];
    
    if (!mapContainer || !map) return;

    if (mapContainer.style.height === '300px') {
      mapContainer.style.height = '500px';
    } else {
      mapContainer.style.height = '300px';
    }
    
    setTimeout(() => {
      map.invalidateSize();
    }, 100);
  }

  // Update weather time based on slider (wrapper for enhanced version)
  async function updateWeatherTime(destinationId, daysOffset) {
    await updateWeatherTimeEnhanced(destinationId, daysOffset);
  }

  // Update climate widget with forecast data
  async function updateForecastClimate(destinationId, cityName, daysAhead) {
    const climateWidget = document.getElementById(`climate-${destinationId}`);
    if (!climateWidget) return;

    climateWidget.querySelector('.climate-update-time').textContent = 'Loading forecast...';
    
    try {
      const response = await fetch(`/api/weather/${encodeURIComponent(cityName)}`);
      
      if (!response.ok) {
        throw new Error('Weather forecast not available');
      }
      
      const data = await response.json();
      
      // Update widget with forecast indicator
      const tempElement = climateWidget.querySelector('.climate-temp');
      const conditionElement = climateWidget.querySelector('.climate-condition');
      const updateTimeElement = climateWidget.querySelector('.climate-update-time');
      const forecastElement = climateWidget.querySelector('.climate-forecast');
      
      if (tempElement) {
        tempElement.innerHTML = `<img src="https://openweathermap.org/img/wn/${data.icon}.png" class="inline w-12 h-12" alt="weather icon"> ${data.temperature}°C`;
      }
      if (conditionElement) conditionElement.textContent = `${data.condition} (Current)`;
      if (updateTimeElement) updateTimeElement.textContent = `Forecast for +${daysAhead} day${daysAhead > 1 ? 's' : ''}`;
      if (forecastElement) {
        forecastElement.innerHTML = `<strong>Extended Forecast:</strong> Detailed ${daysAhead}-day forecast requires OpenWeatherMap Pro API. Current forecast shows next 24-48 hours.`;
      }
      
    } catch (error) {
      console.error('Error fetching forecast:', error);
      const climateWidget = document.getElementById(`climate-${destinationId}`);
      if (climateWidget) {
        climateWidget.querySelector('.climate-forecast').innerHTML = 'Extended forecast unavailable with free API tier';
      }
    }
  }

  // Show notice for historical data
  function showHistoricalDataNotice(destinationId, daysBack) {
    const climateWidget = document.getElementById(`climate-${destinationId}`);
    if (!climateWidget) return;

    const forecastElement = climateWidget.querySelector('.climate-forecast');
    const updateTimeElement = climateWidget.querySelector('.climate-update-time');
    
    if (updateTimeElement) {
      updateTimeElement.textContent = `${Math.abs(daysBack)} day${Math.abs(daysBack) > 1 ? 's' : ''} ago`;
    }
    if (forecastElement) {
      forecastElement.innerHTML = `<strong>Historical Data:</strong> Historical weather data requires OpenWeatherMap's One Call API 3.0 (paid plan starting at $180/month for time machine access).`;
    }
  }

  // Reset weather time to current
  function resetWeatherTime(destinationId) {
    const slider = document.getElementById(`time-slider-${destinationId}`);
    if (slider) {
      slider.value = 0;
      updateWeatherTime(destinationId, 0);
    }
  }

  // Set time granularity (hours or days)
  function setTimeGranularity(destinationId, type) {
    const slider = document.getElementById(`time-slider-${destinationId}`);
    const pastMarker = document.getElementById(`past-marker-${destinationId}`);
    const futureMarker = document.getElementById(`future-marker-${destinationId}`);
    const buttons = document.querySelectorAll(`[data-dest="${destinationId}"].time-granularity-btn`);
    
    // Update button styles
    buttons.forEach(btn => {
      if (btn.dataset.type === type) {
        btn.className = 'time-granularity-btn text-xs px-2 py-1 rounded bg-blue-500 text-white shadow-sm';
      } else {
        btn.className = 'time-granularity-btn text-xs px-2 py-1 rounded bg-gray-300 text-gray-700';
      }
    });
    
    if (type === 'hours') {
      slider.min = -120;
      slider.max = 120;
      slider.step = 3;
      slider.dataset.granularity = 'hours';
      if (pastMarker) pastMarker.textContent = '5 days (120h) ago';
      if (futureMarker) futureMarker.textContent = '5 days (120h) ahead';
    } else {
      slider.min = -5;
      slider.max = 5;
      slider.step = 1;
      slider.dataset.granularity = 'days';
      if (pastMarker) pastMarker.textContent = '5 days ago';
      if (futureMarker) futureMarker.textContent = '5 days ahead';
    }
    
    slider.value = 0;
    updateWeatherTime(destinationId, 0);
  }

  // Jump to specific date
  function jumpToDate(destinationId, dateStr) {
    if (!dateStr) return;
    
    const targetDate = new Date(dateStr);
    const currentDate = new Date();
    const diffTime = targetDate - currentDate;
    const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));
    
    const slider = document.getElementById(`time-slider-${destinationId}`);
    const granularity = slider?.dataset.granularity || 'days';
    
    if (granularity === 'hours') {
      const diffHours = Math.round(diffTime / (1000 * 60 * 60));
      slider.value = Math.max(-120, Math.min(120, diffHours));
      updateWeatherTime(destinationId, slider.value);
    } else {
      slider.value = Math.max(-5, Math.min(5, diffDays));
      updateWeatherTime(destinationId, slider.value);
    }
  }

  // Toggle compare mode
  function toggleCompareMode(destinationId) {
    const compareDiv = document.getElementById(`compare-mode-${destinationId}`);
    const compareBtn = document.getElementById(`compare-btn-${destinationId}`);
    
    if (!compareDiv || !compareBtn) return;
    
    if (compareDiv.classList.contains('hidden')) {
      compareDiv.classList.remove('hidden');
      compareBtn.textContent = 'Close';
      compareBtn.classList.add('bg-gray-600');
      compareBtn.classList.remove('bg-gray-500');
    } else {
      compareDiv.classList.add('hidden');
      compareBtn.textContent = 'Compare';
      compareBtn.classList.remove('bg-gray-600');
      compareBtn.classList.add('bg-gray-500');
    }
  }

  // Update compare view
  function updateCompareView(destinationId, which, offset) {
    const timeDisplay = document.getElementById(`compare-time-${which}-${destinationId}`);
    if (!timeDisplay) return;
    
    const value = parseInt(offset);
    const slider = document.getElementById(`time-slider-${destinationId}`);
    const granularity = slider?.dataset.granularity || 'days';
    
    let displayText = '';
    if (granularity === 'hours') {
      const hours = Math.abs(value);
      const days = Math.floor(hours / 24);
      const remainingHours = hours % 24;
      
      if (value === 0) {
        displayText = 'Now';
      } else if (value < 0) {
        displayText = days > 0 ? `${days}d ${remainingHours}h ago` : `${hours}h ago`;
      } else {
        displayText = days > 0 ? `+${days}d ${remainingHours}h` : `+${hours}h`;
      }
    } else {
      if (value === 0) {
        displayText = 'Now';
      } else if (value < 0) {
        displayText = `${Math.abs(value)} day${Math.abs(value) > 1 ? 's' : ''} ago`;
      } else {
        displayText = `+${value} day${value > 1 ? 's' : ''}`;
      }
    }
    
    timeDisplay.textContent = displayText;
  }

  // Apply comparison
  async function applyComparison(destinationId) {
    const sliderA = document.getElementById(`compare-slider-a-${destinationId}`);
    const sliderB = document.getElementById(`compare-slider-b-${destinationId}`);
    
    if (!sliderA || !sliderB) return;
    
    const offsetA = parseInt(sliderA.value);
    const offsetB = parseInt(sliderB.value);
    
    const dest = destinations.find(d => d.id === destinationId);
    if (!dest || !dest.city) return;
    
    // Show loading
    const indicator = document.getElementById(`layer-indicator-${destinationId}`);
    if (indicator) {
      indicator.innerHTML = 'Comparing weather data...';
      indicator.classList.add('animate-pulse');
    }
    
    // Simulate comparison (in production, fetch both datasets)
    setTimeout(() => {
      if (indicator) {
        indicator.classList.remove('animate-pulse');
        indicator.innerHTML = `Comparison: ${formatOffset(offsetA)} vs ${formatOffset(offsetB)}`;
      }
      
      // Show alert if extreme difference
      checkWeatherAlert(destinationId, offsetA, offsetB);
    }, 1000);
  }

  // Format offset for display
  function formatOffset(offset) {
    if (offset === 0) return 'Now';
    if (offset < 0) return `${Math.abs(offset)}h ago`;
    return `+${offset}h`;
  }

  // Check for weather alerts
  function checkWeatherAlert(destinationId, offsetA, offsetB) {
    const alertDiv = document.getElementById(`weather-alert-${destinationId}`);
    const alertMessage = document.getElementById(`alert-message-${destinationId}`);
    
    if (!alertDiv || !alertMessage) return;
    
    // Simulate alert check (in production, check actual weather data)
    const timeDiff = Math.abs(offsetA - offsetB);
    
    if (timeDiff > 48) {
      alertDiv.classList.remove('hidden');
      alertMessage.textContent = `Large time difference detected (${timeDiff}h). Weather conditions may vary significantly.`;
      
      // Auto-hide after 5 seconds
      setTimeout(() => {
        alertDiv.classList.add('hidden');
      }, 5000);
    }
  }

  // Enhanced updateWeatherTime with animations
  async function updateWeatherTimeEnhanced(destinationId, offsetValue) {
    const timeDisplay = document.getElementById(`time-display-${destinationId}`);
    const map = mapInstances[destinationId];
    
    if (!timeDisplay || !map) return;
    
    const slider = document.getElementById(`time-slider-${destinationId}`);
    const granularity = slider?.dataset.granularity || 'days';
    
    const offset = parseInt(offsetValue);
    const currentDate = new Date();
    const targetDate = new Date(currentDate);
    
    // Calculate target date based on granularity
    if (granularity === 'hours') {
      targetDate.setHours(currentDate.getHours() + offset);
    } else {
      targetDate.setDate(currentDate.getDate() + offset);
    }
    
    // Format the display text
    let displayText = '';
    
    if (offset === 0) {
      displayText = 'Current';
    } else if (offset < 0) {
      if (granularity === 'hours') {
        const hours = Math.abs(offset);
        const days = Math.floor(hours / 24);
        const remainingHours = hours % 24;
        displayText = days > 0 ? 
          `${days}d ${remainingHours}h ago` : 
          `${hours}h ago`;
      } else {
        displayText = `${Math.abs(offset)} day${Math.abs(offset) > 1 ? 's' : ''} ago`;
      }
    } else {
      if (granularity === 'hours') {
        const hours = offset;
        const days = Math.floor(hours / 24);
        const remainingHours = hours % 24;
        displayText = days > 0 ? 
          `+${days}d ${remainingHours}h` : 
          `+${hours}h`;
      } else {
        displayText = `+${offset} day${offset > 1 ? 's' : ''}`;
      }
    }
    
    // Add the actual date/time
    const dateStr = targetDate.toLocaleDateString('en-US', { 
      month: 'short', 
      day: 'numeric',
      hour: granularity === 'hours' ? '2-digit' : undefined,
      minute: granularity === 'hours' ? '2-digit' : undefined
    });
    displayText += ` • ${dateStr}`;
    
    // Animate the change
    timeDisplay.style.transform = 'scale(1.1)';
    timeDisplay.textContent = displayText;
    setTimeout(() => {
      timeDisplay.style.transform = 'scale(1)';
    }, 200);
    
    // Rest of the update logic...
    const dest = destinations.find(d => d.id === destinationId);
    if (!dest || !dest.city) return;
    
    if (offset > 0 && Math.abs(offset) <= (granularity === 'hours' ? 120 : 5)) {
      await updateForecastClimate(destinationId, dest.city, offset);
    } else if (offset === 0) {
      await fetchClimateData(destinationId, dest.city);
    } else if (offset < 0) {
      showHistoricalDataNotice(destinationId, offset);
    }
  }

  function generateReview() {
    const title = document.querySelector('[name="title"]')?.value || '';
    const basePrice = document.querySelector('[name="base_price"]')?.value || '0';
    const schedule = document.querySelector('[name="schedule"]')?.value || '';
    const totalDays = document.getElementById('total_days')?.value || '0';
    const startDate = document.querySelector('[name="start_date"]')?.value || '';
    const description = document.querySelector('[name="description"]')?.value || '';
    
    const totalPrice = Math.round(parseFloat(basePrice) * parseInt(totalDays));
    
    let reviewHtml = `
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">${title}</h3>
          <p class="text-sm text-gray-600 mb-2"><strong>Type:</strong> ${schedule}</p>
          <p class="text-sm text-gray-600 mb-2"><strong>Duration:</strong> ${totalDays} days</p>
          <p class="text-sm text-gray-600 mb-2"><strong>Price:</strong> TN ${totalPrice}</p>
          <p class="text-sm text-gray-600 mb-2"><strong>Start Date:</strong> ${startDate}</p>
        </div>
        <div>
          <h4 class="font-medium text-gray-900 mb-2">Description</h4>
          <p class="text-sm text-gray-600">${description}</p>
        </div>
      </div>
      
      <h4 class="font-medium text-gray-900 mb-3">Itinerary</h4>
      <div class="space-y-3">
    `;
    
    destinations.forEach((dest, index) => {
      reviewHtml += `
        <div class="border border-gray-200 rounded-lg p-3">
          <div class="flex justify-between items-start mb-2">
            <h5 class="font-medium text-gray-900">${dest.city} ${dest.region ? `(${dest.region})` : ''}</h5>
            <span class="text-sm text-blue-600 font-medium">${dest.days} days</span>
          </div>
          ${dest.attractions.length > 0 ? `<p class="text-sm text-gray-600 mb-1"><strong>Attractions:</strong> ${dest.attractions.join(', ')}</p>` : ''}
          ${dest.activities.length > 0 ? `<p class="text-sm text-gray-600 mb-1"><strong>Activities:</strong> ${dest.activities.join(', ')}</p>` : ''}
          ${dest.description ? `<p class="text-sm text-gray-600">${dest.description}</p>` : ''}
        </div>
      `;
    });
    
    reviewHtml += `</div>`;
    
    const reviewElement = document.getElementById('tour-review');
    if (reviewElement) {
      reviewElement.innerHTML = reviewHtml;
    }
  }

  // Initialize
  document.addEventListener('DOMContentLoaded', function() {
    updateDurationDisplay();
    
    // Listen for total days changes
    const totalDaysInput = document.getElementById('total_days');
    if (totalDaysInput) {
      totalDaysInput.addEventListener('input', updateDurationDisplay);
    }
  });

  // ========================================
  // AI RECOMMENDATIONS SYSTEM
  // ========================================

  let currentAIDestination = null;

  // Show AI recommendations modal
  function showAIRecommendations(destinationId) {
    const dest = destinations.find(d => d.id === destinationId);
    if (!dest || !dest.city) {
      alert('Please select a city first!');
      return;
    }

    currentAIDestination = dest;
    const modal = document.getElementById('ai-modal');
    const destName = document.getElementById('ai-destination-name');
    const loading = document.getElementById('ai-loading');
    const content = document.getElementById('ai-content');

    // Show modal with animation
    modal.style.display = 'flex';
    setTimeout(() => {
      modal.style.opacity = '0';
      modal.style.opacity = '1';
    }, 10);

    // Update destination name
    if (destName) {
      destName.textContent = `${dest.city}, Tunisia`;
    }

    // Show loading, hide content
    loading.classList.remove('hidden');
    content.classList.add('hidden');

    // Simulate AI analysis with delay
    setTimeout(() => {
      generateAIRecommendations(dest);
      loading.classList.add('hidden');
      content.classList.remove('hidden');
      
      // Animate content appearance
      content.style.opacity = '0';
      content.style.transform = 'translateY(20px)';
      setTimeout(() => {
        content.style.transition = 'all 0.5s ease';
        content.style.opacity = '1';
        content.style.transform = 'translateY(0)';
      }, 50);
    }, 2500); // 2.5 seconds "AI processing"
  }

  // Generate AI recommendations based on city
  function generateAIRecommendations(dest) {
    const cityData = getAIDataForCity(dest.city);
    
    // Update overview
    document.getElementById('ai-overview').textContent = cityData.overview;
    
    // Update seasons
    document.getElementById('ai-seasons').innerHTML = cityData.seasons;
    
    // Update trends
    document.getElementById('ai-trends').textContent = cityData.trends;
    
    // Update recommendations
    document.getElementById('ai-recommendations').innerHTML = cityData.recommendations
      .map(rec => `<li class="flex items-start gap-2"><span class="text-purple-600">✓</span><span>${rec}</span></li>`)
      .join('');
    
    // Update warnings
    document.getElementById('ai-warnings').textContent = cityData.warnings;
  }

  // Get AI data for specific cities (demo data)
  function getAIDataForCity(cityName) {
    const aiDatabase = {
      'Tunis': {
        overview: 'Over the past 20 years, Tunis has experienced a moderate warming trend of +0.8°C. Average rainfall has decreased by 12%, with longer dry spells in summer. The city enjoys 300+ sunny days annually, making it ideal for tourism year-round.',
        seasons: '<strong>🌸 Spring (March-May):</strong> Perfect! 18-25°C, mild and pleasant<br><strong>☀️ Summer (June-Aug):</strong> Hot 28-35°C, low rainfall<br><strong>🍂 Fall (Sept-Nov):</strong> Excellent! 20-27°C, comfortable<br><strong>❄️ Winter (Dec-Feb):</strong> Mild 10-16°C, occasional rain',
        trends: 'Temperature has risen consistently (+0.04°C/year). Rainfall patterns show increasing variability. Heat waves have become 15% more frequent since 2010. Sea levels have risen 3mm/year affecting coastal areas.',
        recommendations: [
          'Best visit: March-May or September-November for optimal weather',
          'Avoid mid-July to August for extreme heat (35°C+)',
          'Pack sunscreen and light clothing - UV index peaks at 11',
          'Evening activities recommended during summer months',
          'Spring offers best balance of weather and lower tourist crowds'
        ],
        warnings: 'Occasional sandstorms from the Sahara (Sirocco) in spring/summer. Heat advisories common in July-August. Minimal flooding risk. No significant extreme weather patterns detected.'
      },
      'Sousse': {
        overview: 'Sousse, a Mediterranean coastal gem, has warmed by +0.9°C over 20 years. Sea temperatures have risen 1.2°C, extending the beach season. Rainfall reduced by 15%, creating drier summers perfect for beach tourism.',
        seasons: '<strong>🌸 Spring (March-May):</strong> Ideal! 19-24°C, gentle sea breeze<br><strong>☀️ Summer (June-Aug):</strong> Beach perfect! 27-33°C, warm waters<br><strong>🍂 Fall (Sept-Nov):</strong> Outstanding! 22-28°C, extended summer<br><strong>❄️ Winter (Dec-Feb):</strong> Cool 11-15°C, windy',
        trends: 'Mediterranean influence moderating temperature extremes. Sea temperature +1.2°C enables year-round water activities. Coastal erosion increased 8% due to rising seas. Storm frequency unchanged.',
        recommendations: [
          'Peak beach season: June-September with 28°C+ water',
          'Best value: April-May or October for great weather, fewer crowds',
          'Wind conditions perfect for water sports in spring',
          'Winter ideal for cultural tours without summer heat',
          'UV protection essential - coastal reflection intensifies sun'
        ],
        warnings: 'Strong coastal winds (15-25 km/h) common in winter. Jellyfish presence increases in late summer. Minor beach closures possible during storms. Generally very safe climate for tourism.'
      },
      'Hammamet': {
        overview: 'Hammamet, Tunisia\'s premier resort destination, shows +0.7°C warming trend. Microclimate benefits from Mediterranean influence. 315 sunny days/year. Rainfall stable, making it one of Tunisia\'s most reliable weather destinations.',
        seasons: '<strong>🌸 Spring (March-May):</strong> Excellent! 17-23°C, blooming gardens<br><strong>☀️ Summer (June-Aug):</strong> Resort perfect! 26-32°C, consistent<br><strong>🍂 Fall (Sept-Nov):</strong> Premium! 21-27°C, golden beaches<br><strong>❄️ Winter (Dec-Feb):</strong> Mild 9-14°C, peaceful',
        trends: 'Most stable climate in Tunisia over 20 years. Tourism extended by 6 weeks due to warmer fall. Humidity decreased 8%, improving comfort. Coastal development affecting local microclimate minimally.',
        recommendations: [
          'Year-round destination - even winter rarely drops below 8°C',
          'September-October offers summer weather with off-peak prices',
          'Golf season: October-May with perfect temperatures',
          'Family-friendly: consistent weather reduces trip uncertainty',
          'Thalassotherapy best in shoulder seasons (May, October)'
        ],
        warnings: 'Virtually no extreme weather events recorded. Occasional fog in winter mornings. Minimal risks - one of Tunisia\'s safest climate zones. UV index high year-round - constant sun protection needed.'
      },
      'Djerba': {
        overview: 'Djerba, the island paradise, has experienced +1.1°C warming - highest in Tunisia. 330 sunny days annually. Desert influence creates unique microclimate. Rainfall 40% below national average, ensuring dry, hot conditions.',
        seasons: '<strong>🌸 Spring (March-May):</strong> Perfect! 20-26°C, desert blooms<br><strong>☀️ Summer (June-Aug):</strong> Very hot! 30-38°C, intense sun<br><strong>🍂 Fall (Sept-Nov):</strong> Ideal! 24-30°C, warm seas<br><strong>❄️ Winter (Dec-Feb):</strong> Pleasant 12-18°C, sunny',
        trends: 'Saharan influence increasing - 22% more hot days (35°C+) since 2010. Sea level rise of 4mm/year affecting low-lying areas. Wind patterns shifting, reducing cooling effect. Drought cycles intensifying.',
        recommendations: [
          'Avoid July-August unless you love extreme heat (38°C+)',
          'Best months: March-April and October-November',
          'Winter escape destination - warmest Tunisian winter',
          'Flamingo watching peak: October-March in lagoons',
          'Hydration critical - bring 3L water/person in summer'
        ],
        warnings: 'Extreme heat warnings common June-September. Sandstorms possible (3-5 events/year). Strong winds can disrupt ferry services. Heat exhaustion risk HIGH in summer. Adequate sun protection essential.'
      },
      'Tozeur': {
        overview: 'Tozeur, gateway to the Sahara, shows dramatic +1.4°C warming. Desert climate with extreme temperature ranges (0°C winter nights to 45°C summer days). Rainfall decreased 25%, intensifying desert conditions. 350+ sunny days.',
        seasons: '<strong>🌸 Spring (March-May):</strong> Excellent! 22-32°C, desert in bloom<br><strong>☀️ Summer (June-Aug):</strong> EXTREME! 38-46°C, dangerous heat<br><strong>🍂 Fall (Sept-Nov):</strong> Good! 26-36°C, cooling trend<br><strong>❄️ Winter (Dec-Feb):</strong> Mild days 14-20°C, cold nights',
        trends: 'Accelerating desertification. Summer temperatures breaking records (46.8°C in 2023). Night temperatures failing to cool below 28°C in peak summer. Oasis water levels declining 2% annually.',
        recommendations: [
          'ONLY visit October-April - summer is dangerous',
          'Best: November-March for comfortable exploration',
          'Desert safaris: Dawn/dusk only in summer',
          'Star Wars filming locations best in winter',
          'Thermal springs perfect in cooler months'
        ],
        warnings: 'EXTREME HEAT DANGER June-September. Temperatures regularly exceed 42°C. Heat stroke risk SEVERE. Sandstorms frequent in spring (5-8 events). Night temperature drops can reach 15°C - pack layers.'
      },
      'Kairouan': {
        overview: 'Kairouan, the sacred city, has warmed +1.0°C over 20 years. Continental climate with hot summers and cool winters. Rainfall reduced 18%. Dust storms increasing due to desertification spreading from the south.',
        seasons: '<strong>🌸 Spring (March-May):</strong> Beautiful! 18-28°C, pilgrimage season<br><strong>☀️ Summer (June-Aug):</strong> Hot & dry! 32-40°C, intense<br><strong>🍂 Fall (Sept-Nov):</strong> Pleasant! 22-30°C, harvest time<br><strong>❄️ Winter (Dec-Feb):</strong> Cool 8-14°C, rainy periods',
        trends: 'Increasing aridity - summer drought periods extended by 3 weeks. Winter rainfall becoming more variable. Heat waves 18% more frequent. Traditional agriculture challenged by water scarcity.',
        recommendations: [
          'Cultural tours best: March-May and October-November',
          'Great Mosque visits: Avoid Friday prayers and summer heat',
          'Ramadan dates shift yearly - check for altered schedules',
          'Photography: Golden hour light spectacular year-round',
          'Carpet shopping: Better prices in off-season (Nov-Feb)'
        ],
        warnings: 'Summer temperatures can exceed 40°C. Dust storms possible March-June. Limited shade in historical areas - heat exhaustion risk. Winter nights can drop to 2°C. Adequate water essential.'
      },
      'Bizerte': {
        overview: 'Bizerte, the northern coastal jewel, has moderate +0.6°C warming - lowest in Tunisia due to Mediterranean moderating effect. Rainfall stable. Strong maritime influence creates mild, consistent climate. 290 sunny days annually.',
        seasons: '<strong>🌸 Spring (March-May):</strong> Fresh! 15-21°C, wildflower season<br><strong>☀️ Summer (June-Aug):</strong> Warm 24-30°C, perfect beaches<br><strong>🍂 Fall (Sept-Nov):</strong> Lovely! 18-25°C, calm seas<br><strong>❄️ Winter (Dec-Feb):</strong> Cool 8-13°C, wettest season',
        trends: 'Most stable climate in North Africa. Temperature extremes rare. Rainfall patterns unchanged - reliability exceptional. Fish populations stable due to consistent water temperatures. Tourism season extending.',
        recommendations: [
          'Beach season: June-September with comfortable 26-28°C',
          'Sailing ideal: May-October with favorable winds',
          'Birdwatching: March-April migration spectacular',
          'Winter escape from northern Europe - mild and authentic',
          'Fresh seafood year-round - fishing traditions thrive'
        ],
        warnings: 'Minimal weather risks - Tunisia\'s safest destination. Winter brings occasional rain (10-12 days/month). Strong winds possible in spring. Maritime conditions generally excellent. Very tourist-friendly climate.'
      },
      'Tabarka': {
        overview: 'Tabarka, the coral coast, shows +0.8°C warming with increasing Mediterranean influence. Mountainous terrain creates unique microclimates. Highest rainfall in Tunisia (600mm/year). Lush, green environment contrasts with southern regions.',
        seasons: '<strong>🌸 Spring (March-May):</strong> Stunning! 16-22°C, green mountains<br><strong>☀️ Summer (June-Aug):</strong> Perfect! 25-31°C, diving season<br><strong>🍂 Fall (Sept-Nov):</strong> Excellent! 19-26°C, jazz festival<br><strong>❄️ Winter (Dec-Feb):</strong> Wet 9-14°C, misty mountains',
        trends: 'Rainfall patterns shifting - more intense storms, longer dry spells. Cork oak forests thriving due to temperature increase. Coastal erosion minimal. Biodiversity increasing as climate warms.',
        recommendations: [
          'Diving paradise: May-October with 22-26°C water',
          'Hiking: March-May when mountains are emerald green',
          'Jazz Festival: July - book accommodation early',
          'Golf: Year-round with stunning mountain course',
          'Avoid January-February - wettest, coldest period'
        ],
        warnings: 'Winter rainfall can be heavy (15+ rainy days/month). Mountain fog reduces visibility. Sea conditions rough in winter. Flash floods possible in valleys during storms. Summer generally very safe.'
      }
    };

    // Default data for cities not in database
    const defaultData = {
      overview: `${cityName} has experienced typical Mediterranean climate trends over the past 20 years, with moderate warming (+0.7-1.0°C) and changing rainfall patterns. The region maintains its characteristic sunny disposition with 280-310 days of sunshine annually.`,
      seasons: '<strong>🌸 Spring:</strong> Pleasant temperatures, ideal for sightseeing<br><strong>☀️ Summer:</strong> Hot and dry, perfect for beach activities<br><strong>🍂 Fall:</strong> Comfortable weather, fewer crowds<br><strong>❄️ Winter:</strong> Mild with occasional rain',
      trends: 'Regional warming trends align with Mediterranean basin averages. Seasonal variability increasing. Tourism season gradually extending into shoulder months due to milder temperatures.',
      recommendations: [
        'Spring (March-May) offers the best balance of weather and comfort',
        'Summer is hot but ideal for sun and beach enthusiasts',
        'Fall provides excellent conditions with authentic experiences',
        'Pack sun protection regardless of season',
        'Check local weather forecasts before departure'
      ],
      warnings: 'Standard Mediterranean climate considerations apply. Summer heat can be intense. Pack accordingly for temperature variations between day and night. Overall, a very manageable and tourist-friendly climate.'
    };

    return aiDatabase[cityName] || defaultData;
  }

  // Close AI modal
  function closeAIModal(event) {
    const modal = document.getElementById('ai-modal');
    if (event && event.target !== modal) return;
    
    modal.style.opacity = '0';
    setTimeout(() => {
      modal.style.display = 'none';
    }, 300);
  }

  // ========================================
  // OVERALL ITINERARY OPTIMIZER
  // ========================================

  let recommendedOrder = [];

  // Show overall recommendations modal
  function showOverallRecommendations() {
    if (destinations.length === 0) {
      alert('Please add at least one destination first!');
      return;
    }

    if (destinations.some(d => !d.city)) {
      alert('Please select cities for all destinations before optimizing!');
      return;
    }

    const modal = document.getElementById('overall-ai-modal');
    const loading = document.getElementById('overall-ai-loading');
    const content = document.getElementById('overall-ai-content');

    // Show modal with animation
    modal.style.display = 'flex';
    setTimeout(() => {
      modal.style.opacity = '0';
      modal.style.opacity = '1';
    }, 10);

    // Show loading, hide content
    loading.classList.remove('hidden');
    content.classList.add('hidden');

    // Simulate AI analysis with delay
    setTimeout(() => {
      generateOverallRecommendations();
      loading.classList.add('hidden');
      content.classList.remove('hidden');
      
      // Animate content appearance
      content.style.opacity = '0';
      content.style.transform = 'translateY(20px)';
      setTimeout(() => {
        content.style.transition = 'all 0.5s ease';
        content.style.opacity = '1';
        content.style.transform = 'translateY(0)';
      }, 50);
    }, 3000); // 3 seconds "AI processing"
  }

  // Generate overall recommendations
  function generateOverallRecommendations() {
    // Analyze destinations and create optimal order
    const analyzed = destinations.map(dest => {
      const cityInfo = tunisianCities[dest.city];
      return {
        ...dest,
        lat: cityInfo.lat,
        lng: cityInfo.lng,
        region: cityInfo.region,
        score: calculateOptimalityScore(dest, cityInfo)
      };
    });

    // Optimize order based on:
    // 1. Geographic flow (reduce backtracking)
    // 2. Climate progression (mild -> hot or reverse)
    // 3. Regional clustering
    recommendedOrder = optimizeOrder(analyzed);

    // Display current order
    displayCurrentOrder();
    
    // Display recommended order
    displayRecommendedOrder();
    
    // Generate insights
    generateOptimizationInsights();
  }

  // Calculate optimality score for a destination
  function calculateOptimalityScore(dest, cityInfo) {
    let score = 0;
    
    // Regional bonus
    if (cityInfo.region === 'North') score += 10; // Start north
    if (cityInfo.region === 'Center') score += 5;
    if (cityInfo.region === 'South') score += 2; // End south
    
    // Coastal vs inland
    const coastalCities = ['Sousse', 'Hammamet', 'Djerba', 'Bizerte', 'Monastir', 'Mahdia', 'Tabarka', 'Nabeul', 'Sfax', 'Gabes'];
    if (coastalCities.includes(dest.city)) score += 3;
    
    return score;
  }

  // Optimize destination order
  function optimizeOrder(analyzed) {
    // Strategy: North to South flow with regional clustering
    const regions = { 'North': [], 'Center': [], 'South': [] };
    
    analyzed.forEach(dest => {
      regions[dest.region].push(dest);
    });
    
    // Order: North -> Center -> South
    let optimized = [];
    
    // Sort within each region by longitude (west to east)
    ['North', 'Center', 'South'].forEach(region => {
      const sorted = regions[region].sort((a, b) => a.lng - b.lng);
      optimized = optimized.concat(sorted);
    });
    
    return optimized;
  }

  // Display current order
  function displayCurrentOrder() {
    const container = document.getElementById('overall-current-order');
    let html = '';
    
    destinations.forEach((dest, index) => {
      const cityInfo = tunisianCities[dest.city];
      html += `
        <div class="flex items-center gap-2 p-2 bg-white rounded border border-gray-200">
          <span class="flex-shrink-0 w-6 h-6 bg-gray-400 text-white rounded-full flex items-center justify-center text-xs font-bold">${index + 1}</span>
          <div class="flex-1">
            <div class="font-medium text-gray-900">${dest.city}</div>
            <div class="text-xs text-gray-500">${cityInfo.region} • ${dest.days} day${dest.days > 1 ? 's' : ''}</div>
          </div>
        </div>
      `;
    });
    
    container.innerHTML = html;
  }

  // Display recommended order
  function displayRecommendedOrder() {
    const container = document.getElementById('overall-recommended-order');
    let html = '';
    
    recommendedOrder.forEach((dest, index) => {
      const moveIcon = getMovementIcon(dest.id, index);
      html += `
        <div class="flex items-center gap-2 p-2 bg-white rounded border-2 border-green-300 shadow-sm">
          <span class="flex-shrink-0 w-6 h-6 bg-green-600 text-white rounded-full flex items-center justify-center text-xs font-bold">${index + 1}</span>
          <div class="flex-1">
            <div class="font-medium text-green-900">${dest.city}</div>
            <div class="text-xs text-green-700">${dest.region} • ${dest.days} day${dest.days > 1 ? 's' : ''}</div>
          </div>
          ${moveIcon ? `<span class="text-green-600 font-bold">${moveIcon}</span>` : ''}
        </div>
      `;
    });
    
    container.innerHTML = html;
  }

  // Get movement icon (up/down/same)
  function getMovementIcon(destId, newIndex) {
    const oldIndex = destinations.findIndex(d => d.id === destId);
    if (oldIndex === newIndex) return '';
    if (oldIndex < newIndex) return '↓';
    return '↑';
  }

  // Generate optimization insights
  function generateOptimizationInsights() {
    const insights = [];
    const climate = [];
    const tips = [];
    
    // Analyze the recommended order
    const hasNorth = recommendedOrder.some(d => d.region === 'North');
    const hasCenter = recommendedOrder.some(d => d.region === 'Center');
    const hasSouth = recommendedOrder.some(d => d.region === 'South');
    
    // Geographic flow insight
    if (hasNorth && hasCenter && hasSouth) {
      insights.push('🗺️ <strong>Geographic Flow:</strong> Optimized north-to-south progression minimizes travel time and backtracking. This route follows Tunisia\'s natural geography.');
    } else if (hasNorth && hasCenter) {
      insights.push('🗺️ <strong>Regional Coverage:</strong> Covers northern and central Tunisia efficiently, reducing unnecessary long-distance travel.');
    } else if (hasCenter && hasSouth) {
      insights.push('🗺️ <strong>Regional Coverage:</strong> Focuses on central and southern regions with optimal routing between destinations.');
    }
    
    // Climate progression
    if (hasSouth) {
      insights.push(' <strong>Climate Progression:</strong> Moving from milder northern climates to warmer southern regions allows natural acclimatization for travelers.');
      climate.push('<li class="flex items-start gap-2"><span class="text-yellow-600">•</span><span>Southern desert regions (Tozeur, Douz) are hottest - schedule for later in trip when acclimatized</span></li>');
    }
    
    // Coastal considerations
    const coastalCount = recommendedOrder.filter(d => 
      ['Sousse', 'Hammamet', 'Djerba', 'Bizerte', 'Monastir', 'Mahdia', 'Tabarka', 'Nabeul', 'Sfax', 'Gabes'].includes(d.city)
    ).length;
    
    if (coastalCount > 0) {
      insights.push(` <strong>Beach Access:</strong> ${coastalCount} coastal destination${coastalCount > 1 ? 's' : ''} included for Mediterranean experiences and temperature moderation.`);
      climate.push(`<li class="flex items-start gap-2"><span class="text-yellow-600">•</span><span>Coastal areas benefit from sea breeze, averaging 3-5°C cooler than inland</span></li>`);
    }
    
    // Cultural flow
    const hasTunis = recommendedOrder.some(d => d.city === 'Tunis');
    const hasKairouan = recommendedOrder.some(d => d.city === 'Kairouan');
    
    if (hasTunis && recommendedOrder[0].city === 'Tunis') {
      insights.push(' <strong>Cultural Introduction:</strong> Starting in Tunis provides excellent orientation - capital city with museums, history, and modern amenities.');
    }
    
    if (hasKairouan) {
      tips.push('<li class="flex items-start gap-2"><span class="text-purple-600">✓</span><span>Kairouan is a UNESCO World Heritage Site - allocate extra time for Grand Mosque and Medina</span></li>');
    }
    
    // Travel efficiency
    const totalDistance = calculateTotalDistance(recommendedOrder);
    const originalDistance = calculateTotalDistance(destinations);
    
    if (totalDistance < originalDistance) {
      const savings = ((originalDistance - totalDistance) / originalDistance * 100).toFixed(0);
      insights.push(` <strong>Travel Efficiency:</strong> This route reduces total travel distance by ~${savings}%, saving time and transportation costs.`);
    }
    
    // Seasonal timing
    climate.push('<li class="flex items-start gap-2"><span class="text-yellow-600">•</span><span>Best overall period: March-May or September-November for moderate temperatures across all regions</span></li>');
    climate.push('<li class="flex items-start gap-2"><span class="text-yellow-600">•</span><span>Summer (June-August): Focus on coastal and northern destinations; desert areas extremely hot (40°C+)</span></li>');
    climate.push('<li class="flex items-start gap-2"><span class="text-yellow-600">•</span><span>Winter (December-February): Southern regions mild and pleasant; northern areas can be rainy</span></li>');
    
    // Smart tips
    tips.push('<li class="flex items-start gap-2"><span class="text-purple-600">✓</span><span>Book accommodations in advance for peak season (July-August) in coastal areas</span></li>');
    tips.push('<li class="flex items-start gap-2"><span class="text-purple-600">✓</span><span>Start early mornings in southern desert regions to avoid midday heat</span></li>');
    tips.push('<li class="flex items-start gap-2"><span class="text-purple-600">✓</span><span>Pack layers - temperature variations between regions can be significant (10-15°C difference)</span></li>');
    tips.push('<li class="flex items-start gap-2"><span class="text-purple-600">✓</span><span>Friday is the Muslim holy day - some attractions may have reduced hours</span></li>');
    
    // Update the DOM
    document.getElementById('overall-insights').innerHTML = insights.join('<br><br>');
    document.getElementById('overall-climate-notes').innerHTML = climate.join('');
    document.getElementById('overall-tips').innerHTML = tips.join('');
  }

  // Calculate total distance between destinations
  function calculateTotalDistance(destList) {
    let total = 0;
    for (let i = 0; i < destList.length - 1; i++) {
      const d1 = destList[i];
      const d2 = destList[i + 1];
      const distance = haversineDistance(d1.lat, d1.lng, d2.lat, d2.lng);
      total += distance;
    }
    return total;
  }

  // Haversine formula for distance calculation
  function haversineDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Earth's radius in km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
  }

  // Apply recommended order
  function applyRecommendedOrder() {
    if (recommendedOrder.length === 0) return;
    
    // Reorder destinations array
    destinations = [...recommendedOrder];
    
    // Clear and rebuild the destinations container
    const container = document.getElementById('destinations-container');
    container.innerHTML = '';
    
    // Re-add each destination with correct order
    destinations.forEach((dest, index) => {
      const destHtml = createDestinationHtml(dest.id, dest.days);
      container.insertAdjacentHTML('beforeend', destHtml);
      
      // Repopulate the fields
      setTimeout(() => {
        const element = document.querySelector(`[data-index="${dest.id}"]`);
        if (element) {
          element.querySelector('.destination-city').value = dest.city;
          element.querySelector('.destination-days').value = dest.days;
          element.querySelector('.destination-region').value = dest.region;
          element.querySelector('.destination-attractions').value = dest.attractions.join(', ');
          element.querySelector('.destination-activities').value = dest.activities.join(', ');
          element.querySelector('.destination-description').value = dest.description;
          
          // Reinitialize map and climate
          if (dest.city) {
            fetchClimateData(dest.id, dest.city);
            initializeWeatherMap(dest.id, dest.city);
          }
        }
      }, 100);
    });
    
    closeOverallAIModal();
    
    // Show success message
    setTimeout(() => {
      alert('Itinerary optimized! Your destinations have been reordered for optimal travel flow and climate conditions.');
    }, 500);
  }

  // Close overall AI modal
  function closeOverallAIModal(event) {
    const modal = document.getElementById('overall-ai-modal');
    if (event && event.target !== modal) return;
    
    modal.style.opacity = '0';
    setTimeout(() => {
      modal.style.display = 'none';
    }, 300);
  }

</script>

  <!-- Leaflet Map Library -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  <style>
    .step-content {
      min-height: 500px;
    }
    
    .suggestion-city {
      transition: all 0.2s ease;
    }
    
    .suggestion-city:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Leaflet map styles */
    .leaflet-container {
      font-family: inherit;
    }
    
    .leaflet-popup-content-wrapper {
      border-radius: 8px;
    }
    
    .map-layer-btn {
      cursor: pointer;
      user-select: none;
    }
    
    .map-layer-btn:active {
      transform: scale(0.95);
    }

    /* Enhanced slider styles */
    .weather-time-slider {
      -webkit-appearance: none;
      appearance: none;
      outline: none;
      transition: opacity 0.2s;
    }
    
    .weather-time-slider::-webkit-slider-thumb {
      -webkit-appearance: none;
      appearance: none;
      width: 24px;
      height: 24px;
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%);
      cursor: grab;
      border-radius: 50%;
      border: 3px solid white;
      box-shadow: 0 2px 8px rgba(0,0,0,0.3), 0 0 0 1px rgba(59,130,246,0.5);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .weather-time-slider::-webkit-slider-thumb:hover {
      transform: scale(1.3);
      box-shadow: 0 4px 12px rgba(59,130,246,0.5), 0 0 0 3px rgba(59,130,246,0.3);
    }
    
    .weather-time-slider::-webkit-slider-thumb:active {
      cursor: grabbing;
      transform: scale(1.1);
    }
    
    .weather-time-slider::-moz-range-thumb {
      width: 24px;
      height: 24px;
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%);
      cursor: grab;
      border-radius: 50%;
      border: 3px solid white;
      box-shadow: 0 2px 8px rgba(0,0,0,0.3), 0 0 0 1px rgba(59,130,246,0.5);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .weather-time-slider::-moz-range-thumb:hover {
      transform: scale(1.3);
      box-shadow: 0 4px 12px rgba(59,130,246,0.5), 0 0 0 3px rgba(59,130,246,0.3);
    }
    
    .weather-time-slider::-moz-range-thumb:active {
      cursor: grabbing;
      transform: scale(1.1);
    }
    
    .weather-time-slider::-webkit-slider-runnable-track {
      height: 10px;
      background: linear-gradient(to right, 
        #fbbf24 0%, 
        #f59e0b 15%, 
        #fb923c 30%,
        #3b82f6 50%, 
        #10b981 70%,
        #22c55e 85%,
        #16a34a 100%);
      border-radius: 5px;
      box-shadow: inset 0 1px 3px rgba(0,0,0,0.2);
    }
    
    .weather-time-slider::-moz-range-track {
      height: 10px;
      background: linear-gradient(to right, 
        #fbbf24 0%, 
        #f59e0b 15%, 
        #fb923c 30%,
        #3b82f6 50%, 
        #10b981 70%,
        #22c55e 85%,
        #16a34a 100%);
      border-radius: 5px;
      box-shadow: inset 0 1px 3px rgba(0,0,0,0.2);
    }

    /* Compare mode slider styles */
    .weather-time-slider-compare::-webkit-slider-thumb {
      -webkit-appearance: none;
      appearance: none;
      width: 18px;
      height: 18px;
      background: linear-gradient(135deg, #a855f7 0%, #7c3aed 100%);
      cursor: pointer;
      border-radius: 50%;
      border: 2px solid white;
      box-shadow: 0 2px 4px rgba(0,0,0,0.2);
      transition: all 0.2s ease;
    }
    
    .weather-time-slider-compare::-webkit-slider-thumb:hover {
      transform: scale(1.2);
    }
    
    .weather-time-slider-compare::-moz-range-thumb {
      width: 18px;
      height: 18px;
      background: linear-gradient(135deg, #a855f7 0%, #7c3aed 100%);
      cursor: pointer;
      border-radius: 50%;
      border: 2px solid white;
      box-shadow: 0 2px 4px rgba(0,0,0,0.2);
      transition: all 0.2s ease;
    }
    
    .weather-time-slider-compare::-moz-range-thumb:hover {
      transform: scale(1.2);
    }

    /* Animations */
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .animate-fadeIn {
      animation: fadeIn 0.3s ease-out;
    }
    
    @keyframes pulse {
      0%, 100% {
        opacity: 1;
      }
      50% {
        opacity: 0.7;
      }
    }
    
    .animate-pulse {
      animation: pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    /* Time display animation */
    #time-display-1, #time-display-2, #time-display-3, #time-display-4, #time-display-5 {
      transition: transform 0.2s ease, background-color 0.3s ease;
    }
    
    /* Granularity button animations */
    .time-granularity-btn {
      transition: all 0.2s ease;
    }
    
    .time-granularity-btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* AI Modal animations */
    #ai-modal {
      transition: opacity 0.3s ease;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    .animate-spin {
      animation: spin 1s linear infinite;
    }

    /* Smooth content fade */
    #ai-content {
      transition: opacity 0.5s ease, transform 0.5s ease;
    }

    /* Gradient animation for AI button */
    @keyframes gradient-shift {
      0%, 100% {
        background-position: 0% 50%;
      }
      50% {
        background-position: 100% 50%;
      }
    }

    button[onclick*="showAIRecommendations"] {
      background-size: 200% auto;
      animation: gradient-shift 3s ease infinite;
    }
  </style>
</x-layout>