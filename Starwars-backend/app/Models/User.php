<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Starship;
use App\Notifications\CustomResetPassword;  // para 
use Laravel\Sanctum\HasApiTokens;  // Añadido lo de SANCTUM 
use Illuminate\Contracts\Auth\CanResetPassword;  // para el RESET password

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;  // Añadido lo de SANCTUM ---- HasApiTokens

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'admin',
    ];


    public function starships(): HasMany
    {
        return $this->hasMany(Starship::class);
    }



    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }




    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
