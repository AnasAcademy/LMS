@extends(getTemplate() . '.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
@endpush

<style>
    .requirement-card {
        padding: 40px 25px;
        box-shadow: 0px 0px 5px 0px #00000073;

    }

    .requirement-head {
        top: -15px;
        right: 30px;

    }

    .bundle-details {
        font-family: "IBM Plex Sans Arabic" !important;
    }
</style>

@section('content')

    @include('web.default.panel.requirements.requirements_includes.progress')

    <section class="row mt-80 mx-0 justify-content-center">
        @if (count($bundleInstallments) > 0)
            @php
                $count = 0;
            @endphp
            @foreach ($bundleInstallments as $bundleId => $bundleData)
                @php
                    $count++;
                @endphp
                <section
                    class="requirement-card bg-white position-relative col-lg-9 col-11 d-flex justify-content-center align-items-center rounded-sm mb-80">
                    <h2 class="position-absolute bg-white p-5 requirement-head">
                        {{ clean($bundleData['bundle']->bundle->title, 't') }}</h2>
                    @if (empty($bundleData['bundle']->studentRequirement))
                        <div class="w-100 text-center">
                            <p class="alert alert-info text-center">
                                لم يتم رفع متطلبات القبول بعد ، يرجي الضعط علي الزر للذهاب لصفحة متطلبات القبول
                            </p>
                            <a href="/panel/bundles/{{ $bundleData['bundle']->id }}/requirements"
                                class="btn btn-success p-5 mt-20 bg-secondary">للذهاب لرفع ملفات متطلبات القبول اضغط هنا</a>
                        </div>
                    @else
                        @if ($bundleData['bundle']->studentRequirement->status == 'pending')
                            <div class="w-100 text-center">
                                <p class="alert alert-info text-center">
                                    لقد تم بالفعل رفع متطلبات القبول يرجي الانتظار حتي يتم مراجعتها
                                </p>
                            </div>
                        @elseif ($bundleData['bundle']->studentRequirement->status == 'approved')
                            @php
                                $hasBought = $bundleData['bundle']->bundle->checkUserHasBought(auth()->user());
                                $canSale = ($bundleData['bundle']->bundle->canSale() and !$hasBought);

                            @endphp
                            <section class="row mx-0 col-12">
                                <div class="col-12 text-center mb-20">
                                    @if (!($hasBought or !empty($bundleData['bundle']->bundle->getInstallmentOrder())))
                                        <p class="alert alert-success text-center">
                                            لقد تم بالفعل رفع متطلبات القبول وتم الموافقة عليها يرجي الذهاب للخطوة التاليه
                                            للدفع
                                        </p>
                                    @endif
                                </div>

                                {{-- direct buy --}}
                                <div class="px-20 pb-30 col-12 {{ !empty($bundleData['installments']) && count($bundleData['installments']) ? 'col-md-6' : '' }} installment-card mb-md-0 mb-20"
                                    style="background-color: #fbfbfb">

                                    <section class="mt-20 text-start">
                                        @if (!($hasBought or !empty($bundleData['bundle']->bundle->getInstallmentOrder())))
                                            <h4 class="font-16 font-weight-bold text-dark-blue">
                                                دفع الرسوم كاملة
                                            </h4>
                                            <p class="text-gray font-14 text-ellipsis">كاملة شاملة الضريبة</p>
                                        @endif
                                        {{-- <div class="mt-20 d-flex align-items-center">
                                            <div class="progress card-progress flex-grow-1">
                                                <span class="progress-bar rounded-sm bg-primary" style="width: 100%"></span>
                                            </div>
                                            <div class="ml-10 font-12 text-danger">100% of capacity reached</div>
                                        </div> --}}
                                        {{-- bundle Price --}}
                                        @if ($bundleData['bundle']->bundle->price > 0)
                                            <div id="priceBox"
                                                class="d-flex align-items-center justify-content-center mt-20 {{ !empty($activeSpecialOffer) ? ' flex-column ' : '' }}">
                                                <div class="text-center">
                                                    @php
                                                        $realPrice = handleCoursePagePrice(
                                                            $bundleData['bundle']->bundle->price,
                                                        );
                                                    @endphp
                                                    <span id="realPrice"
                                                        data-value="{{ $bundleData['bundle']->bundle->price }}"
                                                        data-special-offer="{{ !empty($activeSpecialOffer) ? $activeSpecialOffer->percent : '' }}"
                                                        class="d-block @if (!empty($activeSpecialOffer)) font-16 text-gray text-decoration-line-through @else font-30 text-primary @endif">
                                                        {{ $realPrice['price'] }}
                                                    </span>

                                                    @if (!empty($realPrice['tax']) and empty($activeSpecialOffer))
                                                        <span class="d-block font-14 text-gray">+ {{ $realPrice['tax'] }}
                                                            tax</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center justify-content-center mt-20">
                                                <span class="font-36 text-primary">{{ trans('public.free') }}</span>
                                            </div>
                                        @endif

                                        <p class="bundle-details text-gray mt-10">
                                            الدبلومة عن بعد 100%
                                        </p>

                                        <p class="bundle-details text-gray mt-10">
                                            مكونة من
                                            {{ $bundleData['bundle']->bundle->bundleWebinars->count() }} فصول دراسية
                                        </p>
                                        <p class="bundle-details text-gray mt-10">
                                            مكونة من
                                            {{ convertMinutesToHourAndMinute($bundleData['bundle']->bundle->getBundleDuration()) }}
                                            ساعات دراسية
                                        </p>

                                    </section>
                                    <form action="{{ route('purchase_bundle') }}" method="POST">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="item_id"
                                            value="{{ $bundleData['bundle']->bundle->id }}">



                                        <div class="mt-20 d-flex flex-column">
                                            @if ($hasBought or !empty($bundleData['bundle']->bundle->getInstallmentOrder()))
                                                <button type="button" class="btn btn-primary"
                                                    disabled>{{ trans('panel.purchased') }}</button>
                                            @elseif($bundleData['bundle']->bundle->price > 0)
                                                <button type="{{ $canSale ? 'submit' : 'button' }}"
                                                    @if (!$canSale) disabled @endif
                                                    class="btn btn-primary">
                                                    @if (!$canSale)
                                                        {{ trans('update.disabled_add_to_cart') }}
                                                    @else
                                                        لدفع رسوم البرنامج كاملة اضغط هنا
                                                    @endif
                                                </button>

                                                @if ($canSale and !empty(getFeaturesSettings('direct_bundles_payment_button_status')))
                                                    <button type="button"
                                                        class="btn btn-outline-danger mt-20 js-bundle-direct-payment">
                                                        {{ trans('update.buy_now') }}
                                                    </button>
                                                @endif
                                            @else
                                                <a href="{{ $canSale ? '/bundles/' . $bundleData['bundle']->bundle->slug . '/free' : '#' }}"
                                                    class="btn btn-primary @if (!$canSale) disabled @endif">{{ trans('update.enroll_on_bundle') }}</a>
                                            @endif
                                        </div>

                                    </form>
                                </div>

                                @if (!empty($bundleData['installments']) && count($bundleData['installments']))
                                    <div class="col-12 col-md-6">

                                        {{-- Installments --}}
                                        @if (
                                            !empty($bundleData['installments']) and
                                                count($bundleData['installments']) and
                                                getInstallmentsSettings('installment_plans_position') == 'top_of_page')
                                            @foreach ($bundleData['installments'] as $installmentRow)
                                                @include('web.default.installment.card', [
                                                    'installment' => $installmentRow,
                                                    'itemPrice' => $bundleData['bundle']->bundle->getPrice(),
                                                    'itemId' => $bundleData['bundle']->bundle->id,
                                                    'itemType' => 'bundles',
                                                ])
                                            @endforeach
                                        @endif
                                        {{-- Installments --}}
                                        @if (
                                            !empty($bundleData['installments']) and
                                                count($bundleData['installments']) and
                                                getInstallmentsSettings('installment_plans_position') == 'bottom_of_page')
                                            @foreach ($bundleData['installments'] as $installmentRow)
                                                @include('web.default.installment.card', [
                                                    'installment' => $installmentRow,
                                                    'itemPrice' => $bundleData['bundle']->bundle->getPrice(),
                                                    'itemId' => $bundleData['bundle']->bundle->id,
                                                    'itemType' => 'bundles',
                                                ])
                                            @endforeach
                                        @endif
                                    </div>
                                @endif
                            </section>
                        @elseif ($bundleData['bundle']->studentRequirement->status == 'rejected')
                            <div class="w-100 text-center">
                                <p class="alert alert-danger text-center text-white">
                                    لقد تم رفض الملفات التي قمت برفعها يرجي مراجعة الميل لمشاهدة السبب ثم ارفع الملفات مرة
                                    اخري
                                </p>
                                <a href="/panel/bundles/{{ $bundleData['bundle']->id }}/requirements"
                                    class="btn btn-primary p-5 mt-20">للذهاب
                                    لرفع الملفات مرة اخري اضغط هنا</a>
                            </div>
                        @endif
                    @endif
                </section>
            @endforeach
        @else
            <section class="w-100 text-center">
                <p class="alert alert-info text-center">
                    لم يتم التسجيل في اي دبلومه بعد
                </p>
                <a href="/apply" class="btn bg-secondary text-white p-5 mt-20">للتسجيل اضغط علي هذا اللينك</a>
            </section>
        @endif

    </section>

@endsection

@push('scripts_bottom')
    <script src="/assets/vendors/cropit/jquery.cropit.js"></script>
    <script src="/assets/default/js/parts/img_cropit.min.js"></script>
    <script src="/assets/default/vendors/select2/select2.min.js"></script>

    <script>
        var editEducationLang = '{{ trans('site.edit_education') }}';
        var editExperienceLang = '{{ trans('site.edit_experience') }}';
        var saveSuccessLang = '{{ trans('webinars.success_store') }}';
        var saveErrorLang = '{{ trans('site.store_error_try_again') }}';
        var notAccessToLang = '{{ trans('public.not_access_to_this_content') }}';
    </script>

    <script src="/assets/default/js/panel/user_setting.min.js"></script>
@endpush
