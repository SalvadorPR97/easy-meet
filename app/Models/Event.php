<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'location',
        'latitude',
        'longitude',
        'city',
        'image_url',
        'date',
        'start_time',
        'end_time',
        'min_participants',
        'max_participants',
        'price',
        'description',
        'only_women',
        'only_men',
        'category_id',
        'subcategory_id',
        'owner_id'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'events_users');
    }
}
