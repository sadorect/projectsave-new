<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use App\Models\Partner;
use App\Models\Activity;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
      * The attributes that are mass assignable.
      *
      * @var array<int, string>
      */
    protected $fillable = [
        'name',
        'email',
        'password',
        'birthday',
        'wedding_anniversary'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birthday' => 'date',
        'wedding_anniversary' => 'date'
    ];


public function activities()
{
    return $this->hasMany(Activity::class);
}



public function partnerships()
{
    return $this->hasMany(Partner::class);
}



public function getNextCelebrationDateAttribute()
{
    $nextBirthday = $this->birthday ? Carbon::parse($this->birthday)->setYear(now()->year) : null;
    $nextAnniversary = $this->wedding_anniversary ? Carbon::parse($this->wedding_anniversary)->setYear(now()->year) : null;
    
    if ($nextBirthday && $nextBirthday->isPast()) {
        $nextBirthday->addYear();
    }
    
    if ($nextAnniversary && $nextAnniversary->isPast()) {
        $nextAnniversary->addYear();
    }
    
    if (!$nextBirthday && !$nextAnniversary) {
        return null;
    }
    
    if (!$nextBirthday) return $nextAnniversary;
    if (!$nextAnniversary) return $nextBirthday;
    
    return $nextBirthday->min($nextAnniversary);
}

}