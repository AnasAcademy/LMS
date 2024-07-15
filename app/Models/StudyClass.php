<?php

namespace App\Models;

use App\BundleStudent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyClass extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function enrollments(){
        return $this->hasMany(BundleStudent::class, 'class_id')->groupBy('student_id');
    }
}
