@props(['tour'])
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach(['Price' => $tour->price, 'Location' => $tour->location, 'Schedule' => $tour->schedule, 'Duration' => $tour->duration ?? 'Not specified'] as $label => $value)
        <div class="bg-gray-50 p-3 rounded border">
            <p class="text-xs text-gray-500 uppercase font-semibold">{{ $label }}</p>
            <p class="font-medium">{{ $value }}</p>
        </div>
    @endforeach
</div>