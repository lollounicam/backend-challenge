<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DebtCase extends Model
{
    use HasFactory;

    public const STATUS_NEW = 'new';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_CLOSED = 'closed';

    public const ALLOWED_TRANSITIONS = [
        self::STATUS_NEW => [
            self::STATUS_NEW,
            self::STATUS_IN_PROGRESS,
        ],
        self::STATUS_IN_PROGRESS => [
            self::STATUS_IN_PROGRESS,
            self::STATUS_CLOSED,
        ],
        self::STATUS_CLOSED => [
            self::STATUS_CLOSED,
        ],
    ];

    public $timestamps = false;

    protected $fillable = [
        'client_id',
        'description',
        'debt_amount',
    ];

    protected function casts(): array
    {
        return [
            'debt_amount' => 'decimal:2',
            'opened_at' => 'datetime',
        ];
    }

    public function canTransitionTo(string $status): bool
    {
        return in_array(
            $status,
            self::ALLOWED_TRANSITIONS[$this->status],
            true,
        );
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
