@props(['tour'])
<div class="prose max-w-none">
    {!! nl2br(e($tour->description)) !!}
    
    @if($tour->duration)
        <div class="mt-4">
            <p class="font-medium">Duration: <span class="font-normal">{{ $tour->duration }}</span></p>
        </div>
    @endif
</div>