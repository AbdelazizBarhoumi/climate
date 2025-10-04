@props(['tour'])
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <p class="text-gray-700 font-semibold">Price:</p>
        <p class="text-gray-800">{{ $tour->price }}</p>
    </div>
    <div>
        <p class="text-gray-700 font-semibold">Location:</p>
        <p class="text-gray-800">{{ $tour->location }}</p>
    </div>
    <div>
        <p class="text-gray-700 font-semibold">Schedule:</p>
        <p class="text-gray-800">{{ $tour->schedule }}</p>
    </div>
    <div>
        <p class="text-gray-700 font-semibold">Company Website:</p>
        <a href="{{ $tour->employer->website }}" class="text-blue-600 hover:underline" target="_blank">{{ $tour->employer->website }}</a>
    </div>
</div>