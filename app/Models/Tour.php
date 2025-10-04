<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Tour extends Model
{
    use HasFactory;
    /**
     * @property Carbon|null $deadline_date
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

   protected $fillable = [
       'employer_id',
       'title',
       'price',
       'location',
       'destinations',
       'schedule',
       'description',
       'duration',
       'deadline_date',
       'featured',
       'is_active',
       'view_count',
   ];

   /**
    * The attributes that should be cast.
    *
    * @var array<string, string>
    */
   protected $casts = [
       'featured' => 'boolean',
       'deadline_date' => 'date',
       'destinations' => 'array',
   ];

    /**
     * Get the employer that owns the tour.
     */
    public function employer()
    {
        return $this->belongsTo(Employer::class);
    }

    /**
     * Get all applications for this tour.
     */
    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Add a tag to the tour.
     *
     * @param string $tagName
     * @return $this
     */
    public function tag(string $tagName)
    {
        $tag = Tag::firstOrCreate(['name' => $tagName]);
        $this->tags()->attach($tag);
        return $this;
    }

    /**
     * Get all tags associated with the tour.
     */
    public function tags()
    {
        // Explicitly specify the pivot table and foreign keys to match the
        // migration and the Tag model which use the 'tour_tag' pivot table.
        return $this->belongsToMany(Tag::class, 'tour_tag', 'tour_id', 'tag_id');
    }

    /**
     * Get random tags for this tour.
     *
     * @param int $count
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function randomTags($count = 4)
    {
        return $this->tags->count() <= $count 
            ? $this->tags 
            : $this->tags->random($count);
    }

    /**
     * Get random tags except the specified one.
     *
     * @param int $excludeId
     * @param int $count
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function randomTagsExcept($excludeId, $count = 3)
    {
        $filteredTags = $this->tags->where('id', '!=', $excludeId);
        return $filteredTags->count() <= $count 
            ? $filteredTags 
            : $filteredTags->random(min($count, $filteredTags->count()));
    }

    /**
     * Get pending applications count.
     *
     * @return int
     */
    public function pendingApplicationsCount()
    {
        return $this->applications()->where('status', 'pending')->count();
    }

    /**
     * Get accepted applications count.
     *
     * @return int
     */
    public function acceptedApplicationsCount()
    {
        return $this->applications()->where('status', 'accepted')->count();
    }

    /**
     * Check if the user has already applied to this tour.
     *
     * @param User $user
     * @return bool
     */
    public function hasApplicant(User $user)
    {
        return $this->applications()->where('user_id', $user->id)->exists();
    }

    /**
     * Scope a query to only include featured tours.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }
    public function isExpired()
{
    return $this->deadline_date instanceof Carbon && $this->deadline_date->isPast();
}


// Add this accessor for formatted deadline_date
public function getdeadline_dateFormattedAttribute()
{
    return $this->deadline_date instanceof Carbon ? $this->deadline_date->format('M d, Y') : 'No deadline_date';
}
public function incrementViewCount()
{
    $this->increment('view_count');
}

/**
 * Get all destinations for this tour.
 *
 * @return array
 */
public function getDestinations()
{
    return $this->destinations ?? [];
}

/**
 * Get the starting location.
 *
 * @return string
 */
public function getStartingLocation()
{
    if (!empty($this->destinations)) {
        return $this->destinations[0]['city'] ?? $this->location;
    }
    return $this->location;
}

/**
 * Get total number of days from duration string.
 *
 * @return int
 */
public function getTotalDays()
{
    if (!$this->duration) {
        return 1;
    }
    
    // Extract number from duration string (e.g., "7 days" -> 7)
    preg_match('/(\d+)/', $this->duration, $matches);
    return isset($matches[1]) ? (int)$matches[1] : 1;
}

/**
 * Format destinations as a readable string.
 *
 * @return string
 */
public function getDestinationsString()
{
    $destinations = $this->getDestinations();
    if (empty($destinations)) {
        return $this->location ?? 'Not specified';
    }
    
    $cities = array_map(function($dest) {
        return $dest['city'];
    }, $destinations);
    
    if (count($cities) <= 3) {
        return implode(' → ', $cities);
    }
    
    return $cities[0] . ' → ' . $cities[1] . ' → ... → ' . end($cities);
}

}