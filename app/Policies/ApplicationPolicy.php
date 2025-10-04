<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApplicationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create an application.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Tour  $tour
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, Tour $tour)
    {
        // Check if user is active
        if (!$user->is_active) {
            return false;
        }

        // Check if user is an employer or admin (they shouldn't apply)
        if ($user->isEmployer() || $user->isAdmin()) {
            return false;
        }

        // Check if user has already applied
        if ($user->hasAppliedTo($tour)) {
            return false;
        }

        // Check if tour deadline has passed
        if ($tour->deadline_date && $tour->deadline_date->isPast()) {
            return false;
        }

        // Check if tour is active
        if (!$tour->is_active) {
            return false;
        }

        // Check if employer is active
        if (!$tour->employer->user->is_active) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can view their application.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Application $application)
    {
        // User can view their own application
        if ($user->id === $application->user_id) {
            return true;
        }

        // Employer can view applications to their tours
        if ($user->isEmployer()) {
            return $user->employer->id === $application->tour->employer_id;
        }

        // Admins can view all applications
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can withdraw an application.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function withdraw(User $user, Application $application)
    {
        // Only the user who created the application can withdraw it
        // And only if it's still pending
        return $user->id === $application->user_id && 
               $application->status === 'pending';
    }
}