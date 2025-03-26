<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    protected $fillable = ['name', 'address'];

    public function users()
    {
        return $this->hasMany(User::class, 'merchant_id');
    }
}
