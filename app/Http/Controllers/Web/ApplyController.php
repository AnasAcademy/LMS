<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\Webinar;
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
use App\Models\Code;
use App\Models\StudyClass;
use Illuminate\Support\Facades\Date;

class ApplyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Bundle $bundle)
    {
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->first();
        // $categories = Category::whereNull('parent_id')->whereHas('bundles')->get();

        $categories = Category::whereNull('parent_id')
        ->where(function ($query) {
            $query->whereHas('bundles', function ($query) {
                $query->where('status', "active");
            })
            ->orWhereHas('subCategories', function ($query) {
                $query->whereHas('bundles', function ($query) {
                    $query->where('status', "active");
                });
            });
        })->get();

        $courses = Webinar::where('unattached', 1)->get();

        return view(getTemplate() . '.pages.application_form', compact('user', 'categories', 'student', 'courses', 'bundle'));
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
        // $categories = Category::whereNull('parent_id')->whereHas('bundles')->get();
        $categories = Category::whereNull('parent_id')
        ->where(function ($query) {
            $query->whereHas('bundles', function ($query) {
                $query->where('status', "active");
            })
            ->orWhereHas('subCategories', function ($query) {
                $query->whereHas('bundles', function ($query) {
                    $query->where('status', "active");
                });
            });
        })->get();

            // dd($categories);
        $courses = Webinar::where('unattached', 1)->get();
        return view(getTemplate() . '.panel.newEnrollment.index', compact('user', 'categories', 'student', 'courses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkout(Request $request, $carts = null)
    {
        app()->setLocale('ar');

        $category = Category::where('id', $request->category_id)->first();
        $bundle = Bundle::where('id', $request->bundle_id)->first();
        $webinar = Webinar::where('id', $request->webinar_id)->first();
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->first();

        try {
            if ($student) {
                $validatedData = $request->validate([
                    'user_id' => 'required',
                    // 'type' => 'required|in:courses,diplomas',
                    // 'category_id' => [
                    //     function ($attribute, $value, $fail) use ($request) {
                    //         $type = $request->input('type');
                    //         if ($type && $type == 'diplomas' && empty ($value)) {
                    //             $fail('يجب تحديد الدرجه العلميه ');
                    //         }
                    //     }
                    // ],
                    'bundle_id' => [
                        'required',
                        function ($attribute, $value, $fail) use ($request) {
                            if (empty($value)) {
                                $fail('يجب تحديد التخصص العلمي ');
                            }
                            $user = auth()->user();
                            $student = Student::where('user_id', $user->id)->first();

                            if ($student && $student->bundles()->where('bundles.id', $value)->exists()) {
                                $fail('User has already applied for this bundle.');
                            }
                        },
                    ],
                    'webinar_id' => [
                        function ($attribute, $value, $fail)  use ($request) {
                            $type = $request->input('type');
                            if ($type && $type == 'courses' && empty($value)) {
                                $fail('يجب تحديد الدورة  ');
                            }
                            $user = auth()->user();
                            $purchasedWebinarsIds = $user->getAllPurchasedWebinarsIds();

                            if ($user && in_array($value, $purchasedWebinarsIds)) {
                                $fail('User has already applied for this webinar.');
                            }
                        },
                    ],
                    'terms' => 'accepted',
                    'certificate' => $bundle ? ($bundle->has_certificate ? 'required|boolean' : "") : '',
                    'requirement_endorsement' => $bundle ? 'accepted'  : ''
                ]);
            } else {
                $validatedData = $request->validate([
                    'user_id' => 'required',
                    // 'type' => 'required|in:courses,diplomas',
                    // 'category_id' => [
                    //     function ($attribute, $value, $fail) use ($request) {
                    //         $type = $request->input('type');
                    //         if ($type && $type == 'diplomas' && empty ($value)) {
                    //             $fail('يجب تحديد الدرجه العلميه ');
                    //         }
                    //     }
                    // ],
                    'bundle_id' => "required|exists:bundles,id",
                    'webinar_id' => [
                        function ($attribute, $value, $fail)  use ($request) {
                            $type = $request->input('type');
                            if ($type && $type == 'courses' && empty($value)) {
                                $fail('يجب تحديد الدورة  ');
                            }
                            $user = auth()->user();
                            $purchasedWebinarsIds = $user->getAllPurchasedWebinarsIds();

                            if ($user && in_array($value, $purchasedWebinarsIds)) {
                                $fail('User has already applied for this webinar.');
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
                    // 'email' => 'required|email|max:255|regex:/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/',
                    'birthdate' => 'required|date',
                    // 'phone' => 'required|min:5|max:20',
                    // 'mobile' => 'required|min:5|max:20',
                    // 'educational_qualification_country' => $category ? ($category->education ? 'required|string|max:255|min:3|regex:/^(?=.*[\p{Arabic}\p{L}])[0-9\p{Arabic}\p{L}\s]+$/u' : '') : '',
                    // 'secondary_school_gpa' => $category ? (!$category->education ? 'required|string|max:255|min:1' : '') : '',
                    // 'educational_area' => $category ? 'required|string|max:255|min:3|regex:/^(?=.*[\p{Arabic}\p{L}])[0-9\p{Arabic}\p{L}\s]+$/u' : '',
                    // 'secondary_graduation_year' => $category ? (!$category->education ? 'required|numeric|regex:/^\d{3,10}$/' : '') : '',
                    // 'school' => $category ? (!$category->education ? 'required|string|max:255|min:3|regex:/^(?=.*[\p{Arabic}\p{L}])[0-9\p{Arabic}\p{L}\s]+$/u' : '') : '',
                    // 'university' => $category ? ($category->education ? 'required|string|max:255|min:3|regex:/^(?=.*[\p{Arabic}\p{L}])[0-9\p{Arabic}\p{L}\s]+$/u' : '') : '',
                    // 'faculty' => $category ? ($category->education ? 'required|string|max:255|min:3|regex:/^(?=.*[\p{Arabic}\p{L}])[0-9\p{Arabic}\p{L}\s]+$/u' : '') : '',
                    // 'education_specialization' => $category ? ($category->education ? 'required|string|max:255|min:3|regex:/^(?=.*[\p{Arabic}\p{L}])[0-9\p{Arabic}\p{L}\s]+$/u' : '') : '',
                    // 'graduation_year' => $category ? ($category->education ? 'required|numeric|regex:/^\d{3,10}$/' : '') : '',
                    // 'gpa' => $category ? ($category->education ? 'required|string|max:255|min:1' : '') : '',
                    // 'deaf' => 'required|in:0,1',
                    // 'disabled_type' => $category ? ($request->disabled == 1 ? 'required|string|max:255|min:3' : 'nullable') : '',
                    'gender' => 'required|in:male,female',
                    // 'healthy_problem' => $request->healthy == 1 ? 'required|string|max:255|min:3' : 'nullable',
                    // 'nationality' => 'required|string|min:3|max:25',
                    // 'job' => $request->workStatus == 1 ? 'required' : 'nullable',
                    // 'job_type' => $request->workStatus == 1 ? 'required' : 'nullable',
                    // 'referral_person' => 'required|string|min:3|max:255',
                    // 'relation' => 'required|string|min:3|max:255',
                    // 'referral_email' => 'required|email|max:255|regex:/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/',
                    // 'referral_phone' => 'required|min:3|max:20',
                    'about_us' => 'required|string|min:3|max:255',
                    'terms' => 'accepted',
                    'certificate' => $bundle ? ($bundle->has_certificate ? 'required|boolean' : "") : '',
                    'requirement_endorsement' => $bundle ? 'accepted'  : ''
                ]);

                if ($request->direct_register) {
                    $studentData = [
                        'ar_name' => $request->ar_name,
                        'ar_name' => $request->en_name,
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'phone' => $user->mobile,
                        'mobile' => $user->mobile,
                        'gender' => $request->gender,
                        'birthdate' => $request->birthdate,
                        'identifier_num' => $request->identifier_num,
                        'country' => $request->country,
                        'area' => $request->area,
                        'city' => $request->city,
                        'town' => $request->town,
                        'about_us' => $request->about_us,

                    ];

                    $student = Student::create($studentData);
                    $code = generateStudentCode();
                    $user->update([
                        'user_code' => $code,
                        'access_content' => 1
                    ]);

                    // update code
                    Code::latest()->first()->update(['lst_sd_code' => $code]);
                }
            }
        } catch (\Exception $e) {
            // dd($e);
            return redirect()->back()->withErrors($e->validator)->withInput();
            // dd($e);
        }




        if ($request->direct_register) {

            $class =  StudyClass::get()->last();
            if(!$class){
                $class = StudyClass::create(['title'=> "الدفعة الأولي"]);
            }
            $student->bundles()->attach($request->bundle_id, ['certificate' => (!empty($request['certificate'])) ? $request['certificate'] : null, 'class_id' => $class->id,
            'created_at' => Date::now(),  // Set current timestamp for created_at
            'updated_at' => Date::now()]);

            if(count($bundle->category->categoryRequirements)>0){
                return redirect("/panel/requirements");
            }else{
                return redirect("/panel/requirements/applied");
            }
        }

        Cookie::queue('user_data', json_encode($validatedData));


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
            'webinar_id' => $request->webinar_id ?? null,
            'bundle_id' => $request->bundle_id ?? null,
            'certificate_template_id' => null,
            'certificate_bundle_id' => null,
            'form_fee' => 1,
            'product_id' => null,
            'product_order_id' => null,
            'reserve_meeting_id' => null,
            'subscribe_id' => null,
            'promotion_id' => null,
            'gift_id' => null,
            'installment_payment_id' => null,
            'ticket_id' => null,
            'discount_id' => null,
            'amount' =>  230,
            'total_amount' =>230,
            // 'amount' => $request->type == 'diplomas' ? 230 : $webinar->price ?? 0,
            // 'total_amount' => $request->type == 'diplomas' ? 230 : $webinar->price ?? 0,
            'tax' => null,
            'tax_price' => 0,
            'commission' => 0,
            'commission_price' => 0,
            'product_delivery_fee' => 0,
            'discount' => 0,
            'created_at' => time(),
        ]);


        if (!empty($order) and $order->total_amount > 0) {

            return redirect('/payment/' . $order->id);

            // return view(getTemplate() . '.cart.payment', $data);

            // $data = [
            //     'pageTitle' => trans('public.checkout_page_title'),
            //     'paymentChannels' => $paymentChannels,
            //     'carts' => $carts,
            //     'subTotal' => null,
            //     'totalDiscount' => null,
            //     'tax' => null,
            //     'taxPrice' => null,
            //     'total' => $request->type=='diplomas' ? 230: $webinar->price,
            //     'userGroup' => $user->userGroup ? $user->userGroup->group : null,
            //     'order' => $order,
            //     'type' => $order->orderItems[0]->form_fee,
            //     'count' => 0,
            //     'userCharge' => $user->getAccountingCharge(),
            //     'razorpay' => $razorpay,
            //     'totalCashbackAmount' => null,
            //     'previousUrl' => url()->previous(),
            // ];

            // return view(getTemplate() . '.cart.payment', $data);
        }
        // else {

        //     return $this->handlePaymentOrderWithZeroTotalAmount($order);
        // }



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

        return redirect('/payments/status/' . $order->id);
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
