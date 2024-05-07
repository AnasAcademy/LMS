<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $services = Service::paginate(10);

        return view('admin.services.index', compact('services'));


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.services.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $authUser = Auth::user();
       $request->validate([
        'title' => 'required|string|min:3',
        'description' => 'nullable|string|min:10',
        'price' => 'required|regex:/^\d{1,3}(\.\d{1,6})?$/',
        'apply_link' => 'required|url',
        'review_link' => 'required|url',
        'status' => ['required', Rule::in(['pending', 'active', 'inactive'])],

       ]);

       $request['created_by'] =$authUser->id;
       Service::create($request->all());
       return back()->with('success', 'تم إنشاء الخدمة بنجاح');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service)
    {
        //
        return view('admin.services.show',  compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     *

     * @return \Illuminate\Http\Response
     */
    public function edit(Service $service)
    {
        //
        return view('admin.services.create', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $service)
    {
        //
        $authUser = Auth::user();
        $request->validate([
            'title' => 'required|string|min:3',
            'description' => 'nullable|string|min:10',
            'price' => 'required|regex:/^\d{1,3}(\.\d{1,6})?$/',
            'apply_link' => 'required|url',
            'review_link' => 'required|url',
            'status' => ['required', Rule::in(['pending', 'active', 'inactive'])],

           ]);

           $request['created_by'] =$authUser->id;

           $service->update($request->all());
        return back()->with('success', 'تم تعديل الخدمة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service)
    {
        //
        $service->delete();
        return redirect('admin/services')->with('success', 'تم حذف الخدمة بنجاح');
    }
}
