@extends(getTemplate() . '.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
@endpush

<style>
    .installment-card {
        background-color: #FBFBFB !important;
    }

    .discount {
        min-height: 17px;
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
                    class="bg-white position-relative col-xl-9 col-12 justify-content-center align-items-center rounded-sm mb-80 py-35 px-0">
                    <h2 class="mb-25 col-12">
                        {{ clean($bundleData['bundle']->bundle->title, 't') }}</h2>


                    @if (empty($bundleData['bundle']->studentRequirement))
                        @if ($bundleData['bundle']->status == 'pending')
                            <div class="w-100 text-center">
                                <p class="alert alert-info text-center mx-30">
                                    طلبك لحجز مقعد دراسي تحت المراجعه من قبل الإدارة المالية يرجي الانتظار حتي يتم مراجعته
                                </p>
                            </div>
                        @elseif ($bundleData['bundle']->status == 'rejected')
                            <div class="w-100 text-center">
                                <p class="alert alert-danger text-center text-white mx-30">
                                    طلبك لحجز مقعد دراسي تم رفضه من قبل الإدارة المالية لمعرفة السبب اضغط هنا
                                </p>
                                <a href="/panel/financial/offline-payments"
                                    class="btn btn-success p-5 mt-20 bg-secondary">للذهاب لمتابعة طلبك اضغط
                                    هنا</a>
                            </div>
                        @else
                            @if ($bundleData['bundle']->upload_later == true)
                                @include('web.default.panel.requirements.payment_card', [
                                    'bundleData' => $bundleData,
                                ])
                            @elseif ($bundleData['bundle']->bundle->early_enroll)
                                <div class="w-100 text-center">
                                    <p class="alert alert-info text-center mx-30">
                                        يرجى ملاحظة أن التسجيل الرسمي سيبدأ في شهر يوليو المقبل.
                                        <br> بمجرد فتح التسجيل، ستتمكن من استكمال رفع المتطلبات اللازمة وإتمام إجراءات
                                        التسجيل.
                                    </p>
                                </div>
                            @else
                                <div class="w-100 text-center">
                                    <p class="alert alert-info text-center mx-30">
                                        لم يتم رفع متطلبات القبول بعد ، يرجي الضعط علي الزر للذهاب لصفحة متطلبات القبول
                                    </p>
                                    <a href="/panel/bundles/{{ $bundleData['bundle']->id }}/requirements"
                                        class="btn btn-success p-5 mt-20 bg-secondary">للذهاب لرفع ملفات متطلبات القبول اضغط
                                        هنا</a>
                                </div>
                            @endif
                        @endif
                    @else
                        @if ($bundleData['bundle']->studentRequirement->status == 'pending')
                            <div class="w-100 text-center">
                                <p class="alert alert-info text-center mx-30">
                                    لقد تم بالفعل رفع متطلبات القبول يرجي الانتظار حتي يتم مراجعتها
                                </p>
                            </div>
                        @elseif ($bundleData['bundle']->studentRequirement->status == 'approved')
                            @include('web.default.panel.requirements.payment_card', [
                                'bundleData' => $bundleData,
                            ])
                        @elseif ($bundleData['bundle']->studentRequirement->status == 'rejected')
                            <div class="w-100 text-center mx-30">
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
                <p class="alert alert-info text-center mx-30">
                    لم يتم التسجيل في اي دبلومه بعد
                </p>
                <a href="{{ auth()->user()->student ? '/panel/newEnrollment' : '/apply' }}"
                    class="btn bg-secondary text-white p-5 mt-20">للتسجيل اضغط علي هذا اللينك</a>
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