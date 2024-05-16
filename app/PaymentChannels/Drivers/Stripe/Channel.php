<?php

namespace App\PaymentChannels\Drivers\Stripe;

use App\Http\Controllers\Auth\RegisterController;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentChannel;
use App\PaymentChannels\BasePaymentChannel;
use App\PaymentChannels\IChannel;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class Channel extends BasePaymentChannel implements IChannel
{
    protected $currency;
    protected $api_key;
    protected $api_secret;
    protected $order_session_key;

    /**
     * Channel constructor.
     * @param PaymentChannel $paymentChannel
     */
    public function __construct(PaymentChannel $paymentChannel)
    {
        $this->currency = currency();

        $this->api_key = env('STRIPE_KEY');
        $this->api_secret = env('STRIPE_SECRET');

        $this->order_session_key = 'strip.payments.order_id';
    }

    public function paymentUserRequest()
    {
        $price = $this->makeAmountByCurrency(230, $this->currency);
        $generalSettings = getGeneralSettings();
        $currency = currency();

        Stripe::setApiKey($this->api_secret);
        $checkout = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => $currency,
                        'unit_amount_decimal' => $price * 100,
                        'product_data' => [
                            'name' => $generalSettings['site_name'] . ' payment',
                        ],
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => $this->makeCallbackUrlUser('success'),
            'cancel_url' => $this->makeCallbackUrlUser('cancel'),
        ]);
        // dd($checkout);
        /*$order->update([
            'reference_id' => $checkout->id,
        ]);*/

        // session()->put($this->order_session_key, $order->id);

        $Html = '<script src="https://js.stripe.com/v3/"></script>';
        $Html .= '<script type="text/javascript">let stripe = Stripe("' . $this->api_key . '");';
        $Html .= 'stripe.redirectToCheckout({ sessionId: "' . $checkout->id . '" }); </script>';

        echo $Html;
    }
    public function paymentRequest(Order $order)
    {
        $price = $this->makeAmountByCurrency($order->total_amount, $this->currency);
        $generalSettings = getGeneralSettings();
        $currency = currency();

        Stripe::setApiKey($this->api_secret);
        $checkout = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => $currency,
                        'unit_amount_decimal' => $price * 100,
                        'product_data' => [
                            'name' => $generalSettings['site_name'] . ' payment',
                        ],
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => $this->makeCallbackUrl('success', $order->id),
            'cancel_url' => $this->makeCallbackUrl('cancel', $order->id),
        ]);
        // dd($checkout);
        /*$order->update([
            'reference_id' => $checkout->id,
        ]);*/

        // session()->put($this->order_session_key, $order->id);

        $Html = '<script src="https://js.stripe.com/v3/"></script>';
        $Html .= '<script type="text/javascript">let stripe = Stripe("' . $this->api_key . '");';
        $Html .= 'stripe.redirectToCheckout({ sessionId: "' . $checkout->id . '" }); </script>';

        echo $Html;
    }

    private function makeCallbackUrlUser($status)
    {
        return url("/payments/verify/Stripe?status=$status&session_id={CHECKOUT_SESSION_ID}");
    }

    private function makeCallbackUrl($status, $order_id)
    {
        return url("/payments/verify/Stripe?status=$status&order_id=$order_id&session_id={CHECKOUT_SESSION_ID}");
    }

    public function verify(Request $request)
    {
        $data = $request->all();
        $status = $data['status'];
        $order_id = $data['order_id'] ?? null;

        $user = auth()->user();

        $order = Order::where('id', $order_id)
            ->where('user_id', $user->id)
            ->first();

        if ($status == 'success' and !empty($request->session_id) and !empty($order)) {
            Stripe::setApiKey($this->api_secret);
            $session = Session::retrieve($request->session_id);
            if (!empty($session) and $session->payment_status == 'paid') {
                $order->update([
                    'status' => Order::$paying
                ]);
                return $order;
            }
        }

        // is fail

        if (!empty($order)) {
            $order->update(['status' => Order::$fail]);
        }

        if (empty($user)) {
            if ($status == 'success' and !empty($request->session_id)) {
                Stripe::setApiKey($this->api_secret);
                $session = Session::retrieve($request->session_id);
                if (!empty($session) and $session->payment_status == 'paid') {

                    $data=$this->user_data($request->cookie('user_data'));
                    $user_data=collect($data)->except(['bundle_id'])->toArray();
                    $user = (new RegisterController())->create($user_data);
                    $user->update(['status' => User::$active]);
                    event(new Registered($user));

                    $notifyOptions = [
                        '[u.name]' => $user->full_name,
                        '[u.role]' => trans("update.role_{$user->role_name}"),
                        '[time.date]' => dateTimeFormat($user->created_at, 'j M Y H:i'),
                    ];

                    sendNotification("new_registration", $notifyOptions, 1);
                    \Auth::login($user);
                    
                    $bundle_id=$data['bundle_id'];
                    $order=$this->makeUserOrder($user,$bundle_id);
                    $order->update([
                        'status' => Order::$paying
                    ]);
                    return $order;
                }
            }
        }
        return $order;
    }

    public function user_data($cookie)
    {
        $collectedData = [];
        if ($cookie) {
            $userData = json_decode($cookie, true);
            $desiredKeys = ['ar_name', 'password', 'email','timezone','bundle_id']; // List of keys you want to collect
            foreach ($desiredKeys as $key) {
                $collectedData[$key] = $userData[$key];
            }
            // $studentData = collect($userData)->except(['category_id', 'bundle_id', 'terms', 'certificate', 'timezone', 'password', 'password_confirmation'])->toArray();
        }
        return  $collectedData;

    }
    public function makeUserOrder($user,$bundle_id){
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
            'bundle_id' => $bundle_id ?? null,
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
        return $order;
    }

}
