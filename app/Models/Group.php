<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    public $timestamps = true;
    const UPDATED_AT = null;

    protected $guarded = ['id'];

    public function groupUsers()
    {
        return $this->hasMany('App\Models\GroupUser', 'group_id', 'id');
    }

    public function users()
    {
        return $this->hasMany('App\Models\GroupUser', 'id', 'group_id');
    }

    public function groupRegistrationPackage()
    {
        return $this->hasOne('App\Models\GroupRegistrationPackage', 'group_id', 'id');
    }
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function webinar()
    {
        return $this->belongsTo(Webinar::class);
    }
    public function bundle()
    {
        return $this->belongsTo(Bundle::class);
    }
    public function item()
    {
        return (!empty($this->bundle_id))? $this->belongsTo(Bundle::class, 'bundle_id') : $this->belongsTo(Webinar::class, 'webinar_id');
    }

}
