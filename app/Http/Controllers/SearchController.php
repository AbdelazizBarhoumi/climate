<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Tag;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        // Get search parameters
        $search = $request->input('search', '');
        $filter = $request->input('filter');
        $sort = $request->input('sort', 'newest');
        $location = $request->input('location');
        $duration = $request->input('duration');
        
        // Start building the query
        $query = Tour::with(['employer', 'tags']);
        
        // Apply search term filter if provided
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('location', 'LIKE', "%{$search}%")
                    ->orWhere('price', 'LIKE', "%{$search}%")
                    ->orWhere('schedule', 'LIKE', "%{$search}%")
                    ->orWhereHas('tags', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('employer', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%")
                          ->orWhere('email', 'LIKE', "%{$search}%");
                    });
            });
        }
        
        // Apply tag filter if provided
        if ($filter) {
            $query->whereHas('tags', function($q) use ($filter) {
                $q->where('name', $filter);
            });
        }
        
        // Apply location filter if provided
        if ($location && $location !== 'any') {
            if ($location === 'north') {
                $query->where('location', 'LIKE', '%Bizerte%')
                      ->orWhere('location', 'LIKE', '%Tabarka%')
                      ->orWhere('location', 'LIKE', '%Nabeul%');
            } else if ($location === 'center') {
                $query->where('location', 'LIKE', '%Tunis%')
                      ->orWhere('location', 'LIKE', '%Sousse%')
                      ->orWhere('location', 'LIKE', '%Monastir%')
                      ->orWhere('location', 'LIKE', '%Kairouan%');
            } else if ($location === 'south') {
                $query->where('location', 'LIKE', '%Djerba%')
                      ->orWhere('location', 'LIKE', '%Tozeur%')
                      ->orWhere('location', 'LIKE', '%Gabes%')
                      ->orWhere('location', 'LIKE', '%Zarzis%');
            } else {
                // Specific city search
                $query->where('location', 'LIKE', "%{$location}%");
            }
        }
        
        // Apply sorting
        switch ($sort) {
            case 'relevant':
                // For relevance sorting, we could implement a more complex algorithm
                // For now, let's default to sorting by id
                $query->orderBy('id', 'desc');
                break;
                
            case 'expiring':
                $query->whereNotNull('deadline')
                      ->where('deadline', '>=', now())
                      ->orderBy('deadline', 'asc');
                break;
                
            case 'company':
                $query->join('employers', 'tours.employer_id', '=', 'employers.id')
                      ->orderBy('employers.name', 'asc');
                break;
                
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
        
        // Make sure only active tours from active employers are shown
        $query->where('is_active', true)
              ->whereHas('employer.user', function ($subQuery) {
                  $subQuery->where('is_active', true);
              });

        // Execute the query with pagination
        $tours = $query->paginate(10);
        
        // Determine if we're searching for a specific tag
        $searchedTag = $filter;
        
        // Return the view with all necessary data
        return view('results', [
            'tours' => $tours, 
            'search' => $search,
            'searchedTag' => $searchedTag
        ]);
    }
}