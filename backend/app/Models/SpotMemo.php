<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpotMemo extends Model
{
    /** @use HasFactory<\Database\Factories\SpotMemoFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'spot_id',
        'user_id',
        'body',
    ];

    /**
     * @return BelongsTo<Spot, $this>
     */
    public function spot(): BelongsTo
    {
        return $this->belongsTo(Spot::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
