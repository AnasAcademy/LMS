<?php

namespace App\Models;

use App\BundleStudent;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyClass extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function enrollments(){
        return User::whereHas(
            'bundleSales',
            function ($query) {
                $query->where("class_id", $this->id)->groupBy('buyer_id');
            }
        )->get();
    }
    // public function enrollments(){
    //     return $this->hasMany(BundleStudent::class, 'class_id')->groupBy('student_id');
    // }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'class_id');
    }

}
