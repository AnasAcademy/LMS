<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;

use App\Models\Service;
use App\Models\Bundle;
use App\Models\BundleTransform;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ServiceUser;

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
        $services = Service::where('status', 'active')->get();
        return view(getTemplate() . '.panel.services.index', compact('services'));
    }

    public function requests()
    {
        //
        $services = auth()->user()->services()->paginate(10);

        // dd($services);
        return view(getTemplate() . '.panel.services.requests', compact('services'));
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
    public function store(Request $request, Service $service, $content = null)
    {
        //
        $user = auth()->user();
        if (empty($content)) {
            $content = $service->title;
        }

        if ($service->price > 0) {

            Cookie::queue('service_content', json_encode($content));
            $order = $this->createOrder($service);
            return redirect('/payment/' . $order->id);
        } else {
            $service->users()->attach($user, ['content' => $content]);
            return redirect('/panel/services')->with("success", "تم ارسال الطلب بنجاح");
        }
    }


    function bundleTransformRequest(Service $service)
    {
        $categories = Category::whereNull('parent_id')->where('status', 'active')
            ->where(function ($query) {
                $query->whereHas('activeBundles')
                    ->orWhereHas('activeSubCategories', function ($query) {
                        $query->whereHas('activeBundles');
                    });
            })->get();

        $bundles = Bundle::get();
        return view('web.default.panel.services.includes.bundleTransform', compact('categories', 'bundles', 'service'));
    }

    function bundleTransform(Request $request, Service $service)
    {

        $user = auth()->user();

        $to_bundle = Bundle::where('id', $request->to_bundle_id)->first();
        $validatedData = $request->validate([
            'from_bundle_id' => 'required|exists:bundles,id',
            'to_bundle_id' => [
                'required', 'exists:bundles,id',
                function ($attribute, $value, $fail) {
                    $student = auth()->user()->student;
                    if ($student && $student->bundles()->where('bundles.id', $value)->exists()) {
                        $fail('انك مسجل بالفعل في هذا البرنامج');
                    }
                }
            ],
            'certificate' => $to_bundle ? ($to_bundle->has_certificate ? 'required|boolean' : "") : '',
        ]);




        $from_bundle = Bundle::where('id', $request->from_bundle_id)->first();

        $content = " طلب تحويل من " . $from_bundle->title . " الي " . $to_bundle->title;
        if ($request->certificate) {
            $content .= " والرغبة في حجز الشهادة المهنيه الاحترافية ACP ";
        }

        if ($service->price > 0) {

            Cookie::queue('service_content', json_encode($content));
            $order = $this->createOrder($service);
            return redirect('/payment/' . $order->id);
        } else {
            if ($to_bundle->price == $from_bundle->price) {
                $type = null;
            } else if ($to_bundle->price > $from_bundle->price) {
                $type = "pay";
            } else {
                $type = "refund";
            }

            $serviceRequest = ServiceUser::create(['service_id' => $service->id, 'user_id' => $user->id, 'content' => $content]);
            BundleTransform::create([...$validatedData, 'student_id' => $user->student->id, 'service_request_id' => $serviceRequest->id, 'type' => $type]);
            return redirect('/panel/services')->with("success", "تم ارسال الطلب بنجاح");
        }
    }

    function bundleTransformPay(Request $request, bundleTransform $bundleTransform)
    {

        $user = auth()->user();
        Cookie::queue('bundleTransformId', json_encode($bundleTransform->id));
        // $order = $this->createOrder($bundleTransform);
        $price = $bundleTransform->toBundle->price - $bundleTransform->fromBundle->price;

        $order = Order::create([
            'user_id' => $user->id,
            'status' => Order::$pending,
            'amount' => $price,
            'tax' => 0,
            'total_discount' => 0,
            'total_amount' =>  $price,
            'product_delivery_fee' => null,
            'created_at' => time(),
        ]);

        OrderItem::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'bundle_id' => $bundleTransform->to_bundle_id,
            'amount' => $price,
            'total_amount' => $price,
            'tax_price' => 0,
            'commission' => 0,
            'commission_price' => 0,
            'product_delivery_fee' => 0,
            'discount' => 0,
            'created_at' => time(),
        ]);
        return redirect('/payment/' . $order->id);
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
    public function destroy($id)
    {
        //
    }
    public function createOrder(Service $service)
    {
        $user = auth()->user();
        $order = Order::create([
            'user_id' => $user->id,
            'status' => Order::$pending,
            'amount' => $service->price,
            'tax' => 0,
            'total_discount' => 0,
            'total_amount' =>  $service->price,
            'product_delivery_fee' => null,
            'created_at' => time(),
        ]);

        OrderItem::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'service_id' => $service->id,
            'amount' => $service->price,
            'total_amount' => $service->price,
            'tax_price' => 0,
            'commission' => 0,
            'commission_price' => 0,
            'product_delivery_fee' => 0,
            'discount' => 0,
            'created_at' => time(),
        ]);

        return $order;
    }
}
