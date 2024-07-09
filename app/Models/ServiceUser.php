<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

use App\User;

class ServiceUser extends Pivot{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'service_user';
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

}
