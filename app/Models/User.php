<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'patronymic',
        'email',
        'password',
        'birth_date',
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
        'email_verified_at' => 'datetime',
        'birth_date' => 'date:Y-m-d',
        'password' => 'hashed',
    ];
    protected function name(): Attribute // <-- Добавить Accessor для полного имени
    {
        return Attribute::make(
            get: fn () => $this->last_name . ' ' . $this->first_name . ' ' . $this->patronymic,
        );
    }

    protected function spaceFlightBookings()
    {
        return $this->hasMany(SpaceFlightBooking::class);
    }

    public function watermarkedImages()
    {
        return $this->hasMany(WatermarkedImage::class);
    }
}
