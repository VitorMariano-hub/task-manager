<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\User;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getTimeLeftAttribute()
    {
        if ($this->status === 'completed') {
            return 0;
        }

        $timezone = 'America/Sao_Paulo';
        $now = Carbon::now($timezone);
        $endOfDay = Carbon::today($timezone)->setHour(23)->setMinute(59)->setSecond(59);

        return max(0, $now->diffInSeconds($endOfDay, false));
    }

    public static function userReachedDailyLimit(User $user, int $limit = 10): bool
    {
        $timezone = 'America/Sao_Paulo';
        $startOfDay = Carbon::today($timezone)->startOfDay()->timezone('UTC');
        $endOfDay = Carbon::today($timezone)->endOfDay()->timezone('UTC');

        return self::where('user_id', $user->id)
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->count() >= $limit;
    }

}
