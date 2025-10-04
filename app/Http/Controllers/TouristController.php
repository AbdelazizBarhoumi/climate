<?php

namespace App\Http\Controllers;

use App\Models\Tourist;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TouristController extends Controller
{
    /**
     * Display the tourist's profile
     */
    public function show()
    {
        $tourist = Auth::user()->tourist;
        
        if (!$tourist) {
            return redirect()->route('tourist.create')
                ->with('info', 'Please complete your tourist profile first.');
        }
        
        return view('tourists.show', compact('tourist'));
    }
    
    /**
     * Show the form for creating a new tourist profile
     */
    public function create()
    {
        // Check if user already has a tourist profile
        if (Auth::user()->tourist) {
            return redirect()->route('tourist.edit')
                ->with('info', 'You already have a tourist profile.');
        }
        
        return view('tourists.create');
    }
    
    /**
     * Store a newly created tourist profile
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'education_level' => 'required|in:high_school,associate,bachelor,master,phd',
            'institution' => 'required|string|max:255',
            'field_of_study' => 'nullable|string|max:255',
            'graduation_date' => 'nullable|date',
            'skills' => 'nullable|string',
            'bio' => 'nullable|string|max:1000',
            'resume' => 'nullable|file|mimes:pdf|max:2048',
            'linkedin_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'portfolio_url' => 'nullable|url|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone' => 'nullable|string|max:20',
        ]);
        
        // Handle resume upload
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('resumes', 'public');
            $validated['resume_path'] = $resumePath;
        }


        if ($request->hasFile('profile_photo')) {
            $profilePhotoPath = $request->file('profile_photo')->store('profile_photos', 'public');
            $validated['profile_photo_path'] = $profilePhotoPath;
        }
        
        // Create the tourist profile
        $tourist = new Tourist($validated);
        $tourist->user_id = Auth::id();
        $tourist->save();
        
        return redirect()->route('dashboard')
            ->with('success', 'Tourist profile created successfully!');
    }
    
    /**
     * Show the form for editing the tourist profile
     */
    public function edit()
    {
        $tourist = Auth::user()->tourist;
        
        if (!$tourist) {
            return redirect()->route('tourist.create')
                ->with('info', 'Please create your tourist profile first.');
        }
        
        return view('tourists.edit', compact('tourist'));
    }
    
    /**
     * Update the tourist profile
     */
    public function update(Request $request)
    {
        $tourist = Auth::user()->tourist;
        
        if (!$tourist) {
            return redirect()->route('tourist.create')
                ->with('info', 'Please create your tourist profile first.');
        }
        
        $validated = $request->validate([
            'education_level' => 'required|in:high_school,associate,bachelor,master,phd',
            'institution' => 'required|string|max:255',
            'field_of_study' => 'nullable|string|max:255',
            'graduation_date' => 'nullable|date',
            'skills' => 'nullable|string',
            'bio' => 'nullable|string|max:1000',
            'resume' => 'nullable|file|mimes:pdf|max:2048',
            'linkedin_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'portfolio_url' => 'nullable|url|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone' => 'nullable|string|max:20',
        ]);
        
        // Handle resume upload
        if ($request->hasFile('resume')) {
            // Delete old resume if it exists
            if ($tourist->resume_path) {
                Storage::disk('public')->delete($tourist->resume_path);
            }
            
            $resumePath = $request->file('resume')->store('resumes', 'public');
            $validated['resume_path'] = $resumePath;
        }


        if ($request->hasFile('profile_photo')) {

            if ($tourist->profile_photo_path) {
                Storage::disk('public')->delete($tourist->profile_photo_path);
            }
            
            $profilePhotoPath = $request->file('profile_photo')->store('profile_photos', 'public');
            $validated['profile_photo_path'] = $profilePhotoPath;
        }
        
        // Update the tourist profile
        $tourist->update($validated);
        
        return redirect()->route('tourist.show')
            ->with('success', 'Tourist profile updated successfully!');
    }
}