<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\Bundle;

class Student extends Model

{
    protected $table = "students";
    protected $guarded = [];
    public function registeredUser()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    // student Requirments reltion
    public function bundles()
    {
        return $this->belongsToMany(Bundle::class);
    }

}
