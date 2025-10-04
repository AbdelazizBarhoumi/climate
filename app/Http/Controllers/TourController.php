<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;

use Illuminate\Routing\Controller;

class TourController extends Controller
{
    use AuthorizesRequests;

    /**
     * Constructor - apply middleware
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        // Remove the active.user middleware since we'll handle it directly
    }
    
    /**
     * Check if the current user is active
     * Returns true if active, redirects if not
     */
    protected function checkUserIsActive()
    {
        if (Auth::check() && !Auth::user()->is_active) {
            return false;
        }
        
        return true;
    }

    /**
     * Display a listing of the resource.
     */
        public function index(Request $request)
    {
        $baseQuery = Tour::with(['employer', 'tags'])
            // Only show tours from active users
            ->whereHas('employer.user', function($query) {
                $query->where('is_active', true);
            })
            // Only show active tours
            ->where('is_active', true);
        
        // Filter by employer if viewing own listings
        if ($request->has('employer') && Auth::check() && Auth::user()->isEmployer()) {
            $baseQuery->where('employer_id', Auth::user()->employer->id);
        }
        
        // Get featured tours with pagination
        $featuredTours = (clone $baseQuery)
            ->where('featured', true)
            ->orderBy('created_at', 'desc')
            ->paginate(6, ['*'], 'featured_page'); // Use 'featured_page' as the page parameter
            
        // Get regular tours with pagination (use a different page parameter)
        $regularTours = $baseQuery
            ->where('featured', false)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'page'); // Use default 'page' parameter
                            
        // Get up to 15 random tags that are associated with active tours
        $tags = Tag::whereHas('tours', function($query) {
                $query->whereHas('employer.user', function($subQuery) {
                    $subQuery->where('is_active', true);
                })->where('is_active', true);
            })
            ->inRandomOrder()
            ->limit(15)
            ->get();
        
        return view('tours.index', [
            'featuredTours' => $featuredTours,
            'tours' => $regularTours,
            'tags' => $tags,
        ]);
    }

    /**
     * Display a listing of the user's tours.
     */
    public function myTours()
    {
        // Temporarily commented out suspension check
        // if (Auth::check() && !Auth::user()->is_active) {
        // return redirect()->route('account.suspended')
        //         ->with('error', 'Your account is currently suspended. Please contact an administrator.');
        // }
        
        // Check if user has an employer profile
        if (!Auth::user()->isEmployer()) {
            return redirect()->route('home')
                ->with('error', 'You need an employer profile to manage tours.');
        }
        
        $tours = Auth::user()->employer->tours()
            ->with(['tags'])
            ->withCount(['applications', 'applications as pending_count' => function($query) {
                $query->where('status', 'pending');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('tours.mine', [
            'tours' => $tours,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Temporarily commented out suspension check
        // if (Auth::check() && !Auth::user()->is_active) {
        // return redirect()->route('account.suspended')
        //         ->with('error', 'Your account is currently suspended. You cannot create tours.');
        // }
        
        // Check if user has an employer profile
        if (!Auth::user()->isEmployer()) {
            return redirect()->route('employer.create')
                ->with('error', 'You need to create an employer profile first.');
        }
        
        return view('tours.create', [
            'popularTags' => Tag::withCount('tours')
                ->orderBy('tours_count', 'desc')
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Temporarily commented out suspension check
        // if (Auth::check() && !Auth::user()->is_active) {
        // return redirect()->route('account.suspended')
        //         ->with('error', 'Your account is currently suspended. You cannot post tours.');
        // }
        
        // Check if user has an employer profile
        if (!Auth::user()->isEmployer()) {
            return redirect()->route('employer.create')
                ->with('error', 'You need to create an employer profile first.');
        }
        
        $attributes = $request->validate([
            'title' => ['required', 'string', 'max:255', 'min:3'],
            'price' => ['required', 'string', 'max:255', 'min:3'],
            'schedule' => ['required', 'string', 'max:255', Rule::in(['Cultural', 'Adventure', 'Beach', 'Desert', 'Historical', 'Mixed'])],
            'location' => ['required', 'string', 'max:255', 'min:2'],
            'description' => ['required', 'string', 'max:5000', 'min:10'],
            'duration' => ['required', 'string', 'max:255'],
            'deadline_date' => ['nullable', 'date', 'after:today'],
            'start_date' => ['required', 'date', 'after:+6 days'],
            'total_days' => ['required', 'integer', 'min:1', 'max:60'],
            'base_price' => ['required', 'numeric', 'min:50', 'max:20000'],
            'destinations' => ['required', 'json'],
            'tags' => ['nullable', 'string', 'max:500'],
        ]);

        // Validate destinations JSON
        $destinations = json_decode($attributes['destinations'], true);
        
        if (!is_array($destinations) || count($destinations) < 1) {
            return back()->withErrors(['destinations' => 'At least one destination is required.'])->withInput();
        }
        
        if (count($destinations) > 15) {
            return back()->withErrors(['destinations' => 'Maximum 15 destinations allowed.'])->withInput();
        }
        
        // Validate total days match destination days
        $totalDestinationDays = array_sum(array_column($destinations, 'days'));
        if ($totalDestinationDays != $attributes['total_days']) {
            return back()->withErrors(['destinations' => 'Total destination days must equal tour duration.'])->withInput();
        }
        
        // Validate each destination
        foreach ($destinations as $index => $dest) {
            if (empty($dest['city']) || !is_string($dest['city']) || strlen(trim($dest['city'])) < 2) {
                return back()->withErrors(['destinations' => "Destination " . ($index + 1) . " must have a valid city name."])->withInput();
            }
            
            if (!isset($dest['days']) || !is_numeric($dest['days']) || $dest['days'] < 1 || $dest['days'] > 30) {
                return back()->withErrors(['destinations' => "Destination " . ($index + 1) . " must have between 1-30 days."])->withInput();
            }
            
            if (isset($dest['attractions']) && is_array($dest['attractions'])) {
                if (count($dest['attractions']) > 10) {
                    return back()->withErrors(['destinations' => "Destination " . ($index + 1) . " cannot have more than 10 attractions."])->withInput();
                }
            }
            
            if (isset($dest['activities']) && is_array($dest['activities'])) {
                if (count($dest['activities']) > 10) {
                    return back()->withErrors(['destinations' => "Destination " . ($index + 1) . " cannot have more than 10 activities."])->withInput();
                }
            }
        }
        
        // Clean and prepare destinations data
        foreach ($destinations as &$dest) {
            $dest['city'] = trim($dest['city']);
            $dest['region'] = trim($dest['region'] ?? '');
            $dest['description'] = trim($dest['description'] ?? '');
            
            // Ensure arrays
            $dest['attractions'] = is_array($dest['attractions']) ? $dest['attractions'] : [];
            $dest['activities'] = is_array($dest['activities']) ? $dest['activities'] : [];
            
            // Clean empty values
            $dest['attractions'] = array_values(array_filter(array_map('trim', $dest['attractions'])));
            $dest['activities'] = array_values(array_filter(array_map('trim', $dest['activities'])));
        }
        
        $attributes['destinations'] = $destinations;
        
        $attributes['featured'] = $request->has('featured');
        // Set is_active status based on settings or default to true
        $attributes['is_active'] = true;
        
        $tour = Auth::user()->employer->tours()->create(
            Arr::except($attributes, ['tags'])
        );
        
        // Handle tags
        if (!empty($attributes['tags'])) {
            foreach (explode(',', $attributes['tags']) as $tagName) {
                if (trim($tagName)) {
                    $tour->tag(trim($tagName));
                }
            }
        }
        
        return redirect()->route('tour.show', $tour)
            ->with('success', 'Tour created successfully!');
    }

    /**
     * Display the specified tour.
     *
     * @param  \App\Models\Tour  $tour
     * @return \Illuminate\Http\Response
     */
    public function show(Tour $tour)
    {
        // Only increment the view count if:
        // 1. The viewer is not the owner of the tour
        // 2. We're not in an admin context
        
        if (auth()->guest() || auth()->id() !== $tour->employer_id) {
            // You could use a session to prevent multiple views in same session
            $viewedTours = session()->get('viewed_tours', []);
            
            if (!in_array($tour->id, $viewedTours)) {
                $tour->incrementViewCount();
                
                // Add this tour to the viewed list
                $viewedTours[] = $tour->id;
                session()->put('viewed_tours', $viewedTours);
            }
        }
        
        // Ensure inactive users' tours are not visible unless to the owner or admin
        $isOwner = Auth::check() && Auth::user()->isEmployer() && 
                  Auth::user()->employer->id === $tour->employer_id;
        $isAdmin = Auth::check() && Auth::user()->isAdmin();
        
        if (!$tour->is_active && !$isOwner && !$isAdmin) {
            abort(404, 'Tour not found');
        }
        
        // Temporarily commented out suspension check for employer tours
        // if (!$tour->employer->user->is_active && !$isOwner && !$isAdmin) {
        //     abort(404, 'Tour not found');
        // }
        
        return response()->view('tours.show', [
            'tour' => $tour->load(['employer', 'tags']),
            'isOwner' => $isOwner,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tour $tour)
    {
        // Temporarily commented out suspension check
        // if (Auth::check() && !Auth::user()->is_active) {
        // return redirect()->route('account.suspended')
        //         ->with('error', 'Your account is currently suspended. You cannot edit tours.');
        // }
        
        $this->authorize('update', $tour);
        
        return view('tours.edit', [
            'tour' => $tour,
            'popularTags' => Tag::withCount('tours')
                ->orderBy('tours_count', 'desc')
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tour $tour)
    {
        // Temporarily commented out suspension check
        // if (Auth::check() && !Auth::user()->is_active) {
        // return redirect()->route('account.suspended')
        //         ->with('error', 'Your account is currently suspended. You cannot update tours.');
        // }
        
        $this->authorize('update', $tour);
        
        $attributes = $request->validate([
            'title' => ['required', 'string', 'max:255', 'min:3'],
            'price' => ['required', 'string', 'max:255', 'min:3'],
            'schedule' => ['required', 'string', 'max:255', Rule::in(['Day Trip', 'Half Day', 'Multi-Day', 'Weekend', 'Custom'])],
            'location' => ['required', 'string', 'max:255', 'min:5'],
            'description' => ['nullable', 'string', 'max:5000'],
            'duration' => ['nullable', 'string', 'max:255'],
            'deadline_date' => ['nullable', 'date', 'after:today'],
            'tags' => ['nullable', 'string', 'max:255'],
        ]);
        
        $attributes['featured'] = $request->has('featured');
        
        // Update the specific tour
        $tour->update(Arr::except($attributes, ['tags']));
        
        // Handle tags
        if (isset($attributes['tags'])) {
            // Clear existing tags first
            $tour->tags()->detach();
            
            // Add new tags
            foreach (explode(',', $attributes['tags']) as $tagName) {
                if (trim($tagName)) {
                    $tour->tag(trim($tagName));
                }
            }
        }
        
        return redirect()->route('tour.show', $tour)
            ->with('success', 'Tour updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tour $tour)
    {
        // Temporarily commented out suspension check
        // if (Auth::check() && !Auth::user()->is_active) {
        // return redirect()->route('account.suspended')
        //         ->with('error', 'Your account is currently suspended. You cannot delete tours.');
        // }
        
        $this->authorize('delete', $tour);
        
        $tour->delete();
        
        return redirect()->route('tours.mine')
            ->with('success', 'Tour deleted successfully!');
    }
}