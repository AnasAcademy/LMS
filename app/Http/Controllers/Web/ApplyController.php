<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentChannel;
use App\Models\Product;
use App\Models\Sale;
use App\User;
use App\Student;
use App\Models\Category;
use Illuminate\Support\Facades\Cookie;
use App\Http\Controllers\Web\PaymentController;
use App\Models\Accounting;
use App\Models\OfflineBank;
use App\Models\OfflinePayment;


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
        $student = Student::where('user_id', $user->id)->first();
        $category = Category::where('parent_id', '!=', null)->get();
        return view(getTemplate() . '.pages.application_form', compact('user', 'category', 'student'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function newEnrollment()
    {
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->first();
        $category = Category::where('parent_id', '!=', null)->get();
        return view(getTemplate() . '.panel.newEnrollment.index', compact('user', 'category', 'student'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkout(Request $request, $carts = null)
    {
        // dd($request->all());
        app()->setLocale('ar');

        $category = Category::where('id', $request->category_id)->first();
        $bundle = Bundle::where('id', $request->bundle_id)->first();
        $categoryTitle = $category->title;
        $student = Student::where('user_id', auth()->user()->id)->first();


        if ($student) {
            $validatedData = $request->validate([
                'user_id' => 'required',
                'category_id' => 'required',
                'bundle_id' =>  [
                    'required',
                    function ($attribute, $value, $fail) {
                        $user = auth()->user();
                        $student = Student::where('user_id', $user->id)->first();

                        if ($student && $student->bundles()->where('bundles.id', $value)->exists()) {
                            $fail('User has already applied for this bundle.');
                        }
                    },
                ],
                'terms' => 'accepted',
                'certificate' => $bundle->has_certificate ? 'required|boolean' : "",
            ]);
        } else {
            $validatedData = $request->validate([
                'user_id' => 'required',
                'category_id' => 'required',
                'bundle_id' =>  [
                    'required',
                    function ($attribute, $value, $fail) {
                        $user = auth()->user();
                        $student = Student::where('user_id', $user->id)->first();

                        if ($student && $student->bundles()->where('bundles.id', $value)->exists()) {
                            $fail('User has already applied for this bundle.');
                        }
                    },
                ],
                'ar_name' => 'required|string|regex:/^[\p{Arabic} ]+$/u|max:255|min:5',
                'en_name' => 'required|string|regex:/^[a-zA-Z\s]+$/|max:255|min:5',
                'identifier_num' => 'required|numeric|regex:/^\d{6,10}$/',
                'country' => 'required|string|max:255|min:3|regex:/^(?=.*[\p{Arabic}\p{L}])[0-9\p{Arabic}\p{L}\s]+$/u',
                'area' => 'nullable|string|max:255|min:3|regex:/^(?=.*[\p{Arabic}\p{L}])[0-9\p{Arabic}\p{L}\s]+$/u',
                'city' => 'nullable|string|max:255|min:3|regex:/^(?=.*[\p{Arabic}\p{L}])[0-9\p{Arabic}\p{L}\s]+$/u',
                'town' => 'required|string|max:255|min:3|regex:/^(?=.*[\p{Arabic}\p{L}])[0-9\p{Arabic}\p{L}\s]+$/u',
                'email' => 'required|email|max:255|regex:/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/',
                'birthdate' => 'required|date',
                'phone' => 'required|min:5|max:20',
                'mobile' => 'required|min:5|max:20',
                'educational_qualification_country' => $category->education ? 'required|string|max:255|min:3|regex:/^(?=.*[\p{Arabic}\p{L}])[0-9\p{Arabic}\p{L}\s]+$/u' : '',
                'secondary_school_gpa' => !$category->education ? 'required|string|max:255|min:1' : '',
                'educational_area' => 'required|string|max:255|min:3|regex:/^(?=.*[\p{Arabic}\p{L}])[0-9\p{Arabic}\p{L}\s]+$/u',
                'secondary_graduation_year' => !$category->education  ? 'required|numeric|regex:/^\d{3,10}$/' : '',
                'school' => !$category->education  ? 'required|string|max:255|min:3|regex:/^(?=.*[\p{Arabic}\p{L}])[0-9\p{Arabic}\p{L}\s]+$/u' : '',
                'university' => $category->education ? 'required|string|max:255|min:3|regex:/^(?=.*[\p{Arabic}\p{L}])[0-9\p{Arabic}\p{L}\s]+$/u' : '',
                'faculty' => $category->education ? 'required|string|max:255|min:3|regex:/^(?=.*[\p{Arabic}\p{L}])[0-9\p{Arabic}\p{L}\s]+$/u' : '',
                'education_specialization' => $category->education  ? 'required|string|max:255|min:3|regex:/^(?=.*[\p{Arabic}\p{L}])[0-9\p{Arabic}\p{L}\s]+$/u' : '',
                'graduation_year' => $category->education ? 'required|numeric|regex:/^\d{3,10}$/' : '',
                'gpa' => $category->education ? 'required|string|max:255|min:1' : '',
                'deaf' => 'required|in:0,1',
                'disabled_type' => $request->disabled == 1 ? 'required|string|max:255|min:3' : 'nullable',
                'gender' => 'required|in:male,female',
                'healthy_problem' => $request->healthy == 1 ? 'required|string|max:255|min:3' : 'nullable',
                'nationality' => 'required|string|min:3|max:25',
                'job' => $request->workStatus == 1 ? 'required' : 'nullable',
                'job_type' => $request->workStatus == 1 ? 'required' : 'nullable',
                'referral_person' => 'required|string|min:3|max:255',
                'relation' => 'required|string|min:3|max:255',
                'referral_email' => 'required|email|max:255|regex:/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/',
                'referral_phone' => 'required|min:3|max:20',
                'about_us' => 'required|string|min:3|max:255',
                'terms' => 'accepted',
                'certificate' => $bundle->has_certificate ? 'required|boolean' : "",
            ]);
        }


        Cookie::queue('user_data', json_encode($validatedData));
        $user = auth()->user();

        // $paymentChannels = PaymentChannel::where('status', 'active')->get();
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
            'bundle_id' => $request->bundle_id ?? null,
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
        //     $razorpay = false;
        //     $isMultiCurrency = !empty(getFinancialCurrencySettings('multi_currency'));

        //     foreach ($paymentChannels as $paymentChannel) {
        //         if ($paymentChannel->class_name == 'Razorpay' and (!$isMultiCurrency or in_array(currency(), $paymentChannel->currencies))) {
        //             $razorpay = true;
        //         }
        //     }

        //     $userAuth = auth()->user();

        //     $editOfflinePayment = null;
        //     if (!empty($id)) {
        //         $editOfflinePayment = OfflinePayment::where('id', $id)
        //             ->where('user_id', $userAuth->id)
        //             ->first();
        //     }



        //     $offlinePayments = OfflinePayment::where('user_id', $userAuth->id)->orderBy('created_at', 'desc')->get();

        //     $offlineBanks = OfflineBank::query()
        //     ->orderBy('created_at', 'desc')
        //     ->with([
        //         'specifications'
        //     ])
        //     ->get();




            // $registrationBonusAmount = null;

            // if ($userAuth->enable_registration_bonus) {
            //     $registrationBonusSettings = getRegistrationBonusSettings();

            //     $registrationBonusAccounting = Accounting::query()
            //         ->where('user_id', $userAuth->id)
            //         ->where('is_registration_bonus', true)
            //         ->where('system', false)
            //         ->first();

            //     $registrationBonusAmount = (empty($registrationBonusAccounting) and !empty($registrationBonusSettings['status']) and !empty($registrationBonusSettings['registration_bonus_amount'])) ? $registrationBonusSettings['registration_bonus_amount'] : null;
            // }


            // $data = [
            //     'pageTitle' => trans('public.checkout_page_title'),
            //     'paymentChannels' => $paymentChannels,
            //     'carts' => $carts,
            //     'subTotal' => null,
            //     'totalDiscount' => null,
            //     'tax' => null,
            //     'taxPrice' => null,
            //     'total' => 230,
            //     'userGroup' => $user->userGroup ? $user->userGroup->group : null,
            //     'order' => $order,
            //     'type' => $order->orderItems[0]->form_fee,
            //     'count' => 0,
            //     'userCharge' => $user->getAccountingCharge(),
            //     'razorpay' => $razorpay,
            //     'totalCashbackAmount' => null,
            //     'previousUrl' => url()->previous(),
            //     'offlinePayments' => $offlinePayments,
            //     'offlineBanks' => $offlineBanks,
            //     'accountCharge' => $userAuth->getAccountingCharge(),
            //     'readyPayout' => $userAuth->getPayout(),
            //     'totalIncome' => $userAuth->getIncome(),
            //     'editOfflinePayment' => $editOfflinePayment,
            //     'registrationBonusAmount' => $registrationBonusAmount,
            // ];


            return redirect('/payment/'.$order->id);

            // return view(getTemplate() . '.cart.payment', $data);
        } else {

            return $this->handlePaymentOrderWithZeroTotalAmount($order);
        }

        return redirect('/panel');
    }
    private function handlePaymentOrderWithZeroTotalAmount($order)
    {
        $order->update([
            'payment_method' => Order::$paymentChannel
        ]);

        $paymentController = new PaymentController();

        $paymentController->setPaymentAccounting($order);

        $order->update([
            'status' => Order::$paid
        ]);

        return redirect('/payments/status?order_id=' . $order->id);
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
