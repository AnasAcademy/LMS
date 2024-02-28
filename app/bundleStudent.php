<?php

namespace App;

use App\StudentRequirement;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BundleStudent extends Pivot
{
    public function studentRequirement()
    {
        return $this->hasOne(StudentRequirement::class, "bundle_student_id");
    }
}
