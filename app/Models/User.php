<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use function PHPUnit\Framework\isNull;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        "cart",
        'phoneNumber',
        'uid'
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
    ];

    /**
     * @param value
     *
     * @return string
     */
    public function setPasswordAttribute($value){
        $this->attributes["password"] = bcrypt($value);
    }

    /**
     * @param $phoneNumber
     *
     * @return true
     * @return false
     */
    public function isPhoneNumberTaken($phoneNumber){
        $result = User::where('phone_number', $phoneNumber)->first() ? true : false;
        return $result;
    }

    /**
     * @param $uid
     * @param $phoneNumber
     *
     * @return boolean
     */
    public function getUserByOtp($phoneNumber, $uid){
        return User::where('phone_number', $phoneNumber)
            ->where('uid', $uid)
            ->firstOrFail();
    }

}
