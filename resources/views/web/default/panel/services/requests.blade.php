@extends(getTemplate() . '.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
@endpush

@section('content')


    @if (Session::has('success'))
        <div class="container d-flex justify-content-center mt-80">
            <p class="alert alert-success w-75 text-center"> {{ Session::get('success') }} </p>
        </div>
    @endif
    @if (Session::has('error'))
        <div class="container d-flex justify-content-center mt-80">
            <p class="alert alert-danger w-75 text-center"> {{ Session::get('error') }} </p>
        </div>
    @endif



    @if ($services->count() > 0)
        <section class="mt-40">
            @include('web.default.panel.services.includes.progress', [
                'title' => 'طلبات الخدمات الإلكترونية',
            ])

            <div class="panel-section-card py-20 px-25 mt-20">
                <div class="row">
                    <div class="col-12 ">
                        <div class="table-responsive">
                            <table class="table text-center custom-table">
                                <thead>
                                    <tr>
                                        <th class="text-center">{{ 'Index' }}</th>
                                        <th class="text-center">{{ 'اسم الخدمة' }}</th>
                                        <th class="text-center">{{ '  ثمن الخدمة (ر.س) ' }}</th>
                                        <th class="text-center">{{ 'حالة الطلب' }}</th>
                                        <th class="text-center">{{ 'محتوي الطلب' }}</th>
                                        <th class="text-center">{{ 'تاريخ الطلب ' }}</th>
                                        <th class="text-center">{{ 'الإجراءات' }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($services as $service)
                                        <tr @if ($service->status == 'canceled') class="bg-light" style="opacity: 0.5" @endif>
                                            <td class="text-center">
                                                <span>{{ $loop->iteration }}</span>
                                            </td>

                                            <td class="text-center">
                                                <span>{{ $service->title }}</span>
                                            </td>

                                            <td class="text-center align-middle">
                                                <span
                                                    class="font-16 font-weight-bold text-primary">{{ handlePrice($service->price, false) }}</span>
                                            </td>

                                            <td class="text-center align-middle">
                                                @switch($service->pivot->status)
                                                    @case('pending')
                                                        <span class="text-warning">{{ trans('public.waiting') }}</span>
                                                    @break

                                                    @case('approved')
                                                        <span class="text-secondary">{{ trans('financial.approved') }}</span>
                                                    @break

                                                    @case('canceled')
                                                        <span class="text-primary">ملغي</span>
                                                    @break

                                                    @case('rejected')
                                                        <span class="text-danger">{{ trans('public.rejected') }}</span>
                                                        @include('admin.includes.message_button', [
                                                            'url' => '#',
                                                            'btnClass' => 'd-flex align-items-center mt-1',
                                                            'btnText' =>
                                                                '<span class="ml-2">' . ' سبب الرفض</span>',
                                                            'hideDefaultClass' => true,
                                                            'deleteConfirmMsg' => 'هذا سبب الرفض',
                                                            'message' => $service->pivot->message,
                                                            'id' => $service->pivot->id,
                                                        ])
                                                    @break
                                                @endswitch
                                            </td>



                                            <td class="text-center">
                                                @include('admin.services.requestContentMessage', [
                                                    'url' => '#',
                                                    'btnClass' =>
                                                        'd-flex align-items-center justify-content-center mt-1 text-primary',
                                                    'btnText' => '<span class="ml-2">' . ' محتوي الطلب</span>',
                                                    'hideDefaultClass' => true,
                                                    'deleteConfirmMsg' => 'test',
                                                    'message' => $service->pivot->content,
                                                    'id' => $service->pivot->id,
                                                ])
                                            </td>

                                            <td class="font-12">
                                                {{ Carbon\Carbon::parse($service->pivot->created_at)->translatedFormat(handleDateAndTimeFormat('Y M j | H:i')) }}
                                            </td>

                                            <td>
                                                @if (!empty($service->pivot->bundleTransform && $service->pivot->bundleTransform->type=="pay" && $service->pivot->status=="approved" && $service->pivot->bundleTransform->status!="paid"))
                                                <a href="/panel/bundletransform/{{ $service->pivot->bundleTransform->id}}/pay">دفع الفرق و إتمام التحويل</a>
                                                @endif
                                                @if (!empty($service->pivot->bundleTransform && $service->pivot->bundleTransform->type=="refund" && $service->pivot->status=="approved" && $service->pivot->bundleTransform->status!="paid"))
                                                <a href="/panel/bundletransform/{{ $service->pivot->bundleTransform->id}}/refund">استيرداد الفرق و إتمام التحويل</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center text-center">
                    {{ $services->links() }}
                </div>
            </div>

        </section>
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
