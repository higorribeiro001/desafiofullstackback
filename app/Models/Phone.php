<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    use HasFactory;
    protected $fillable = ['num', 'user_id'];

    public function rules() {
        return [
            'num' => 'required|unique:phones,num,'.$this->id.'|min:9|max:19',
            'user_id' => 'required|exists:users,id',
        ];
    }

    public function user() {
        return $this->belongsTo('App\Models\User');
    }
}
