<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property TaskStatus $status
 * @property int $position
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'position',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'position' => 'integer',
        ];
    }

    /**
     * Default ordering: manual position first, newest first for ties.
     *
     * @param  Builder<Task>  $query
     */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('position')->orderByDesc('id');
    }

    /**
     * Filter by a (partial) title.
     *
     * @param  Builder<Task>  $query
     */
    public function scopeSearch(Builder $query, ?string $term): void
    {
        $query->when($term, function (Builder $q, string $term) {
            $escaped = addcslashes($term, '%_\\');
            $q->where('title', 'like', "%{$escaped}%");
        });
    }

    /**
     * Filter by status (ignored when empty or invalid).
     *
     * @param  Builder<Task>  $query
     */
    public function scopeWithStatus(Builder $query, ?string $status): void
    {
        $query->when(
            $status !== null && TaskStatus::tryFrom($status),
            fn (Builder $q) => $q->where('status', $status),
        );
    }
}
