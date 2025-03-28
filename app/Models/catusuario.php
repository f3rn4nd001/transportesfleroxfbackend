<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class catusuario extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'catusuarios';

}
