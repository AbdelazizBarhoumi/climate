<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\Application;
use App\Models\Tourist;
use App\Notifications\ApplicationStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ApplicationController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Display a listing of applications for employers and admins
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        // Temporarily commented out suspension check
        // if (!Auth::user()->is_active) {
        //     return $this->accountSuspendedRedirect();
        // }
        
        // Authorization check
        //   @if(auth()->user()->isEmployer() && $tour->employer_id == auth()->user()->employer->id)

        if (!Auth::user()->isEmployer() && !Auth::user()->isAdmin()) {
            return $this->unauthorizedAccess('You do not have permission to view all applications');
        }
        
        // Build the applications query with filters
        $query = $this->buildApplicationsQuery($request);
        
        // Get counts for different statuses
        $pendingCount = (clone $query)->where('status', 'pending')->count();
        $reviewingCount = (clone $query)->where('status', 'reviewing')->count();
        $interviewedCount = (clone $query)->where('status', 'interviewed')->count();
        $acceptedCount = (clone $query)->where('status', 'accepted')->count();
        
        // Paginate and load related data
        $applications = $query->paginate(20)->withQueryString();
        
        // Get tours for the filter dropdown
        $tours = $this->getToursForCurrentUser();
        
        return view('applications.index', compact(
            'applications',
            'tours',
            'pendingCount',
            'reviewingCount',
            'interviewedCount',
            'acceptedCount'
        ));
    }

    /**
     * Display a listing of the current tourist's applications
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function myIndex(Request $request)
    {
        // Ensure the user is a tourist
        if (!Auth::user()->tourist) {
            return redirect()->route('dashboard')
                ->with('error', 'Only tourists can view applications.');
        }

        // Build the query with filters and sorting
        $query = Application::query()
            ->with(['tour', 'tour.employer'])
            ->where('applications.user_id', Auth::id());
        
        $this->applyTouristApplicationFilters($query, $request);
        
        $applications = $query->paginate(10);
        
        // Get application statistics by status
        $statistics = $this->getApplicationStatisticsForUser(Auth::id());
        
        return view('applications.my-index', array_merge(
            compact('applications'),
            $statistics
        ));
    }

    /**
     * Show application form for an tour
     *
     * @param  \App\Models\Tour  $tour
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create(Tour $tour)
    {
        // Temporarily commented out suspension check
        // if (!Auth::user()->is_active) {
        //     return $this->accountSuspendedRedirect('You cannot apply for tours.');
        // }
        
        // Check for eligibility and existing applications
        $checkResult = $this->checktourApplicationEligibility($tour);
        if ($checkResult !== true) {
            return $checkResult;
        }
        
        // Get tourist profile to pre-fill the form
        $tourist = Auth::user()->tourist;
        
        if (!$tourist) {
            return redirect()->route('tourist.profile.create')
                ->with('info', 'Please complete your tourist profile before applying for tours.');
        }
        
        return view('applications.create', compact('tour', 'tourist'));
    }

    /**
     * Store a new application
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tour  $tour
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Tour $tour)
    {
        // Temporarily commented out suspension check
        // if (!Auth::user()->is_active) {
        //     return $this->accountSuspendedRedirect('You cannot apply for tours.');
        // }
        
        // Check for eligibility and existing applications
        $checkResult = $this->checktourApplicationEligibility($tour);
        if ($checkResult !== true) {
            return $checkResult;
        }
        
        // Verify tourist profile exists
        $tourist = Auth::user()->tourist;
        if (!$tourist) {
            return redirect()->route('tourist.profile.create')
                ->with('error', 'You must complete your tourist profile before applying.');
        }
        
        // Validate application details
        $validated = $request->validate([
            'cover_letter' => 'nullable|string|max:5000',
            'why_interested' => 'required|string|max:2000',
            'availability' => 'required|date|after:today',
            'resume' => 'nullable|file|mimes:pdf|max:5120', // 5MB max
            'transcript' => 'nullable|file|mimes:pdf|max:10240', // 10MB max
        ]);
        
        try {
            // Process and store the application
            $application = $this->processAndStoreApplication($request, $tour, $tourist, $validated);
            
            /* Notification code commented out
            $tour->employer->user->notify(
                new ApplicationStatusChanged($application, 'new')
            ); */
            
            return redirect()->route('dashboard')
                ->with('success', 'Your application for "' . $tour->title . '" has been submitted successfully!');
            
        } catch (\Exception $e) {
            return $this->handleApplicationError($e, $request, $tour);
        }
    }

    /**
     * Display application details
     *
     * @param  \App\Models\Application  $application
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(Application $application)
    {
        // Temporarily commented out suspension check
        // if (!Auth::user()->is_active) {
        //     return $this->accountSuspendedRedirect();
        // }
        
        // Check authorization
        if (!$this->canViewApplication($application)) {
            return $this->unauthorizedAccess();
        }
        
        // Load related models
        $application->load(['user', 'user.tourist', 'tour', 'tour.employer']);
        
        return view('applications.show', compact('application'));
    }

    /**
     * Update application status
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, Application $application)
    {
        // Temporarily commented out suspension check
        // if (!Auth::user()->is_active) {
        //     return $this->accountSuspendedRedirect();
        // }
        
        // Check authorization
        if (!$this->canManageApplication($application)) {
            return $this->unauthorizedAccess();
        }
        
        // Validate the request
        $validated = $request->validate([
            'status' => 'required|in:pending,reviewing,interviewed,accepted,rejected',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        try {
            // Update the application status and tracking dates
            $oldStatus = $application->status;
            $this->updateApplicationStatusAndNotes($application, $validated);
            
            /* Notification code commented out
            if ($oldStatus !== $application->status) {
                $application->user->notify(
                    new ApplicationStatusChanged($application, $application->status)
                );
            } */
            
            return back()->with('success', 'Application status has been updated successfully.');
            
        } catch (\Exception $e) {
            Log::error('Failed to update application status: ' . $e->getMessage());
            return back()->with('error', 'Failed to update application status. Please try again.');
        }
    }

    /**
     * Update application notes
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateNotes(Request $request, Application $application)
    {
        // Temporarily commented out suspension check
        // if (!Auth::user()->is_active) {
        //     return $this->accountSuspendedRedirect();
        // }
        
        // Check authorization
        if (!$this->canManageApplication($application)) {
            return $this->unauthorizedAccess();
        }
        
        // Validate the request
        $validated = $request->validate([
            'notes' => 'required|string|max:5000',
        ]);
        
        try {
            $this->addNotesToApplication($application, $validated['notes']);
            return back()->with('success', 'Application notes have been updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update application notes: ' . $e->getMessage());
            return back()->with('error', 'Failed to update notes. Please try again.');
        }
    }

    /**
     * Download application document (resume or transcript)
     *
     * @param  \App\Models\Application  $application
     * @param  string  $type
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function download(Application $application, $type)
    {
        // Check if user can access this application's files
        if (!$this->canAccessApplicationFiles($application)) {
            return $this->unauthorizedAccess();
        }
        
        // Determine which file to download
        switch ($type) {
            case 'resume':
                return $this->downloadFile(
                    $application->resume_path,
                    'Resume - ' . $application->user->name . '.pdf',
                    'Resume file not found'
                );
                
            case 'transcript':
                return $this->downloadFile(
                    $application->transcript_path,
                    'Transcript - ' . $application->user->name . '.pdf',
                    'Transcript file not found'
                );
                
            default:
                return back()->with('error', 'Invalid document type requested');
        }
    }

    /**
     * Withdraw an application
     *
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Http\RedirectResponse
     */
    public function withdraw(Application $application)
    {
        // Verify user owns this application
        if ($application->user_id !== Auth::id()) {
            return $this->unauthorizedAccess('You do not have permission to withdraw this application');
        }
        
        // Check if application can be withdrawn
        if (in_array($application->status, ['accepted', 'rejected', 'withdrawn'])) {
            return back()->with('error', 'This application cannot be withdrawn because it is already ' . $application->status);
        }
        
        try {
            // Update application status
            $application->status = 'withdrawn';
            $application->notes = ($application->notes ? $application->notes . "\n\n" : '') . 
                                "Application withdrawn by applicant on " . now()->format('Y-m-d H:i:s');
            $application->save();
            
            return redirect()->route('applications.my.index')
                ->with('success', 'Your application has been withdrawn successfully');
        } catch (\Exception $e) {
            Log::error('Failed to withdraw application: ' . $e->getMessage());
            return back()->with('error', 'Failed to withdraw application. Please try again.');
        }
    }
/**
 * Withdraw/destroy an application
 *
 * @param  \App\Models\Application  $application
 * @return \Illuminate\Http\RedirectResponse
 */
public function destroy(Application $application)
{
    // Temporarily commented out suspension check
    // if (!Auth::user()->is_active) {
    //     return $this->accountSuspendedRedirect('Your account is currently suspended.');
    // }
    
    // Verify user owns this application
    if ($application->user_id !== Auth::id()) {
        return $this->unauthorizedAccess('You do not have permission to withdraw this application');
    }
    
    // Check if application can be withdrawn
    if (!in_array($application->status, ['pending', 'reviewing'])) {
        return back()->with('error', 'This application can no longer be withdrawn because it is already ' . $application->status);
    }
    
    try {
        // Add a note before deletion
        $application->notes = ($application->notes ? $application->notes . "\n\n" : '') . 
                            "Application withdrawn by applicant on " . now()->format('Y-m-d H:i:s');
        $application->save();
        
        // Use soft delete
        $application->delete();
        
        // Log the withdrawal
        Log::info('Application withdrawn', [
            'application_id' => $application->id,
            'user_id' => Auth::id(),
            'tour_id' => $application->tour_id
        ]);
        
        return redirect()->route('applications.my.index')
            ->with('success', 'Your application has been withdrawn successfully');
    } catch (\Exception $e) {
        Log::error('Failed to withdraw application: ' . $e->getMessage(), [
            'application_id' => $application->id,
            'user_id' => Auth::id(),
            'error' => $e->getMessage()
        ]);
        
        return back()->with('error', 'Failed to withdraw application. Please try again. ' . 
            (config('app.debug') ? 'Error: ' . $e->getMessage() : ''));
    }
}

    /*
     * Private helper methods
     */
    
    /**
     * Check if a user can apply for an tour
     *
     * @param  \App\Models\Tour  $tour
     * @return mixed  True if eligible, redirect response if not
     */
    private function checktourApplicationEligibility(Tour $tour)
    {
        // Check if tour is active
        if (!$tour->is_active) {
            return redirect()->route('tours.show', $tour)
                ->with('error', 'This tour is no longer accepting applications.');
        }
        
        // Check if deadline has passed
        if ($tour->deadline_date && $tour->deadline_date < now()) {
            return redirect()->route('tours.show', $tour)
                ->with('error', 'The application deadline for this tour has passed.');
        }
        
        // Check if user has already applied
        $existingApplication = Application::where('user_id', Auth::id())
            ->where('tour_id', $tour->id)
            ->first();
            
        if ($existingApplication) {
            return redirect()->route('tours.show', $tour)
                ->with('error', 'You have already applied for this tour.');
        }
        
        return true;
    }


    
    /**
     * Redirect for suspended accounts
     *
     * @param  string  $message
     * @return \Illuminate\Http\RedirectResponse
     */
    private function accountSuspendedRedirect($message = 'Your account is currently suspended.')
    {
        return redirect()->route('account.suspended')
            ->with('error', $message);
    }

    /**
     * Return a 403 response or redirect
     *
     * @param  string  $message
     * @return \Illuminate\Http\Response
     */
    private function unauthorizedAccess($message = 'You do not have permission to access this resource')
    {
        abort(403, $message);
    }

    /**
     * Builds the main applications query with filters
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function buildApplicationsQuery(Request $request)
    {
        // Base query with relationships
        $query = Application::query()
            ->with(['user', 'user.tourist', 'tour']);
        
        // Employer can only see their own tours' applications
        if (Auth::user()->employer) {
            $employerId = Auth::user()->employer->id;
            $query->whereHas('tour', function($q) use ($employerId) {
                $q->where('employer_id', $employerId);
            });
        }
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('tour_id')) {
            $query->where('tour_id', $request->tour_id);
        }
        
        if ($request->filled('search')) {
            $this->applySearchFilter($query, $request->search);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Apply sorting
        $this->applySorting($query, $request);
        
        return $query;
    }

    /**
     * Apply search filter to applications query
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return void
     */
    private function applySearchFilter($query, $search)
    {
        $query->where(function($q) use ($search) {
            // Search in user name or email
            $q->whereHas('user', function($subq) use ($search) {
                $subq->where('name', 'like', "%{$search}%")
                     ->orWhere('email', 'like', "%{$search}%");
            })
            // Or in application fields
            ->orWhere('institution', 'like', "%{$search}%")
            ->orWhere('field_of_study', 'like', "%{$search}%")
            ->orWhere('skills', 'like', "%{$search}%");
        });
    }

    /**
     * Apply filters for the tourist's applications page
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    private function applyTouristApplicationFilters($query, Request $request)
    {
        // Apply status filter
        if ($request->filled('status')) {
            $query->where('applications.status', $request->status);
        }
        
        // Apply search if provided
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('tour', function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhereHas('employer', function($q2) use ($search) {
                      $q2->where('employer_name', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        // Apply sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'date_asc':
                    $query->oldest('applications.created_at');
                    break;
                case 'company_asc':
                    $query->join('tours', 'applications.tour_id', '=', 'tours.id')
                          ->join('employers', 'tours.employer_id', '=', 'employers.id')
                          ->orderBy('employers.employer_name', 'asc')
                          ->select('applications.*')
                          ->where('applications.user_id', Auth::id());
                    break;
                case 'company_desc':
                    $query->join('tours', 'applications.tour_id', '=', 'tours.id')
                          ->join('employers', 'tours.employer_id', '=', 'employers.id')
                          ->orderBy('employers.employer_name', 'desc')
                          ->select('applications.*')
                          ->where('applications.user_id', Auth::id());
                    break;
                case 'date_desc':
                default:
                    $query->latest('applications.created_at');
                    break;
            }
        } else {
            // Default sorting
            $query->latest('applications.created_at');
        }
    }

    /**
     * Apply sorting to the applications query
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    private function applySorting($query, Request $request)
    {
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        // Whitelist allowed sort columns for security
        $allowedSortFields = ['created_at', 'updated_at', 'status'];
        
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest(); // Default sort
        }
    }

    /**
     * Get available tours for the current user
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getToursForCurrentUser()
    {
        if (Auth::user()->isAdmin()) {
            return Tour::orderBy('title')->get();
        } else {
            return Auth::user()->employer->tours()->orderBy('title')->get();
        }
    }

    /**
     * Get application statistics by status for a user
     *
     * @param  int  $userId
     * @return array
     */
    private function getApplicationStatisticsForUser($userId)
    {
        return [
            'pendingCount' => Application::where('user_id', $userId)->where('status', 'pending')->count(),
            'reviewingCount' => Application::where('user_id', $userId)->where('status', 'reviewing')->count(),
            'interviewedCount' => Application::where('user_id', $userId)->where('status', 'interviewed')->count(),
            'acceptedCount' => Application::where('user_id', $userId)->where('status', 'accepted')->count(),
            'rejectedCount' => Application::where('user_id', $userId)->where('status', 'rejected')->count(),
        ];
    }

    /**
     * Process uploaded files and create an application record
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tour  $tour
     * @param  \App\Models\Tourist  $tourist
     * @param  array  $validated
     * @return \App\Models\Application
     */
    private function processAndStoreApplication(Request $request, Tour $tour, Tourist $tourist, array $validated)
    {
        // Handle resume upload - if no new resume uploaded, use profile resume
        $resumePath = $request->hasFile('resume')
            ? $request->file('resume')->store('resumes', 'public')
            : $tourist->resume_path;
        
        // Handle transcript upload
        $transcriptPath = $request->hasFile('transcript')
            ? $request->file('transcript')->store('transcripts', 'public')
            : null;
        
        // Create application record
        $application = new Application([
            'user_id' => Auth::id(),
            'tour_id' => $tour->id,
            'phone' => $tourist->phone,
            'education' => $tourist->education_level,
            'institution' => $tourist->institution,
            'field_of_study' => $tourist->field_of_study,
            'skills' => $tourist->skills,
            'resume_path' => $resumePath,
            'cover_letter' => $validated['cover_letter'] ?? null,
            'transcript_path' => $transcriptPath,
            'profile_photo_path' => $tourist->profile_photo_path ?? null,
            'why_interested' => $validated['why_interested'],
            'availability' => $validated['availability'],
            'status' => 'pending'
        ]);
        
        $application->save();
        return $application;
    }

    /**
     * Handle application submission error
     *
     * @param  \Exception  $e
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tour  $tour
     * @return \Illuminate\Http\RedirectResponse
     */
    private function handleApplicationError(\Exception $e, Request $request, Tour $tour)
    {
        // Log the error
        Log::error('Application submission failed: ' . $e->getMessage(), [
            'user_id' => Auth::id(),
            'tour_id' => $tour->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        // Clean up any newly uploaded files if the application failed
        if ($request->hasFile('resume') && isset($resumePath) && Storage::disk('public')->exists($resumePath)) {
            Storage::disk('public')->delete($resumePath);
        }
        
        if (isset($transcriptPath) && Storage::disk('public')->exists($transcriptPath)) {
            Storage::disk('public')->delete($transcriptPath);
        }
        
        return back()->withInput()
            ->with('error', 'There was a problem submitting your application: ' . $e->getMessage());
    }

    /**
     * Check if user has permission to view an application
     *
     * @param  \App\Models\Application  $application
     * @return bool
     */
    private function canViewApplication(Application $application)
    {
        // Application owner can view
        if ($application->user_id == Auth::id()) {
            return true;
        }
        
        // Employer of the tour can view
        if (Auth::user()->employer && Auth::user()->employer->id == $application->tour->employer_id) {
            return true;
        }
        
        // Admin can view
        if (Auth::user()->isAdmin()) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if user has permission to manage an application
     *
     * @param  \App\Models\Application  $application
     * @return bool
     */
    private function canManageApplication(Application $application)
    {
        // Only employer of the tour or admin can manage
        if (Auth::user()->employer && Auth::user()->employer->id == $application->tour->employer_id) {
            return true;
        }
        
        if (Auth::user()->isAdmin()) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if user can access application files
     *
     * @param  \App\Models\Application  $application
     * @return bool
     */
    private function canAccessApplicationFiles(Application $application)
    {
        return $application->user_id === Auth::id() || 
               Auth::user()->isAdmin() || 
               (Auth::user()->employer && Auth::user()->employer->id === $application->tour->employer_id);
    }

    /**
     * Update application status and add notes
     *
     * @param  \App\Models\Application  $application
     * @param  array  $validated
     * @return void
     */
    private function updateApplicationStatusAndNotes(Application $application, array $validated)
    {
        // Update the application status
        $application->status = $validated['status'];
        
        // Set appropriate date fields based on status
        if ($validated['status'] === 'interviewed' && !$application->interview_date) {
            $application->interview_date = now();
        }
        
        if (in_array($validated['status'], ['accepted', 'rejected']) && !$application->response_date) {
            $application->response_date = now();
        }
        
        // Add notes if provided
        if (isset($validated['notes']) && !empty($validated['notes'])) {
            $this->addNotesToApplication($application, $validated['notes']);
        }
        
        $application->save();
        
        // Log the status change
        Log::info('Application status updated', [
            'application_id' => $application->id,
            'old_status' => $application->getOriginal('status'),
            'new_status' => $application->status,
            'updated_by' => Auth::id()
        ]);
    }

    /**
     * Add notes to an application with user info and timestamp
     *
     * @param  \App\Models\Application  $application
     * @param  string  $notes
     * @return void
     */
    private function addNotesToApplication(Application $application, $notes)
    {
        // Format notes with timestamp and user info
        $noteWithMeta = "[" . now()->format('M j, Y g:i A') . " - " . Auth::user()->name . "]\n";
        $noteWithMeta .= $notes . "\n\n";
        
        // Append to existing notes or create new
        if ($application->notes) {
            $application->notes .= $noteWithMeta;
        } else {
            $application->notes = $noteWithMeta;
        }
        
        $application->save();
    }

    /**
     * Download a file with error handling
     *
     * @param  string  $path
     * @param  string  $filename
     * @param  string  $errorMessage
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    private function downloadFile($path, $filename, $errorMessage)
    {
        if (!$path || !Storage::disk('public')->exists($path)) {
            return back()->with('error', $errorMessage);
        }
        
        return response()->download(
            storage_path('app/public/' . $path), 
            $filename
        );
    }
}