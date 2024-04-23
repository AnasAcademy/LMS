<?php

namespace App\Http\Controllers\Api\Panel;

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
use App\Models\Category;
use Illuminate\Support\Facades\Cookie;
use App\Http\Controllers\web\PaymentController;

class ApplyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth("api")->user();
        $student = Student::where('user_id', $user->id)->first();
        $category = Category::where('parent_id', '!=', null)->get();
        return apiResponse2(1, 'apply_page', "successfully retrive data needed to apply to diploma", ['user' => $user, "student" => $student, "category" => $category]);
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

        $student = Student::where('user_id', auth("api")->user()->id)->first();

        // $categoryTitle = $category->title;

        if ($student) {
            $rules = [
                'user_id' => 'required',
                'category_id' => 'required|exists:id,categories',
                'bundle_id' =>  [
                    'required',
                    function ($attribute, $value, $fail) {
                        $user = auth("api")->user();
                        $student = Student::where('user_id', $user->id)->first();

                        if ($student && $student->bundles()->where('bundles.id', $value)->exists()) {
                            $fail('User has already applied for this bundle.');
                        }
                    },
                ],
                'terms' => 'accepted'
            ];
        } else {

            $rules = [
                'user_id' => 'required',
                'category_id' => 'required|exists:categories,id',
                'bundle_id' =>  [
                    'required',
                    function ($attribute, $value, $fail) {
                        $user = auth("api")->user();
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
                'deaf' => 'required|in:0,1',
                'disabled_type' => $request->disabled == 1 ? 'required|string|max:255|min:3' : 'nullable',
                'gender' => 'required|in:male,female',
                'healthy_problem' => $request->healthy == 1 ? 'required|string|max:255|min:3' : 'nullable',
                'nationality' => 'required|string|min:3|max:25',
                'job' => 'nullable',
                'job_type' => 'nullable',
                'referral_person' => 'required|string|min:3|max:255',
                'relation' => 'required|string|min:3|max:255',
                'referral_email' => 'required|email|max:255|regex:/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/',
                'referral_phone' => 'required|min:3|max:20',
                'about_us' => 'required|string|min:3|max:255',
                'terms' => 'accepted',
            ];
        }

        validateParam($request->all(), $rules);

        $category = Category::where('id', $request->category_id)->first();
        $rules2 = [
            'educational_qualification_country' => $category->title != "دبلوم متوسط" ? 'required|string|max:255|min:3|regex:/^(?=.*[\p{Arabic}\p{L}])[0-9\p{Arabic}\p{L}\s]+$/u' : '',
            'secondary_school_gpa' => $category->title == "دبلوم متوسط" ? 'required|string|max:255|min:1' : '',
            'educational_area' => 'required|string|max:255|min:3|regex:/^(?=.*[\p{Arabic}\p{L}])[0-9\p{Arabic}\p{L}\s]+$/u',
            'secondary_graduation_year' => $category->title == "دبلوم متوسط" ? 'required|numeric|regex:/^\d{3,10}$/' : '',
            'school' => $category->title == "دبلوم متوسط" ? 'required|string|max:255|min:3|regex:/^(?=.*[\p{Arabic}\p{L}])[0-9\p{Arabic}\p{L}\s]+$/u' : '',
            'university' => $category->title != "دبلوم متوسط" ? 'required|string|max:255|min:3|regex:/^(?=.*[\p{Arabic}\p{L}])[0-9\p{Arabic}\p{L}\s]+$/u' : '',
            'faculty' => $category->title != "دبلوم متوسط" ? 'required|string|max:255|min:3|regex:/^(?=.*[\p{Arabic}\p{L}])[0-9\p{Arabic}\p{L}\s]+$/u' : '',
            'education_specialization' => $category->title != "دبلوم متوسط" ? 'required|string|max:255|min:3|regex:/^(?=.*[\p{Arabic}\p{L}])[0-9\p{Arabic}\p{L}\s]+$/u' : '',
            'graduation_year' => $category->title != "دبلوم متوسط" ? 'required|numeric|regex:/^\d{3,10}$/' : '',
            'gpa' => $category->title != "دبلوم متوسط" ? 'required|string|max:255|min:1' : '',
        ];
        validateParam($request->all(), $rules2);

        Cookie::queue('user_data', json_encode($request->all()));
        $user_data = $request->all();
        $user = auth("api")->user();

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
                "user_data" =>$user_data,
                'order' => $order,
                'type' => $order->orderItems[0]->form_fee,
                'count' => 0,
                'userCharge' => $user->getAccountingCharge(),
                'razorpay' => $razorpay,
                'totalCashbackAmount' => null,
                'previousUrl' => url()->previous(),
            ];

            return apiResponse2(1, 'applied', "application for diploma is successfully, pay to continue", $data);
        } else {

            return $this->handlePaymentOrderWithZeroTotalAmount($order);
        }

        return apiResponse2(1, 'applied', "application for diploma is successfully");
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
        return apiResponse2(1, 'paid', "diploma is bought successfully");
        // return redirect('/payments/status?order_id=' . $order->id);
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
