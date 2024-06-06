<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    public $guarded = [];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
