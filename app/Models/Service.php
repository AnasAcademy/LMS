<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\User;

class Service extends Model
{
    use HasFactory;

    protected $gaurded = ['id'];


    public function createdBy()
    {
        return $this->belongsTo(User::class);
    }


    public function users(){
        return $this->belongsToMany(User::class);
    }
}
