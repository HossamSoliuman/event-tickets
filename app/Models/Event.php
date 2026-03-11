<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'venue',
        'date',
        'total_tickets',
        'available_tickets',
        'price',          
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'date'               => 'datetime',
        'price'              => 'integer',
        'total_tickets'      => 'integer',
        'available_tickets'  => 'integer',
        'is_active'          => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /** Only upcoming, active events */
    public function scopeUpcoming($query)
    {
        return $query
            ->where('is_active', true)
            ->where('date', '>', now())
            ->orderBy('date', 'asc');
    }

    /** Events that still have tickets */
    public function scopeAvailable($query)
    {
        return $query->where('available_tickets', '>', 0);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /** Return price in dollars for display */
    public function getPriceInDollarsAttribute(): float
    {
        return $this->price / 100;
    }

    public function isSoldOut(): bool
    {
        return $this->available_tickets <= 0;
    }
}
