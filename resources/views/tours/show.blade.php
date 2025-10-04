<x-layout :showHero="false">
    <x-page-heading>{{ auth()->user()?->isEmployer() ? 'Manage Tour' : 'Tour Details' }}</x-page-heading>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <x-alert-success :message="session('success')" />

        @auth
            @if(auth()->user()->isEmployer() && $tour->employer_id == auth()->user()->employer->id)
                @include('tours.partials.employer-view', ['tour' => $tour])
            @else
                @include('tours.partials.tourist-view', ['tour' => $tour])
            @endif
        @else
            @include('tours.partials.tourist-view', ['tour' => $tour])
        @endauth
    </div>
</x-layout>