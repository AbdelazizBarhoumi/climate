<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\TouristProfileUpdateRequest;
use App\Http\Requests\EmployerProfileUpdateRequest;
use App\Models\User;
use App\Models\Tourist;
use App\Models\Employer;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }
    public function updateTourist(TouristProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $tourist = $user->tourist;
        
        // Handle file uploads
        if ($request->hasFile('resume')) {
            // Delete old resume if exists
            if ($tourist->resume_path) {
                Storage::disk('public')->delete($tourist->resume_path);
            }
            
            $resumePath = $request->file('resume')->store('resumes', 'public');
            $tourist->resume_path = $resumePath;
        }
        
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($tourist->profile_photo_path) {
                Storage::disk('public')->delete($tourist->profile_photo_path);
            }
            
            $photoPath = $request->file('profile_photo')->store('profile_photos', 'public');
            $tourist->profile_photo_path = $photoPath;
        }
        
        // Update other fields
        $tourist->fill($request->safe()->except(['resume', 'profile_photo']));
        $tourist->save();
        
        return Redirect::route('profile.edit')->with('status', 'tourist-profile-updated');
    }

    /**
     * Update the employer's profile information.
     */
    public function updateEmployer(EmployerProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $employer = $user->employer;
        
        // Handle logo upload
        if ($request->hasFile('employer_logo')) {
            // Delete old logo if exists
            if ($employer->employer_logo) {
                Storage::disk('public')->delete($employer->employer_logo);
            }
            
            $logoPath = $request->file('employer_logo')->store('employer_logos', 'public');
            $employer->employer_logo = $logoPath;
        }
        
        // Update other fields
        $employer->fill($request->safe()->except(['employer_logo']));
        $employer->save();
        
        return Redirect::route('profile.edit')->with('status', 'employer-profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
