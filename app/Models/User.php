<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'image',
        'email',
        'company',
        'password',
    ];

    public function rules() {
        return [
            'name' => 'required|min:5|max:120',
            'image' => 'required|file|mimes:png,jpeg,jpg',
            'email' => 'required|unique:users,email,'.$this->id.'|email|min:5',
            'company' => 'required|min:5|max:120',
            'password' => 'required|min:8|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*?&]/'
        ];
    }

    public function phones() {
        return $this->hasMany('App\Models\Phone');
    }
}
