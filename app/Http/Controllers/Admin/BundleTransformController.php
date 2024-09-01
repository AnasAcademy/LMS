<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BundleTransform;
use Illuminate\Http\Request;

class BundleTransformController extends Controller
{
    //
    function index(){
        $transforms = BundleTransform::paginate(20);
        return view("admin.bundle_transform.index", compact('transforms'));
    }
}
