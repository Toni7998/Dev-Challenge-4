<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    // Campos que se pueden asignar masivamente
    protected $fillable = ['date', 'name', 'email', 'phone', 'course', 'hour'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reservation) {
            $reservation->uuid = (string) \Illuminate\Support\Str::uuid();
        });
    }

}
