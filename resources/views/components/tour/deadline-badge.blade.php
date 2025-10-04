@props(['tour'])
@if($tour->deadline_date)
    @if($tour->deadline_date->isPast())
        <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-semibold mb-2">Application Closed</span>
    @elseif($tour->deadline_date->diffInDays() < 3)
        <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-sm font-semibold mb-2">Closing Soon</span>
    @endif
@endif