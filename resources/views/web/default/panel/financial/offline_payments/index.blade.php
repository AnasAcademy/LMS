@extends(getTemplate() . '.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
@endpush

@section('content')


    @if (\Session::has('msg'))
        <div class="alert alert-warning">
            <ul>
                <li>{!! \Session::get('msg') !!}</li>
            </ul>
        </div>
    @endif

    @php
        $showOfflineFields = false;
        if (
            $errors->has('date') or
            $errors->has('referral_code') or
            $errors->has('account') or
            !empty($editOfflinePayment)
        ) {
            $showOfflineFields = true;
        }

        $isMultiCurrency = !empty(getFinancialCurrencySettings('multi_currency'));
        $userCurrency = currency();
        $invalidChannels = [];
    @endphp


    @if ($offlinePayments->count() > 0)
        <section class="mt-40">
            <h2 class="section-title">{{ trans('financial.offline_transactions_history') }}</h2>

            <div class="panel-section-card py-20 px-25 mt-20">
                <div class="row">
                    <div class="col-12 ">
                        <div class="table-responsive">
                            <table class="table text-center custom-table">
                                <thead>
                                    <tr>
                                        <th>{{ trans('financial.bank') }}</th>
                                        <th>{{ 'البنك المحول منه' }}</th>
                                        <th>{{ 'رقم الحساب المحول منه' }}</th>
                                        <th>{{ 'اي بان (IBAN)' }}</th>
                                        <th>{{ 'سويفت كود (swift code)' }}</th>
                                        <th class="text-center">{{ trans('panel.amount') }} ({{ $currency }})</th>
                                        <th class="text-center">غرض الدفع</th>
                                        <th class="text-center">{{ trans('update.attachment') }}</th>
                                        <th class="text-center">{{ trans('public.status') }}</th>
                                        <th class="text-center">{{ trans('public.controls') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($offlinePayments as $offlinePayment)
                                        <tr>
                                            <td class="text-left">
                                                <div class="d-flex flex-column">

                                                    @if (!empty($offlinePayment->offlineBank))
                                                        <span
                                                            class="font-weight-500 text-dark-blue">{{ $offlinePayment->offlineBank->title }}</span>
                                                    @else
                                                        <span class="font-weight-500 text-dark-blue">-</span>
                                                    @endif
                                                    <span
                                                        class="font-12 text-gray">{{ dateTimeFormat($offlinePayment->pay_date, 'j M Y H:i') }}</span>
                                                </div>
                                            </td>
                                            <td class="text-left align-middle">
                                                <span>{{ $offlinePayment->user_bank }}</span>
                                            </td>

                                            <td class="text-left align-middle">
                                                <span>{{ $offlinePayment->user_account_number }}</span>
                                            </td>

                                            <td class="text-left align-middle">
                                                <span>{{ $offlinePayment->iban }}</span>
                                            </td>

                                            <td class="text-left align-middle">
                                                <span>{{ $offlinePayment->reference_number }}</span>
                                            </td>

                                            <td class="text-center align-middle">
                                                <span
                                                    class="font-16 font-weight-bold text-primary">{{ handlePrice($offlinePayment->amount, false) }}</span>
                                            </td>

                                            <td class="text-center align-middle">
                                                <span class="font-16 font-weight-bold text-secondary">
                                                    @if ($offlinePayment->pay_for == 'form_fee')
                                                        رسوم حجز مقعد دراسي ل
                                                        {{ $offlinePayment->order->orderItems->first()->bundle->title }}
                                                    @elseif ($offlinePayment->pay_for == 'bundle')
                                                        الدفع كامل ل
                                                        {{ $offlinePayment->order->orderItems->first()->bundle->title }}
                                                    @elseif ($offlinePayment->pay_for == 'installment')
                                                        {{ $offlinePayment->order->orderItems->first()->installmentPayment->step->installmentStep->title ?? 'القسط الأول' }}

                                                        ل {{ $offlinePayment->order->orderItems->first()->bundle->title }}
                                                    @endif
                                                </span>
                                            </td>

                                            <td class="text-center align-middle">
                                                @if (!empty($offlinePayment->attachment))
                                                    <a href="{{ $offlinePayment->getAttachmentPath() }}" target="_blank"
                                                        class="text-primary">
                                                        @if (pathinfo($offlinePayment->attachment, PATHINFO_EXTENSION) != 'pdf')
                                                            <img src="{{ $offlinePayment->getAttachmentPath() }}"
                                                                alt="offlinePayment_attachment" width="100px" style="max-height:100px">
                                                        @else
                                                            pdf ملف <i class="fas fa-file font-20"></i>
                                                        @endif
                                                    </a>
                                                @else
                                                    ---
                                                @endif
                                            </td>

                                            <td class="text-center align-middle">
                                                @switch($offlinePayment->status)
                                                    @case(\App\Models\OfflinePayment::$waiting)
                                                        <span class="text-warning">{{ trans('public.waiting') }}</span>
                                                    @break

                                                    @case(\App\Models\OfflinePayment::$approved)
                                                        <span class="text-primary">{{ trans('financial.approved') }}</span>
                                                    @break
                                                    @case('canceled')
                                                        <span class="text-primary">ملغي</span>
                                                    @break

                                                    @case(\App\Models\OfflinePayment::$reject)
                                                        <span class="text-danger">{{ trans('public.rejected') }}</span>
                                                        @include('admin.includes.message_button', [
                                                            'url' => '#',
                                                            'btnClass' => 'd-flex align-items-center mt-1',
                                                            'btnText' =>
                                                                '<span class="ml-2">' . ' سبب الرفض</span>',
                                                            'hideDefaultClass' => true,
                                                            'deleteConfirmMsg' => 'هذا سبب الرفض',
                                                            'message' => $offlinePayment->message,
                                                            'id' => $offlinePayment->id,
                                                        ])
                                                    @break
                                                @endswitch
                                            </td>
                                            <td class="text-center align-middle">
                                                @if ($offlinePayment->status != 'approved' && $offlinePayment->status != 'canceled')
                                                    <div class="d-flex justify-content-between">


                                                        @include(
                                                            'web.default.panel.financial.offline_payments.edit',
                                                            [
                                                                'url' =>
                                                                    '/panel/financial/offline-payments/' .
                                                                    $offlinePayment->id .
                                                                    '/update',
                                                                'btnClass' =>
                                                                    'btn btn-primary d-flex align-items-center btn-sm mt-1 px-0',
                                                                'btnText' =>
                                                                   '<span class="ml-2"> اعادة ارسال</span>',
                                                                'hideDefaultClass' => true,
                                                                'id' => $offlinePayment->id,
                                                                'payment' => $offlinePayment,
                                                                'offlineBanks' => $offlineBanks,
                                                            ]
                                                        )


                                                        <a href="/panel/financial/offline-payments/{{ $offlinePayment->id }}/cancel"
                                                            data-item-id="1" data-confirm="نعم إلغاء الطلب"
                                                            class="delete-action btn btn-danger d-flex align-items-center btn-sm mt-1 mr-2" style="width:100px">إلغاء الطلب</a>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    @else
        @include(getTemplate() . '.includes.no-result', [
            'file_name' => 'offline.png',
            'title' => trans('financial.offline_no_result'),
            'hint' => nl2br(trans('financial.offline_no_result_hint')),
        ])
    @endif
@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>

    <script src="/assets/default/js/panel/financial/account.min.js"></script>

    <script>
        (function($) {
            "use strict";

            @if (session()->has('sweetalert'))
                Swal.fire({
                    icon: "{{ session()->get('sweetalert')['status'] ?? 'success' }}",
                    html: '<h3 class="font-20 text-center text-dark-blue py-25">{{ session()->get('sweetalert')['msg'] ?? '' }}</h3>',
                    showConfirmButton: false,
                    width: '25rem',
                });
            @endif
        })(jQuery)
    </script>
@endpush
