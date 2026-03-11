<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Spot extends Model
{
    /** @use HasFactory<\Database\Factories\SpotFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'description',
        'address',
        'latitude',
        'longitude',
        'business_hours',
        'price_info',
        'google_maps_url',
        'image_url',
        'category',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return HasMany<SpotMemo, $this>
     */
    public function memos(): HasMany
    {
        return $this->hasMany(SpotMemo::class);
    }

    /**
     * @return HasMany<ItineraryItem, $this>
     */
    public function itineraryItems(): HasMany
    {
        return $this->hasMany(ItineraryItem::class);
    }

    /**
     * @return HasMany<Photo, $this>
     */
    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }
}
