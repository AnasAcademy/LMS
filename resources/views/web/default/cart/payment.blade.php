@extends(getTemplate() . '.layouts.app')


@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
@endpush



@section('content')


    @php
        $title = "<h1 class='font-30 text-white font-weight-bold'>" . trans('cart.checkout') . '</h1>';
        $subTitle = "<span class='payment-hint font-20 text-white d-block'>";

        if ($count > 0) {
            $subTitle .= $total . ' ريال سعودي ' . trans('cart.for_items', ['count' => $count]);
        } elseif (!empty($type) && $type == 1) {
            $subTitle .= 'رسوم حجز مقعد : ' . $total . ' ريال سعودي';
            // $subTitle .= 'الرسوم الدراسية للبرنامج : '.($total).' ريال سعودي';
        }
        else if(!empty($order->orderItems[0]->bundle)){
             $subTitle .= 'الرسوم الدراسية للبرنامج '.($order->orderItems[0]->bundle->title).': '.($total).' ريال سعودي';
        }
        else if(!empty($order->orderItems[0]->webinar)){
            $subTitle .= 'الرسوم الدراسية للدورة '.($order->orderItems[0]->webinar->title).': '.($total).' ريال سعودي';
        }
        else if(!empty($order->orderItems[0]->service)){
            $subTitle .= 'الرسوم لطلب خدمة  '.($order->orderItems[0]->service->title).': '.($total).' ريال سعودي';
        }
        // close subtitle
        $subTitle .= '</span>';
    @endphp

    @include('web.default.includes.hero_section', ['inner' => $title . $subTitle]);

    <section class="container mt-45">

        @if (!empty($totalCashbackAmount))
            <div class="d-flex align-items-center mb-25 p-15 success-transparent-alert">
                <div class="success-transparent-alert__icon d-flex align-items-center justify-content-center">
                    <i data-feather="credit-card" width="18" height="18" class=""></i>
                </div>

                <div class="ml-10">
                    <div class="font-14 font-weight-bold ">{{ trans('update.get_cashback') }}</div>
                    <div class="font-12 ">
                        {{ trans('update.by_purchasing_this_cart_you_will_get_amount_as_cashback', ['amount' => handlePrice($totalCashbackAmount)]) }}
                    </div>
                </div>
            </div>
        @endif

        @php

            $showOfflineFields = false;
            if (
                $errors->any() or
                !empty($editOfflinePayment)
            ) {
                $showOfflineFields = true;
            }

            $isMultiCurrency = !empty(getFinancialCurrencySettings('multi_currency'));
            $userCurrency = currency();
            $invalidChannels = [];
        @endphp

        <h2 class="section-title">{{ trans('financial.select_a_payment_gateway') }}</h2>

        <form action="/payments/payment-request" method="post" class=" mt-25" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" name="order_id" value="{{ $order->id }}">

            <div class="row">
                {{-- online  --}}
                @if (!empty($paymentChannels))
                    @foreach ($paymentChannels as $paymentChannel)
                        @if (!$isMultiCurrency or !empty($paymentChannel->currencies) and in_array($userCurrency, $paymentChannel->currencies))
                            <div class="col-6 col-lg-4 mb-40 charge-account-radio ">
                                <input type="radio" name="gateway" class="online-gateway" checked
                                    id="{{ $paymentChannel->title }}" data-class="{{ $paymentChannel->class_name }}"
                                    value="{{ $paymentChannel->id }}">
                                <label for="{{ $paymentChannel->title }}"
                                    class="rounded-sm p-20 p-lg-45 d-flex flex-column align-items-center justify-content-center">
                                    {{-- <img src="{{ $paymentChannel->image }}" width="120" height="60" alt=""> --}}
                                    @include("web.default.cart.includes.online_payment_icon")
                                    <p class="mt-30 mt-lg-50 font-weight-500 text-dark-blue">
                                        {{ trans('financial.pay_via') }}
                                        <span class="font-weight-bold font-14">{{ $paymentChannel->title }}</span>
                                    </p>
                                </label>
                            </div>
                        @else
                            @php
                                $invalidChannels[] = $paymentChannel;
                            @endphp
                        @endif
                    @endforeach
                @endif

                {{-- offline --}}
                @if (!empty(getOfflineBankSettings('offline_banks_status')))
                    <div class="col-6 col-lg-4 mb-40 charge-account-radio">
                        <input type="radio" name="gateway" id="offline" value="offline"
                            @if (old('gateway') == 'offline' or !empty($editOfflinePayment)) checked @endif>
                        <label for="offline"
                            class="rounded-sm p-20 p-lg-45 d-flex flex-column align-items-center justify-content-center">
                            {{-- <img src="/assets/default/img/activity/pay.svg" width="120" height="60" alt=""> --}}
                            @include("web.default.cart.includes.offline_payment_icon")
                            <p class="mt-30 mt-lg-50 font-weight-500 text-dark-blue">{{ trans('financial.pay_via') }}
                                <span class="font-weight-bold">{{ trans('financial.offline') }}</span>
                            </p>
                        </label>
                    </div>
                @endif

                @error('gateway')
                    <div class="invalid-feedback d-block"> {{ $message }}</div>
                @enderror

                {{-- account discharge --}}
                {{-- <div class="col-6 col-lg-4 mb-40 charge-account-radio">
                    <input type="radio" @if (empty($userCharge) or $total > $userCharge) disabled @endif name="gateway" id="offline"
                        value="credit">
                    <label for="offline"
                        class="rounded-sm p-20 p-lg-45 d-flex flex-column align-items-center justify-content-center">
                        <img src="/assets/default/img/activity/pay.svg" width="120" height="60" alt="">

                        <p class="mt-30 mt-lg-50 font-weight-500 text-dark-blue">
                            {{ trans('financial.account') }}
                            <span class="font-weight-bold">{{ trans('financial.charge') }}</span>
                        </p>

                        <span class="mt-5">{{ handlePrice($userCharge) }}</span>
                    </label>
                </div> --}}
            </div>

            @if (!empty($invalidChannels))
                <div class="d-flex align-items-center mt-30 rounded-lg border p-15">
                    <div class="size-40 d-flex-center rounded-circle bg-gray200">
                        <i data-feather="info" class="text-gray" width="20" height="20"></i>
                    </div>
                    <div class="ml-5">
                        <h4 class="font-14 font-weight-bold text-gray">{{ trans('update.disabled_payment_gateways') }}</h4>
                        <p class="font-12 text-gray">{{ trans('update.disabled_payment_gateways_hint') }}</p>
                    </div>
                </div>

                <div class="row mt-20">
                    @foreach ($invalidChannels as $invalidChannel)
                        <div class="col-6 col-lg-4 mb-40 charge-account-radio">
                            <div
                                class="disabled-payment-channel bg-white border rounded-sm p-20 p-lg-45 d-flex flex-column align-items-center justify-content-center">
                                <img src="{{ $invalidChannel->image }}" width="120" height="60" alt="">

                                <p class="mt-30 mt-lg-50 font-weight-500 text-dark-blue">
                                    {{ trans('financial.pay_via') }}
                                    <span class="font-weight-bold font-14">{{ $invalidChannel->title }}</span>
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

             {{-- offline banks --}}
        @if (!empty(getOfflineBankSettings('offline_banks_status')))
            <section class="mt-40 js-offline-payment-input mb-3" style="{{ !$showOfflineFields ? 'display:none' : '' }}">
                <h2 class="section-title">{{ trans('financial.bank_accounts_information') }}</h2>

                <div class="row mt-25">
                    @foreach ($offlineBanks as $offlineBank)
                        <div class="col-12 col-lg-7 mb-30 mb-lg-0">
                            <div
                                class="py-25 px-20 rounded-sm panel-shadow d-flex flex-column align-items-center justify-content-center">
                                <img src="{{ $offlineBank->logo }}" width="120" height="60" alt="">

                                <div class="mt-15 mt-30 w-100">

                                    <div class="d-flex align-items-center justify-content-between">
                                        <span
                                            class="font-14 font-weight-500 text-secondary">{{ trans('public.name') }}:</span>
                                        <span class="font-14 font-weight-500 text-gray">{{ $offlineBank->title }}</span>
                                    </div>

                                    @foreach ($offlineBank->specifications as $specification)
                                        <div class="d-flex align-items-center justify-content-between mt-10">
                                            <span
                                                class="font-14 font-weight-500 text-secondary">{{ $specification->name }}:</span>
                                            <span
                                                class="font-14 font-weight-500 text-gray">{{ $specification->value }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

            {{-- offline inputs --}}
            <div class="">
                <h3 class="section-title mb-20 js-offline-payment-input"
                    style="{{ !$showOfflineFields ? 'display:none' : '' }}">{{ trans('financial.finalize_payment') }}
                </h3>

                <div class="row">

                    <div class="col-12 col-lg-3 mb-25 mb-lg-0 js-offline-payment-input "
                        style="{{ !$showOfflineFields ? 'display:none' : '' }}">
                        <div class="form-group">
                            <label class="input-label">{{ trans('financial.account') }}</label>
                            <select name="account" class="form-control @error('account') is-invalid @enderror">
                                <option selected disabled>{{ trans('financial.select_the_account') }}</option>

                                @foreach ($offlineBanks as $offlineBank)
                                    <option value="{{ $offlineBank->id }}"
                                        @if (old('account')== $offlineBank->id) selected @endif>{{ $offlineBank->title }}
                                    </option>
                                @endforeach
                            </select>

                            @error('account')
                                <div class="invalid-feedback"> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    <div class="col-12 col-lg-3 mb-25 mb-lg-0 js-offline-payment-input "
                        style="{{ !$showOfflineFields ? 'display:none' : '' }}">
                        <div class="form-group">
                            <label for="IBAN" class="input-label"> اي بان (IBAN)</label>
                            <input type="text" name="IBAN" id="IBAN"
                                value="{{ old('IBAN') }}"
                                class="form-control @error('IBAN') is-invalid @enderror" />
                            @error('IBAN')
                                <div class="invalid-feedback"> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12 col-lg-3 mb-25 mb-lg-0 js-offline-payment-input "
                        style="{{ !$showOfflineFields ? 'display:none' : '' }}">
                        <div class="form-group">
                            <label class="input-label">{{ trans('update.attach_the_payment_photo') }}</label>

                            <label for="attachmentFile" id="attachmentFileLabel" class="custom-upload-input-group flex-row-reverse ">
                                <span class="custom-upload-icon text-white">
                                    <i data-feather="upload" width="18" height="18" class="text-white"></i>
                                </span>
                                <div class="custom-upload-input"></div>
                            </label>

                            <input type="file" name="attachment" id="attachmentFile" accept=".jpeg,.jpg,.png"
                                class="form-control h-auto invisible-file-input @error('attachment') is-invalid @enderror"
                                value="" />
                            @error('attachment')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>


                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mt-45">
                <span class="font-16 font-weight-500 text-gray">{{ trans('financial.total_amount') }}
                    {{ handlePrice($total) }}</span>
                <button type="button" id="paymentSubmit"
                    class="btn btn-sm btn-primary">{{ trans('public.start_payment') }}</button>
            </div>
        </form>

        @if (!empty($razorpay) and $razorpay)
            <form action="/payments/verify/Razorpay" method="get">
                <input type="hidden" name="order_id" value="{{ $order->id }}">

                <script src="https://checkout.razorpay.com/v1/checkout.js" data-key="{{ env('RAZORPAY_API_KEY') }}"
                    data-amount="{{ (int) ($order->total_amount * 100) }}" data-buttontext="product_price" data-description="Rozerpay"
                    data-currency="{{ currency() }}" data-image="{{ $generalSettings['logo'] }}"
                    data-prefill.name="{{ $order->user->full_name }}" data-prefill.email="{{ $order->user->email }}"
                    data-theme.color="#43d477"></script>
            </form>
        @endif






    </section>

@endsection

@push('scripts_bottom')
    <script src="/assets/default/js/parts/payment.min.js"></script>
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>

    <script src="/assets/default/js/panel/financial/account.min.js"></script>



    <script src="/assets/default/js//parts/main.min.js"></script>
    <script src="/assets/default/js/panel/public.min.js"></script>
@endpush
