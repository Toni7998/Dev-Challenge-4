<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    // Campos que se pueden asignar masivamente
    protected $fillable = ['date', 'name', 'email', 'phone', 'course', 'hour'];
}
