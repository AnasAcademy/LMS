<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Service;
use App\Models\ServiceUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendNotifications;
use App\Models\Notification;
use Illuminate\Support\Facades\Validator;

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

    public function requests(Service $service)
    {
        //

        // $services = Service::paginate(1);

        return view('admin.services.requests', compact('service'));
    }
    public function approveRequest(Request $request, ServiceUser $serviceUser)
    {
        try {
            $admin = auth()->user();

            $serviceUser->status = 'approved';
            $serviceUser->approved_by = $admin->id;

            $data['user_id'] = $serviceUser->user_id;
            $data['name'] = $serviceUser->user->full_name;
            $data['receiver'] = $serviceUser->user->email;
            $data['fromEmail'] = env('MAIL_FROM_ADDRESS');
            $data['fromName'] = env('MAIL_FROM_NAME');
            $data['subject'] = 'الرد علي طلب خدمة ' . $serviceUser->service->title;
            $data['body'] = 'نود اعلامك علي انه تم الموافقة علي طلبك لخدمة ' . $serviceUser->service->title . ' التي قمت بارساله ';

            $this->sendNotification($data);
            $serviceUser->save();
            return back()->with('success', 'تم الموافقة علي طلب الخدمة وارسال ايميل للطالب بهذا');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ ما يرجي المحاولة مرة أخري');
        }
    }
    public function rejectRequest(Request $request, ServiceUser $serviceUser)
    {
        try {
            $validator = Validator::make($request->all(), [
                // 'reason' => 'required',
                'message' => 'required|string|min:2'
            ]);

            if ($validator->fails()) {
               return back()->with('error', implode(', ', $validator->errors()->all()));

            }
            $admin = auth()->user();

            $serviceUser->status = 'rejected';
            $serviceUser->approved_by = $admin->id;

            $data['user_id'] = $serviceUser->user_id;
            $data['name'] = $serviceUser->user->full_name;
            $data['receiver'] = $serviceUser->user->email;
            $data['fromEmail'] = env('MAIL_FROM_ADDRESS');
            $data['fromName'] = env('MAIL_FROM_NAME');
            $data['subject'] = 'الرد علي طلب خدمة ' . $serviceUser->service->title;

            $data['body'] = "لقد تم رفض طلبك لخدمة ". $serviceUser->service->title ." بسبب " . $request['reason'];
            $serviceUser->message =  $request['reason'] . "<br>";
            if (isset($request['message'])) {
                $data['body'] =  $data['body'] . "\n" . $request['message'];
                $serviceUser->message .= $request['message'];
            }

            $this->sendNotification($data);

            $serviceUser->save();
            return back()->with('success', 'تم رفض طلب الخدمة وارسال ايميل للطالب بهذا');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->with('error', 'حدث خطأ ما يرجي المحاولة مرة أخري');
        }
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
            // 'apply_link' => 'required|url',
            // 'review_link' => 'required|url',
            'status' => ['required', Rule::in(['pending', 'active', 'inactive'])],

        ]);

        $request['created_by'] = $authUser->id;
        $data = $request->all();
        $lastService = (Service::get()->last()->id)+1;
        $data['apply_link']= env('APP_URL').'panel/services/'.$lastService.'/apply';
        $data['review_link']= env('APP_URL').'panel/services/'.$lastService.'/review';
        Service::create($data);
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

        $request['created_by'] = $authUser->id;

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


    protected function sendNotification($data)
    {
        $this->authorize('admin_notifications_send');

        Notification::create([
            'user_id' => !empty($data['user_id']) ? $data['user_id'] : null,
            'sender_id' => auth()->id(),
            'title' => "طلب خدمة",
            'message' => $data['body'],
            'sender' => Notification::$AdminSender,
            'type' => "single",
            'created_at' => time()
        ]);

        if (!empty($data['user_id']) and env('APP_ENV') == 'production') {
            $user = User::where('id', $data['user_id'])->first();
            if (!empty($user) and !empty($user->email)) {
                Mail::to($user->email)->send(new SendNotifications(['title' => $data['subject'], 'message' => $data['body'], 'name' => $data['name']]));
            }
        }

        return true;
    }
}
