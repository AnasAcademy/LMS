<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\User;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description','price', 'apply_link', 'review_link', 'details', 'created_by', 'status'];


    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    public function users(){
        return $this->belongsToMany(User::class);
    }
}
