<?php

namespace App\Domain\Devotee;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Devotee extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'name', 'whatsapp', 'dob', 'anniversary',
        'city', 'fb_consent', 'status', 'joined_at', 'password',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'dob'        => 'date',
        'anniversary' => 'date',
        'fb_consent'  => 'boolean',
        'joined_at'   => 'datetime',
    ];

    // ── Scopes ────────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeBirthdayToday($query)
    {
        $today = now()->format('m-d');
        if (config('database.default') === 'sqlite') {
            return $query->whereRaw("strftime('%m-%d', dob) = ?", [$today]);
        }
        return $query->whereRaw("DATE_FORMAT(dob, '%m-%d') = ?", [$today]);
    }

    public function scopeAnniversaryToday($query)
    {
        $today = now()->format('m-d');
        if (config('database.default') === 'sqlite') {
            return $query->whereRaw("strftime('%m-%d', anniversary) = ?", [$today])
                         ->whereNotNull('anniversary');
        }
        return $query->whereRaw("DATE_FORMAT(anniversary, '%m-%d') = ?", [$today])
                     ->whereNotNull('anniversary');
    }

    public function scopeWithFbConsent($query)
    {
        return $query->where('fb_consent', true)->where('status', 'active');
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('whatsapp', 'like', "%{$term}%");
        });
    }

    public static function scopeCelebratingMilestone($query, $type, $period)
    {
        if ($period === 'all' || !$period) {
            if ($type === 'birthday') {
                return $query->whereNotNull('dob');
            } elseif ($type === 'anniversary') {
                return $query->whereNotNull('anniversary');
            }
            return $query;
        }

        $startDate = null;
        $endDate = null;

        if ($period === 'today') {
            $startDate = now();
            $endDate = now();
        } elseif ($period === 'next_7_days') {
            $startDate = now()->addDay();
            $endDate = now()->addDays(7);
        } elseif ($period === 'next_week') {
            $startDate = now()->addWeek()->startOfWeek();
            $endDate = now()->addWeek()->endOfWeek();
        } else {
            return $query;
        }

        return $query->where(function ($q) use ($type, $startDate, $endDate) {
            if ($type === 'birthday' || $type === 'any' || !$type || $type === 'all') {
                $q->where(function ($sub) use ($startDate, $endDate) {
                    $sub->whereNotNull('dob');
                    self::applyDateRangeFilter($sub, 'dob', $startDate, $endDate);
                });
            }

            if ($type === 'anniversary' || $type === 'any' || !$type || $type === 'all') {
                $or = ($type === 'any' || !$type || $type === 'all') ? 'orWhere' : 'where';
                $q->$or(function ($sub) use ($startDate, $endDate) {
                    $sub->whereNotNull('anniversary');
                    self::applyDateRangeFilter($sub, 'anniversary', $startDate, $endDate);
                });
            }
        });
    }

    protected static function applyDateRangeFilter($query, $column, $startDate, $endDate)
    {
        $isSqlite = config('database.default') === 'sqlite';

        $startMonthDay = $startDate->format('m-d');
        $endMonthDay = $endDate->format('m-d');

        if ($startMonthDay <= $endMonthDay) {
            if ($isSqlite) {
                $query->whereRaw("strftime('%m-%d', {$column}) >= ? AND strftime('%m-%d', {$column}) <= ?", [$startMonthDay, $endMonthDay]);
            } else {
                $query->whereRaw("DATE_FORMAT({$column}, '%m-%d') >= ? AND DATE_FORMAT({$column}, '%m-%d') <= ?", [$startMonthDay, $endMonthDay]);
            }
        } else {
            // Crosses year boundary (e.g. Dec 28 to Jan 3)
            if ($isSqlite) {
                $query->where(function ($q) use ($column, $startMonthDay, $endMonthDay) {
                    $q->whereRaw("strftime('%m-%d', {$column}) >= ?", [$startMonthDay])
                      ->orWhereRaw("strftime('%m-%d', {$column}) <= ?", [$endMonthDay]);
                });
            } else {
                $query->where(function ($q) use ($column, $startMonthDay, $endMonthDay) {
                    $q->whereRaw("DATE_FORMAT({$column}, '%m-%d') >= ?", [$startMonthDay])
                      ->orWhereRaw("DATE_FORMAT({$column}, '%m-%d') <= ?", [$endMonthDay]);
                });
            }
        }
    }

    // ── Relations ─────────────────────────────────────────────────
    public function broadcastLogs(): HasMany
    {
        return $this->hasMany(\App\Domain\Messaging\BroadcastLog::class, 'devotee_id');
    }

    public function wishDeliveryLogs(): HasMany
    {
        return $this->hasMany(\App\Domain\Messaging\WishDeliveryLog::class, 'devotee_id');
    }

    // ── Helpers ───────────────────────────────────────────────────
    public function getFormattedDobAttribute(): ?string
    {
        return $this->dob?->format('d M');
    }

    public function getFormattedAnniversaryAttribute(): ?string
    {
        return $this->anniversary?->format('d M');
    }

    public function getAvatarInitialsAttribute(): string
    {
        $parts = explode(' ', trim($this->name));
        if (count($parts) >= 2) {
            return strtoupper($parts[0][0] . $parts[1][0]);
        }
        return strtoupper(substr($this->name, 0, 2));
    }
}
