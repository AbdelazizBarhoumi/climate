<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tour;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Employer;

class EmployerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $employer = $user->employer;
        
        // Get active tours count
        $activeTours = Tour::where('employer_id', $employer->id)
                            ->where('is_active', true)
                            ->count();
        
        // Get recent applications
        $recentApplications = Application::whereHas('tour', function($query) use ($employer) {
                            $query->where('employer_id', $employer->id);
                          })
                          ->with(['user', 'tour'])
                          ->latest()
                          ->take(5)
                          ->get();
        
        // Get active tours list
        $activeToursList = Tour::where('employer_id', $employer->id)
                              ->where('is_active', true)
                              ->withCount('applications')
                              ->latest()
                              ->take(4)
                              ->get();
        
        // Get total applicants
        $totalApplicants = Application::whereHas('tour', function($query) use ($employer) {
                         $query->where('employer_id', $employer->id);
                       })
                       ->count();
        
        // Get pending applications
        $pendingApplications = Application::whereHas('tour', function($query) use ($employer) {
                             $query->where('employer_id', $employer->id);
                           })
                           ->where('status', 'pending')
                           ->count();
        
        // Get accepted applications
        $acceptedApplications = Application::whereHas('tour', function($query) use ($employer) {
                              $query->where('employer_id', $employer->id);
                            })
                            ->where('status', 'accepted')
                            ->count();
        
        return view('dashboard', compact(
            'recentApplications', 
            'activeToursList', 
            'activeTours',
            'totalApplicants',
            'pendingApplications',
            'acceptedApplications'
        ));
    }
    public function toggletourStatus(Tour $tour)
    {
        // Check if the authenticated user is the employer of the tour
        $user = Auth::user();
               
        $isEmployerOwner = $tour->employer_id === $user->employer->id;
        if (!$isEmployerOwner) {
            abort(403, 'Unauthorized action. You do not own this tour.');
        }

        $tour->is_active = !$tour->is_active;
        $tour->save();

        $status = $tour->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Tour has been {$status}.");
    }
}
