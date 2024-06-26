<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;

use App\Models\Service;
use App\Models\Bundle;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;

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
    public function store(Request $request, Service $service)
    {
        //
        $user = auth()->user();
        $content = $service->title;
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

        $category = Category::where('parent_id', '!=', null)->get();
        $bundles = Bundle::get();
        return view('web.default.panel.services.includes.bundleTransform', compact('category', 'bundles', 'service'));
    }
    function bundleTransform(Request $request, Service $service)
    {
        try {


            $to_bundle = Bundle::where('id', $request->to_bundle)->first();
            $validatedData = $request->validate([
                'from_bundle' => 'required|exists:bundles,id',
                'to_bundle' => 'required|exists:bundles,id',
                function ($attribute, $value, $fail) {
                    $student = auth()->user()->student;
                    // dd($student);
                    // if ($student && $student->bundles()->where('bundles.id', $value)->exists()) {
                        $fail('User has already applied for this bundle.');
                    // }
                },
                'category' => 'required|exists:categories,id',
                'certificate' => $to_bundle ? ($to_bundle->has_certificate ? 'required|boolean' : "") : '',
            ]);
        } catch (\Exception $e) {
            // return back()->withErrors($e->validator)->withInput();
            dd($e);
        }

        $from_bundle = Bundle::where('id', $request->from_bundle)->first();

        $content = " طلب تحويل من " . $from_bundle->title . " الي " . $to_bundle->title;
        if ($request->certificate) {
            $content .= " والرغبة في حجز الشهادة المهنيه الاحترافية ACP ";
        }


        $user = auth()->user();
        if ($service->price > 0) {
            Cookie::queue('service_content', json_encode($content));
            $order = $this->createOrder($service);
            return redirect('/payment/' . $order->id);

        } else {
            $service->users()->attach($user, ['content' => $content]);
            return redirect('/panel/services')->with("success", "تم ارسال الطلب بنجاح");
        }
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
