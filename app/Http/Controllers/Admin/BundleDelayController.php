<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BundleDelay;
use Illuminate\Http\Request;

class BundleDelayController extends Controller
{
    //
    function index(Request $request)
    {
        $query = BundleDelay::whereHas('serviceRequest', function ($query) {
            $query->where('status',  'approved');
        })->orderByDesc('created_at');

        $bundleDelays = $this->filter($request, $query)->paginate(20);
        return view("admin.bundle_delay.index", compact('bundleDelays'));
    }

    function filter(Request $request, $query)
    {
        $userName = $request->get('user_name');

        $email = $request->get('email');
        $user_code = $request->get('user_code');

        if (!empty($userName)) {
            $query->when($userName, function ($query) use ($userName) {
                $query->whereHas('user', function ($q) use ($userName) {
                    $q->where('full_name', 'like', "%$userName%");
                });
            });
        }

        if (!empty($email)) {
            $query->when($email, function ($query) use ($email) {
                $query->whereHas('user', function ($q) use ($email) {
                    $q->where('email', 'like', "%$email%");
                });
            });
        }
        if (!empty($user_code)) {
            $query->when($user_code, function ($query) use ($user_code) {
                $query->whereHas('user', function ($q) use ($user_code) {
                    $q->where('user_code', 'like', "%$user_code%");
                });
            });
        }
        

        return $query;
    }
}
