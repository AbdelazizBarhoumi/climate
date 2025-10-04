<?php

namespace App\Policies;

use App\Models\Tour;
use App\Models\User;


class TourPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    
    public function update(User $user, Tour $tour): bool
   {
     return $tour->employer->user->is($user);
   }
   public function delete(User $user, Tour $tour): bool
   {
    return ($tour->employer->user->is($user) && $tour->employer->is_admin) ?? false;
   }

   public function view(User $user, Tour $tour): bool
   {
       return $tour->employer->user->is($user) || $tour->is_public;
   }

}
