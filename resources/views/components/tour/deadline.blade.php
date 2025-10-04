@props(['tour'])
@if($tour->deadline_date)
    <div class="mb-6 bg-blue-50 p-4 rounded-lg">
        <div class="flex items-center gap-2">
            <x-icon.calendar class="h-5 w-5 text-blue-500" />
            <span class="font-medium">Application deadline_date:</span>
            {{ $tour->deadline_date->format('F j, Y') }}
            @if(!$tour->deadline_date->isPast())
                <span class="text-sm text-gray-500">({{ $tour->deadline_date->diffForHumans() }})</span>
            @endif
        </div>
    </div>
@endif