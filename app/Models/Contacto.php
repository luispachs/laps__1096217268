<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    use HasFactory;
    protected $table ="contacts";
    public $timestamps =false;
    protected $fillable =['name','email','phone','entity_id'];
}
