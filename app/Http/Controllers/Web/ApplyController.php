<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentChannel;
use App\Models\Product;
use App\Models\Sale;
use App\User;
use App\Student;
use Illuminate\Support\Facades\Cookie;

class ApplyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        return view(getTemplate() . '.pages.application_form',compact('user'));
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
    public function checkout(Request $request, $carts = null)
    {
        $validatedData = $request->validate([
            'user_id' => 'required',
            'program' => 'required',
            'specialization' => 'required',
            'ar_name' => 'required',
            'en_name' => 'required',
            'country' => 'required',
            'area' => 'nullable',
            'city' => 'nullable',
            'email' => 'required|email',
            'birthday' => 'required|date',
            'phone' => 'required',
            'deaf' => 'required',
            'gender' => 'required',
            'healthy' => 'required',
            'nationality' => 'required',
            'job' => 'nullable',
            'job_type' => 'nullable',
            'referral_person' => 'required',
            'relation' => 'required',
            'referral_email' => 'required|email',
            'referral_phone' => 'required',
            'about_us' => 'nullable',
        ]);
        Cookie::queue('user_data', json_encode($validatedData));

        $user = auth()->user();
        $student=Student::where('user_id',$user->id)->first();

        if(empty($student)){
            $paymentChannels = PaymentChannel::where('status', 'active')->get();

            $order = Order::create([
            'user_id' => $user->id,
            'status' => Order::$pending,
            'amount' => 230,
            'tax' => 0,
            'total_discount' => 0,
            'total_amount' => 230,
            'product_delivery_fee' => null,
            'created_at' => time(),
            ]);
             OrderItem::create([
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'webinar_id' => null,
                    'bundle_id' => null,
                    'certificate_template_id' =>  null,
                    'certificate_bundle_id' => null,
                    'form_fee' => 1,
                    'product_id' =>  null,
                    'product_order_id' => null,
                    'reserve_meeting_id' => null,
                    'subscribe_id' => null,
                    'promotion_id' => null,
                    'gift_id' => null,
                    'installment_payment_id' => null,
                    'ticket_id' => null,
                    'discount_id' => null,
                    'amount' => 230,
                    'total_amount' => 230,
                    'tax' => null,
                    'tax_price' => 0,
                    'commission' => 0,
                    'commission_price' => 0,
                    'product_delivery_fee' => 0,
                    'discount' => 0,
                    'created_at' => time(),
                ]);


            if (!empty($order) and $order->total_amount > 0) {
                $razorpay = false;
                $isMultiCurrency = !empty(getFinancialCurrencySettings('multi_currency'));

                foreach ($paymentChannels as $paymentChannel) {
                    if ($paymentChannel->class_name == 'Razorpay' and (!$isMultiCurrency or in_array(currency(), $paymentChannel->currencies))) {
                        $razorpay = true;
                    }
                }


                $data = [
                    'pageTitle' => trans('public.checkout_page_title'),
                    'paymentChannels' => $paymentChannels,
                    'carts' => $carts,
                    'subTotal' => null,
                    'totalDiscount' => null,
                    'tax' => null,
                    'taxPrice' => null,
                    'total' => 230,
                    'userGroup' => $user->userGroup ? $user->userGroup->group : null,
                    'order' => $order,
                    'count' => 0,
                    'userCharge' => $user->getAccountingCharge(),
                    'razorpay' => $razorpay,
                    'totalCashbackAmount' => null,
                    'previousUrl' => url()->previous(),
                ];

                return view(getTemplate() . '.cart.payment', $data);
            } else {

                return $this->handlePaymentOrderWithZeroTotalAmount($order);
            }
            //checking form fee for user and adding him as student

}
        return redirect('/panel');
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
}
