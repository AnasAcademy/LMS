<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Group;
use App\Models\Role;
use Illuminate\Http\Request;

use App\Models\StudyClass;
use App\StudentRequirement;
use App\User;

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
        $pageTitle = 'الدفعات الدراسية';
        return view('admin.study_classes.lists', compact('classes', 'pageTitle'));
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
    public function update(Request $request, StudyClass $class)
    {
        //

        $validData = $request->validate([
            'title' => 'required'
        ]);

        $class->update($validData);
        $toastData = [
            'title' => 'تعديل دفعة',
            'msg' => "تم تعديل بيانات الدفعة بنجاح",
            'status' => 'success'
        ];
        return back()->with(['toast' => $toastData]);
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


    public function students(StudyClass $class, Request $request){

        $pageTitle = trans('public.students');
        $query = User::whereHas(
            'bundleSales',
            function ($query) use ($class) {
                $query->where("class_id", $class->id)->groupBy('buyer_id');
            }
        );

         $enrollments = (new UserController())->filters($query, $request)->paginate(10);
        return view('admin.study_classes.student', compact('enrollments', "class", 'pageTitle'));
    }

    public function Users(Request $request, StudyClass $class, $is_export_excel = false)
    {
        $this->authorize('admin_users_list');

        // $usersQuery = User::whereHas('student.bundleStudent', function ($query) use ($class) {
        //     $query->where('class_id', $class->id);
        // });
        $query = User::whereHas('student')->whereHas('purchasedFormBundleUnique', function ($query) use ($class) {
            $query->where('class_id', $class->id);
        });
        $totalStudents = deepClone($query)->count();
        $inactiveStudents = deepClone($query)->where('status', 'inactive')
        ->count();
        $banStudents = deepClone($query)->where('ban', true)
            ->whereNotNull('ban_end_at')
            ->where('ban_end_at', '>', time())
            ->count();

        $totalOrganizationsStudents = User::where('role_name', Role::$user)
            ->whereNotNull('organ_id')
            ->count();
        $userGroups = Group::where('status', 'active')
        ->orderBy('created_at', 'desc')
        ->get();

        $organizations = User::select('id', 'full_name', 'created_at')
        ->where('role_name', Role::$organization)
            ->orderBy('created_at', 'desc')
            ->get();

        $query = (new UserController())->filters($query, $request);

        if ($is_export_excel) {
            $users = $query->orderBy('created_at', 'desc')->get();
        } else {
            $users = $query->orderBy('created_at', 'desc')
            ->paginate(20);
        }

        $users = (new UserController())->addUsersExtraInfo($users);

        if ($is_export_excel) {
            return $users;
        }


        $category = Category::where('parent_id', '!=', null)->get();

        $data = [
            'pageTitle' => trans('public.students'),
            'users' => $users,
            'category' => $category,
            'totalStudents' => $totalStudents,
            'inactiveStudents' => $inactiveStudents,
            'banStudents' => $banStudents,
            'totalOrganizationsStudents' => $totalOrganizationsStudents,
            'userGroups' => $userGroups,
            'organizations' => $organizations,
            'class' => $class
        ];

        return view('admin.students.index', $data);
    }
    public function Enrollers(Request $request, StudyClass $class, $is_export_excel = false)
    {
        $this->authorize('admin_users_list');


        // $usersQuery = User::whereHas('student.bundleStudent', function ($query) use ($class) {
        //         $query->where('class_id', $class->id);
        //     });

        $query = User::where(['role_name' => Role::$user])->whereHas('purchasedBundles', function ($query) use ($class) {
            $query->where('class_id', $class->id)->where("payment_method","!=", 'scholarship');
        });
        $totalStudents = deepClone($query)->count();
        $inactiveStudents = deepClone($query)->where('status', 'inactive')
        ->count();
        $banStudents = deepClone($query)->where('ban', true)
            ->whereNotNull('ban_end_at')
            ->where('ban_end_at', '>', time())
            ->count();

        $totalOrganizationsStudents = User::where('role_name', Role::$user)
            ->whereNotNull('organ_id')
            ->count();
        $userGroups = Group::where('status', 'active')
        ->orderBy('created_at', 'desc')
        ->get();

        $organizations = User::select('id', 'full_name', 'created_at')
        ->where('role_name', Role::$organization)
            ->orderBy('created_at', 'desc')
            ->get();

        $query =(new UserController())->filters($query, $request);

        if ($is_export_excel) {
            $users = $query->orderBy('created_at', 'desc')->get();
        } else {
            $users = $query->orderBy('created_at', 'desc')
            ->paginate(20);
        }

        $users =(new UserController())->addUsersExtraInfo($users);

        if ($is_export_excel) {
            return $users;
        }

        $category = Category::where('parent_id', '!=', null)->get();

        $data = [
            'pageTitle' => trans('public.students'),
            'users' => $users,
            'category' => $category,
            'totalStudents' => $totalStudents,
            'inactiveStudents' => $inactiveStudents,
            'banStudents' => $banStudents,
            'totalOrganizationsStudents' => $totalOrganizationsStudents,
            'userGroups' => $userGroups,
            'organizations' => $organizations,
            'class' => $class
        ];

        return view('admin.students.enrollers', $data);
    }

    public function ScholarshipStudent(Request $request, StudyClass $class, $is_export_excel = false)
    {
        $this->authorize('admin_users_list');
        $query = User::where(['role_name' => Role::$user])->whereHas('purchasedBundles', function ($query) use ($class) {
            $query->where("payment_method", 'scholarship')->where('class_id', $class->id);
        });
        $totalStudents = deepClone($query)->count();
        $inactiveStudents = deepClone($query)->where('status', 'inactive')
        ->count();
        $banStudents = deepClone($query)->where('ban', true)
            ->whereNotNull('ban_end_at')
            ->where('ban_end_at', '>', time())
            ->count();

        $totalOrganizationsStudents = User::where('role_name', Role::$user)
            ->whereNotNull('organ_id')
            ->count();
        $userGroups = Group::where('status', 'active')
        ->orderBy('created_at', 'desc')
        ->get();

        $organizations = User::select('id', 'full_name', 'created_at')
        ->where('role_name', Role::$organization)
            ->orderBy('created_at', 'desc')
            ->get();

        $query = (new UserController())->filters($query, $request);

        if ($is_export_excel) {
            $users = $query->orderBy('created_at', 'desc')->get();
        } else {
            $users = $query->orderBy('created_at', 'desc')
            ->paginate(20);
        }

        $users = (new UserController())->addUsersExtraInfo($users);

        if ($is_export_excel) {
            return $users;
        }

        $category = Category::where('parent_id', '!=', null)->get();

        $data = [
            'pageTitle' => trans('public.students'),
            'users' => $users,
            'category' => $category,
            'totalStudents' => $totalStudents,
            'inactiveStudents' => $inactiveStudents,
            'banStudents' => $banStudents,
            'totalOrganizationsStudents' => $totalOrganizationsStudents,
            'userGroups' => $userGroups,
            'organizations' => $organizations,
        ];

        return view('admin.students.enrollers', $data);
    }


    public function directRegister(Request $request, StudyClass $class, $is_export_excel = false)
    {
        $this->authorize('admin_users_list');
        $query = User::whereHas('student.bundleStudent', function ($query) use ($class){
            $query->whereNull('class_id')->whereHas('bundle', function ($query)  use ($class){
                $query->where('batch_id', $class->id);
            });
        });

        $totalStudents = deepClone($query)->count();


        $query = (new UserController())->filters($query, $request);

        if ($is_export_excel) {
            $users = $query->orderBy('created_at', 'desc')->get();
        } else {
            $users = $query->orderBy('created_at', 'desc')
            ->paginate(20);
        }

        $users = (new UserController())->addUsersExtraInfo($users);

        if ($is_export_excel) {
            return $users;
        }

        $category = Category::where('parent_id', '!=', null)->get();

        $data = [
            'pageTitle' => trans('public.students'),
            'users' => $users,
            'category' => $category,
            'totalStudents' => $totalStudents,
            'class' => $class,

        ];

        return view('admin.students.direct_register', $data);
    }    public function requirements(Request $request, StudyClass $class)
    {
        $query = StudentRequirement::whereHas('bundleStudent', function ($query) use ($class) {
            $query->where('class_id', $class->id); // Filter by class_id
        })->orderByDesc('created_at');
        $query =(new RequirementController())->filters($query, $request);
        $requirements = $query->paginate(20);

        return view('admin.requirements.index', ['requirements' => $requirements]);
    }
}
