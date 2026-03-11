<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reaction extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'board_post_id',
        'user_id',
        'emoji',
    ];

    /**
     * @return BelongsTo<BoardPost, $this>
     */
    public function boardPost(): BelongsTo
    {
        return $this->belongsTo(BoardPost::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
