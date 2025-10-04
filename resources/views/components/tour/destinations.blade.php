@props(['tour'])

@php
    $destinations = $tour->getDestinations();
@endphp

@if(!empty($destinations))
<div class="mb-8 mt-8 bg-white border rounded-lg overflow-hidden">
    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 px-6 py-4 border-b">
        <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
            </svg>
            Trip Itinerary - {{ $tour->duration }}
        </h2>
        <p class="text-sm text-gray-600 mt-1">
            {{ count($destinations) }} amazing destinations across Tunisia
        </p>
    </div>
    
    <div class="p-6">
        <!-- Destinations Timeline -->
        <div class="space-y-6">
            @foreach($destinations as $index => $destination)
                <div class="relative pl-8 pb-6 {{ $loop->last ? '' : 'border-l-2 border-indigo-200' }}">
                    <!-- Timeline dot -->
                    <div class="absolute left-0 top-0 -ml-2 flex items-center justify-center w-6 h-6 rounded-full {{ $index === 0 ? 'bg-green-500' : ($loop->last ? 'bg-red-500' : 'bg-indigo-500') }} ring-4 ring-white">
                        @if($index === 0)
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        @elseif($loop->last)
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        @else
                            <div class="w-2 h-2 rounded-full bg-white"></div>
                        @endif
                    </div>
                    
                    <!-- Destination Card -->
                    <div class="bg-gray-50 rounded-lg p-5 border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-1">
                                    <h3 class="text-lg font-bold text-gray-900">
                                        {{ $destination['city'] }}
                                    </h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $destination['region'] }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $destination['days'] }} {{ $destination['days'] > 1 ? 'days' : 'day' }}
                                    @if($index === 0)
                                        <span class="ml-2 text-green-600 font-medium">(Starting point)</span>
                                    @elseif($loop->last)
                                        <span class="ml-2 text-red-600 font-medium">(Final destination)</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        @if(isset($destination['description']))
                            <p class="text-sm text-gray-700 mb-4 leading-relaxed">
                                {{ $destination['description'] }}
                            </p>
                        @endif
                        
                        <!-- Attractions -->
                        @if(!empty($destination['attractions']))
                            <div class="mb-3">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                    Top Attractions
                                </h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($destination['attractions'] as $attraction)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white border border-amber-200 text-gray-700">
                                            {{ $attraction }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <!-- Activities -->
                        @if(!empty($destination['activities']))
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5" />
                                    </svg>
                                    Activities
                                </h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($destination['activities'] as $activity)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white border border-blue-200 text-gray-700">
                                            {{ $activity }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Summary -->
        <div class="mt-6 p-4 bg-indigo-50 rounded-lg border border-indigo-100">
            <div class="flex items-start gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900 mb-1">Tour Route</p>
                    <p class="text-sm text-gray-700">
                        {{ implode(' → ', array_column($destinations, 'city')) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
