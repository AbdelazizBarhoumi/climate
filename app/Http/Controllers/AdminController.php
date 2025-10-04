<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use App\Models\Employer;
use App\Models\Tour;
use App\Models\Application;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

class AdminController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Authentication will be handled in the routes file
    }

    /**
     * Display admin dashboard with enhanced analytics
     */
    public function dashboard()
    {
        // Authorization check
        if (!Gate::allows('access-admin')) {
            abort(403, 'Unauthorized action.');
        }

        // Basic stats
        $stats = [
            'users' => User::count(),
            'employers' => Employer::whereHas('user', function($q) {
                $q->whereDoesntHave('admin');
            })->count(),
            'tours' => Tour::count(),
            'applications' => Application::count(),
            'admin_count' => Admin::count(),
        ];

        // Recent activity
        $recentTours = Tour::with('employer')->latest()->take(5)->get();
        $recentApplications = Application::with(['user', 'tour'])->latest()->take(5)->get();
        $recentUsers = User::latest()->take(5)->get();

        // Analytics data - Using SQLite's strftime function
        $monthlyStats = [
            'applications' => Application::selectRaw('COUNT(*) as count, strftime(\'%m\', created_at) as month')
                ->whereRaw('strftime(\'%Y\', created_at) = ?', [date('Y')])
                ->groupBy('month')
                ->pluck('count', 'month')
                ->toArray(),
            'tours' => Tour::selectRaw('COUNT(*) as count, strftime(\'%m\', created_at) as month')
                ->whereRaw('strftime(\'%Y\', created_at) = ?', [date('Y')])
                ->groupBy('month')
                ->pluck('count', 'month')
                ->toArray(),
            'users' => User::selectRaw('COUNT(*) as count, strftime(\'%m\', created_at) as month')
                ->whereRaw('strftime(\'%Y\', created_at) = ?', [date('Y')])
                ->groupBy('month')
                ->pluck('count', 'month')
                ->toArray(),
        ];

        return view('admin.dashboard', compact(
            'stats',
            'recentTours',
            'recentApplications',
            'recentUsers',
            'monthlyStats'
        ));
    }

    /**
     * Display user management page with enhanced filtering
     */
    public function users(Request $request)
    {
        if (!Gate::allows('manage-users')) {
            abort(403, 'Unauthorized action.');
        }

        $query = User::with(['admin', 'employer']);

        // Filter by user type
        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'admins':
                    $query->whereHas('admin');
                    break;
                case 'employers':
                    $query->whereHas('employer')
                    ->whereDoesntHave('admin'); // Only show employers who aren't admins
                    break;
                case 'regular':
                    $query->whereDoesntHave('admin')->whereDoesntHave('employer');
                    break;
            }
        }

        // Search functionality
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15)
            ->appends($request->all());

        return view('admin.users', compact('users'));
    }

    /**
     * Show user details
     */
    public function showUser(User $user)
    {
        if (!Gate::allows('manage-users')) {
            abort(403, 'Unauthorized action.');
        }

        $user->load('admin', 'employer');

        // Get related data based on user type
        $userData = [];

        if ($user->employer) {

            $userData['tours'] = Tour::where('employer_id', $user->employer->id)
                ->withCount('applications')
                ->get();
                $userData['totalApplicants'] = Application::whereHas('tour', function($query) use ($user) {
                    $query->where('employer_id', $user->employer->id);
                  })
                  ->count();
        } else {
            $userData['applications'] = Application::where('application_id', $user->id)
                ->with('tour')
                ->get();
        }

        return view('admin.user-details', compact('user', 'userData'));
    }

    /**
     * Promote a user to admin
     */
    /**
     * Promote a user to admin
     */
    public function promote(User $user)
    {
        // Authorization check
        if (!Gate::allows('promote-users')) {
            abort(403, 'Unauthorized action.');
        }

        // Check if already admin
        if ($user->isAdmin()) {
            return back()->with('info', 'User is already an admin.');
        }

        DB::beginTransaction();

        try {
                $user->is_active = false;
                $user->save();
                
                Log::info('Setting regular user inactive during admin promotion', [
                    'user_id' => $user->id
                ]);
            

            // Create admin record
            Admin::create([
                'user_id' => $user->id,
                'role' => 'admin',
            ]);

            DB::commit();
            
            return back()->with('success', 'User promoted to admin successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to promote user to admin', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to promote user to admin: ' . $e->getMessage());
        }
    }

    /**
     * Remove admin privileges
     */
    public function demote(User $user)
    {
        // Authorization check
        if (auth()->user()->admin->role !== 'super_admin' && $user->admin->role === 'super_admin') {
            return back()->with('error', 'You do not have permission to demote a super admin.');
        }

        if ($user->admin) {
            $user->admin->delete();
            $user->is_active = true; // Reactivate user
            $user->save();
            Log::info('User demoted from admin', [
                'user_id' => $user->id
            ]);

            return back()->with('success', 'Admin privileges removed successfully.');
        }

        return back()->with('info', 'User is not an admin.');
    }

    /**
     * Toggle user account status (active/suspended)
     */
    public function toggleUserStatus(User $user)
    {
        if (!Gate::allows('manage-users')) {
            abort(403, 'Unauthorized action.');
        }

        // Don't allow suspending yourself
        if (Auth::user()->id === $user->id) {
            return back()->with('error', 'You cannot suspend your own account.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activated' : 'suspended';
        return back()->with('success', "User account has been {$status}.");
    }

    /**
     * Display all tours with management options
     */
    public function tours(Request $request)
    {
        if (!Gate::allows('access-admin')) {
            abort(403, 'Unauthorized action.');
        }

        $query = Tour::with('employer', 'tags')->withCount('applications');

        // Filter by active status if requested
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
            if ($request->has('employer_id') && !empty($request->employer_id)) {
                $query->where('employer_id', $request->employer_id);
            }
        }

        // Filter by search term
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('location', 'like', "%{$searchTerm}%")
                    ->orWhereHas('employer', function ($eq) use ($searchTerm) {
                        $eq->where('company_name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        $tours = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends($request->all());

        return view('admin.tours', compact('tours'));
    }

    /**
     * Show specific tour details
     */
    public function showtour(Tour $tour)
    {
        if (!Gate::allows('access-admin')) {
            abort(403, 'Unauthorized action.');
        }

        $tour->load('employer', 'tags', 'applications.user');

        return view('admin.tour-details', compact('tour'));
    }

    /**
     * Toggle tour approval status
     */
    public function toggletourStatus(Tour $tour)
    {
        if (!Gate::allows('access-admin')) {
            abort(403, 'Unauthorized action.');
        }

        $tour->is_active = !$tour->is_active;
        $tour->save();

        $status = $tour->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Tour has been {$status}.");
    }

    /**
     * Delete an tour
     */
    public function deletetour(Tour $tour)
    {
        if (!Gate::allows('access-admin')) {
            abort(403, 'Unauthorized action.');
        }

        $title = $tour->title;
        $tour->delete();

        return redirect()->route('admin.tours')
            ->with('success', "Tour '{$title}' has been deleted.");
    }
    public function deleteApplication(Application $application)
    {
        if (!Gate::allows('access-admin')) {
            abort(403, 'Unauthorized action.');
        }
        $application->delete();

        return redirect()->route('admin.applications')
            ->with('success', 'Application has been deleted successfully.');
    }

    /**
     * Display all applications
     */
    public function applications(Request $request)
    {
        if (!Gate::allows('access-admin')) {
            abort(403, 'Unauthorized action.');
        }

        $query = Application::with(['user', 'tour.employer']);

        // Filter by status if provided
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filter by tour_id
        if ($request->has('tour_id') && $request->tour_id != '') {
            $query->where('tour_id', $request->tour_id);
        }
        
        // Filter by employer_id (through the tour relationship)
        if ($request->has('employer_id') && $request->employer_id != '') {
            $query->whereHas('tour', function($q) use ($request) {
                $q->where('employer_id', $request->employer_id);
            });
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Filter by search term
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->whereHas('user', function($query) use ($searchTerm) {
                    $query->where('name', 'like', "%{$searchTerm}%")
                          ->orWhere('email', 'like', "%{$searchTerm}%");
                })
                ->orWhereHas('tour', function($query) use ($searchTerm) {
                    $query->where('title', 'like', "%{$searchTerm}%")
                          ->orWhere('description', 'like', "%{$searchTerm}%");
                });
                
                // Only search in tourist skills if the relationship exists
                if (method_exists($q->getModel()->user->getRelated(), 'tourist')) {
                    $q->orWhereHas('user.tourist', function($query) use ($searchTerm) {
                        $query->where('skills', 'like', "%{$searchTerm}%");
                    });
                }
            });
        }

        $applications = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends($request->all());

        return view('admin.applications', compact('applications'));
    }

    /**
     * Show application details
     */
    public function showApplication(Application $application)
    {
        if (!Gate::allows('access-admin')) {
            abort(403, 'Unauthorized action.');
        }

        $application->load('user', 'tour.employer');

        return view('admin.application-details', compact('application'));
    }

    /**
     * Update application status
     */
    public function UpdateNotes(Request $request, Application $application)
    {
        if (!Gate::allows('access-admin')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);



        try {
            $this->addNotesToApplication($application, $validated['admin_notes']);
            return back()->with('success', 'Application notes have been updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update application notes: ' . $e->getMessage());
            return back()->with('error', 'Failed to update notes. Please try again.');
        }
    }
    private function addNotesToApplication(Application $application, $admin_notes)
    {
        // Format admin_notes with timestamp and user info
        $noteWithMeta = "[" . now()->format('M j, Y g:i A') . " - " . Auth::user()->name . "]\n";
        $noteWithMeta .= $admin_notes . "\n\n";
        
        // Append to existing notes or create new
        if ($application->admin_notes) {
            $application->admin_notes .= $noteWithMeta;
        } else {
            $application->admin_notes = $noteWithMeta;
        }
        
        $application->save();
    }
}