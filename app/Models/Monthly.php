<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monthly extends Model
{
    use HasFactory;

    public $guarded = [];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
