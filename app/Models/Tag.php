<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /** @use HasFactory<\Database\Factories\TagFactory> */
    use HasFactory;
    protected $fillable = ['name'];
    public function tours()
    {
        return $this->belongsToMany(Tour::class, 'tour_tag', 'tag_id', 'tour_id');
    }

}
