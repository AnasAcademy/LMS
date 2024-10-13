<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BundleTransform;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendNotifications;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Sale;
use App\User;
use Illuminate\Http\Request;

class BundleTransformController extends Controller
{
    //
    function index(){
        $transforms = BundleTransform::whereHas('serviceRequest', function($query){
            $query->where('status',  'approved');
        })->orderByDesc('created_at')->paginate(20);
        return view("admin.bundle_transform.index", compact('transforms'));
    }
    function approve(Request $request, BundleTransform $transform){
        $transform->status = 'approved';
        $transform->save();
        if($transform->type == 'refund'){
            return $this->refund($request, $transform);
        }

        $data['user_id'] = $transform->user_id;
        $data['name'] = $transform->user->full_name;
        $data['receiver'] = $transform->user->email;
        $data['fromEmail'] = env('MAIL_FROM_ADDRESS');
        $data['fromName'] = env('MAIL_FROM_NAME');
        $data['subject'] = 'الرد علي طلب خدمة ' . $transform->serviceRequest->title;
        $data['body'] = 'نود اعلامك علي انه تم الموافقة علي طلبك للتحويل من برنامج  ' . $transform->fromBundle->title .  ' إلي برنامج ' . $transform->toBundle->title . 'متبقي فقط دفع فرق السعر لإتمام التحويل' ;

        $this->sendNotification($data);
        $toastData = [
            'title' => " طلب تحويل",
            'msg' => "تم الموافقة علي طلب التحويل بنجاح",
            'status' => 'success'
        ];


        return back()->with(['toast' => $toastData]);

    }

    function refund(Request $request, bundleTransform $bundleTransform)
    {

        $user = auth()->user();
        // $order = $this->createOrder($bundleTransform);
        $price = ($bundleTransform->amount);

        $order = Order::create([
            'user_id' => $bundleTransform->user_id,
            'status' => Order::$pending,
            'amount' => $price,
            'tax' => 0,
            'total_discount' => 0,
            'total_amount' =>  $price,
            'product_delivery_fee' => null,
            'created_at' => time(),
        ]);

        $orderItem = OrderItem::create([
            'user_id' => $bundleTransform->user_id,
            'order_id' => $order->id,
            'bundle_id' => $bundleTransform->to_bundle_id,
            'transform_bundle_id' => $bundleTransform->from_bundle_id,
            'amount' => $price,
            'total_amount' => $price,
            'tax_price' => 0,
            'commission' => 0,
            'commission_price' => 0,
            'product_delivery_fee' => 0,
            'discount' => 0,
            'created_at' => time(),
        ]);

        Sale::createSales($orderItem, $order->payment_method, false, true);
        $bundleTransform->status = 'refunded';
        $bundleTransform->save();

        $data['user_id'] = $bundleTransform->user_id;
        $data['name'] = $bundleTransform->user->full_name;
        $data['receiver'] = $bundleTransform->user->email;
        $data['fromEmail'] = env('MAIL_FROM_ADDRESS');
        $data['fromName'] = env('MAIL_FROM_NAME');
        $data['subject'] = 'الرد علي طلب خدمة ' . $bundleTransform->serviceRequest->title;
        $data['body'] = 'نود اعلامك علي انه تم الموافقة علي طلبك للتحويل من برنامج  ' . $bundleTransform->fromBundle->title .  ' إلي برنامج ' . $bundleTransform->toBundle->title . " واستيرداد مبلغ قدره $bundleTransform->amount ر.س وتم التحويل بنجاح";

        $this->sendNotification($data);

        $toastData = [
            'title' => "اتمام التحويل",
            'msg' => "تم اتمام التحويل واستيرداد المبلغ بنجاح",
            'status' => 'success'
        ];


        return back()->with(['toast' => $toastData]);
    }


    function changeAmount(Request $request, BundleTransform $transform){
        try{


        $request->validate([
            'amount' => 'required|numeric|gte:0|not_in:1,2,3'
        ]);
        $transform->amount = $request->amount;
        $transform->save();
        $toastData = [
            'title' => "تغيير المبلغ",
            'msg' => "تم تغيير المبلغ بنجاح",
           'status' =>'success'
        ];
        return back()->with(['toast' => $toastData]);

    }catch(\Exception $e){
            $toastData = [
                'title' => "تغيير المبلغ",
                'msg' => $e->getMessage(),
                'status' => 'error'
            ];
            return back()->with(['toast' => $toastData]);
    }
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
