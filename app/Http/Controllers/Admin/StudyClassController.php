<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\StudyClass;

class StudyClassController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $classes = StudyClass::paginate(10);
        return view('admin.study_classes.lists', compact('classes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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

       $validData= $request->validate([
            'title'=>'required'
        ]);

        StudyClass::create($validData);
        $toastData = [
            'title' => 'إضافة دفعة',
            'msg' => "تم اضافة دفعة جديدة بنجاح",
            'status' => 'success'
        ];
        return back()->with(['toast' => $toastData]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(StudyClass $class)
    {
        //
        $class->delete();
        $toastData = [
            'title' => 'حذف دفعة',
            'msg' => "تم حذف الدفعة بنجاح",
            'status' => 'success'
        ];
        return back()->with(['toast' => $toastData]);
    }


    public function students(StudyClass $class){

        $enrollments = $class->enrollments()->paginate(10);
        return view('admin.study_classes.student', compact('enrollments'));
    }
}
