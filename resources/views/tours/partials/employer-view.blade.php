<div class="flex flex-col md:flex-row justify-between mb-6 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">{{ $tour->title }}</h1>
        <div class="flex flex-wrap gap-2 mt-2">
            <x-tour.featured-badge :tour="$tour" />
            <x-tour.deadline-badge :tour="$tour" />
            <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-semibold mb-2">
                {{ $tour->schedule }}
            </span>
            <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold mb-2">
                <x-icon.location class="h-4 w-4 inline mr-1" /> {{ $tour->getDestinationsString() }}
            </span>
        </div>
    </div>
    @if(auth()->check() && auth()->user()->isEmployer() && $tour->employer_id == auth()->user()->employer->id)
        <div class="flex gap-2">
            <span><a href="{{ route('mytour.edit', $tour) }}" class="btn btn-blue whitespace-nowrap">
                <x-icon.edit class="h-4 w-4 mr-1" /> Edit Listing
            </a></span>
            
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="btn btn-gray whitespace-nowrap">
                    <x-icon.dots class="h-6 w-4" />
                </button>
                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 border">
                    <div class="py-1">
                        <form method="POST" action="{{ route('mytour.destroy', $tour) }}" onsubmit="return confirm('Are you sure you want to delete this tour listing? This action cannot be undone.')">
                            @csrf @method('DELETE')
                            <button class="flex w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <x-icon.trash class="h-4 w-4 mr-2" /> Delete listing
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="md:col-span-2">
        <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
            <h2 class="text-lg font-semibold mb-3 pb-2 border-b">Listing Details</h2>
            <x-tour.info-grid :tour="$tour" />
            
            <h3 class="text-lg font-semibold mb-3 mt-6">Description</h3>
            <x-tour.description :tour="$tour" />
            
        </div>
        
        <x-tour.destinations :tour="$tour" />
    </div>
    
    <div class="space-y-6">
        @if(auth()->check() && auth()->user()->isEmployer() && $tour->employer_id == auth()->user()->employer->id)
            <div class="bg-gradient-to-br from-indigo-50 to-blue-50 p-4 rounded-lg border border-blue-100 shadow-sm">
                <h2 class="text-lg font-semibold mb-4 flex items-center text-indigo-800">
                    <x-icon.chart class="h-5 w-5 mr-2" /> Application Statistics
                </h2>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <x-stats-card label="Total Applications" :value="$tour->applications->count()" />
                    <x-stats-card label="Pending Review" :value="$tour->applications()->where('status', 'pending')->count()" class="text-indigo-600" />
                    <x-stats-card label="Accepted" :value="$tour->applications()->where('status', 'accepted')->count()" class="text-green-600" />
                    <x-stats-card label="Rejected" :value="$tour->applications()->where('status', 'rejected')->count()" class="text-gray-600" />
                </div>
                <a href="{{ route('applications.index', ['tour_id' => $tour->id]) }}" class="btn btn-indigo w-full text-center">
                    <x-icon.users class="h-4 w-4 mr-1" /> Manage Applications 
                </a>
            </div>
            
            <div class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm">
                <h2 class="text-lg font-semibold mb-3 flex items-center">
                    <x-icon.calendar class="h-5 w-5 mr-2" /> Listing Status
                </h2>
                <ul class="space-y-3">
                    <li class="flex items-start">
                        <x-icon.clock class="h-5 w-5 text-gray-400 mr-2 mt-0.5" />
                        <div>
                            <p class="text-gray-700">Posted on</p>
                            <p class="font-medium">{{ $tour->created_at->format('M d, Y') }}</p>
                        </div>
                    </li>
                    @if($tour->deadline_date)
                    <li class="flex items-start">
                        <x-icon.calendar-end class="h-5 w-5 text-gray-400 mr-2 mt-0.5" />
                        <div>
                            <p class="text-gray-700">Application deadline</p>
                            <p class="font-medium">
                                {{ $tour->deadline_date->format('M d, Y') }}
                                @if($tour->deadline_date->isPast())
                                    <span class="text-red-600 text-sm">(Closed)</span>
                                @else
                                    <span class="text-gray-500 text-sm">({{ $tour->deadline_date->diffForHumans() }})</span>
                                @endif
                            </p>
                        </div>
                    </li>
                    @endif
                    <li class="flex items-start">
                        <x-icon.view class="h-5 w-5 text-gray-400 mr-2 mt-0.5" />
                        <div>
                            <p class="text-gray-700">Listing views</p>
                            <p class="font-medium">{{ $tour->view_count ?? 0 }}</p>
                        </div>
                    </li>
                </ul>
                
                <div class="border-t mt-4 pt-4">
                    <h3 class="font-medium mb-2">Listing visibility</h3>
                    <div class="flex items-center mb-3">
                        <div class="w-3 h-3 rounded-full {{ $tour->is_active ? 'bg-green-500' : 'bg-red-500' }} mr-2"></div>
                        <span>{{ $tour->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                    
                    <form method="POST" action="{{ route('mytour.active', $tour) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $tour->is_active ? 'btn-red' : 'btn-green' }} w-full">
                            {{ $tour->is_active ? 'Pause Listing' : 'Activate Listing' }}
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="flex justify-between">
    <a href="{{ route('dashboard') }}" class="btn btn-gray">
        <x-icon.arrow-left class="h-4 w-4 mr-1" /> Back to Dashboard
    </a>
    @if (auth()->check() && auth()->user()->isEmployer())
    <a href="{{ route('myTours') }}" class="btn btn-blue">
        View My Tours <x-icon.arrow-right class="h-4 w-4 mr-1 ml-1" /> 
    </a>
    @endif

</div>