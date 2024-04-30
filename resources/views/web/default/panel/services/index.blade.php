@extends(getTemplate() . '.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
    <style>
        .service-card svg{
            width: 40px; !important;
            height: 40px; !important;
            fill: var(--secondary);
        }

        /* .module-box:hover{
            background-color: var(--secondary) !important;

        }
        /* .module-box:hover a{
            background-color: var(--secondary);
        } */

        .module-box:hover .service-card svg{
            fill: var(--primary);
        } */


    </style>
@endpush

@section('content')

  @include('web.default.panel.services.includes.progress')

  <section class="row p-20">
    <div class="col-12 col-lg-4 mt-35 ">
        <div class="module-box dashboard-stats rounded-sm panel-shadow py-30 d-flex align-items-center justify-content-center mt-0">

            <div class="d-flex flex-column service-card" style="align-items: center;">
                @include('web.default.panel.includes.sidebar_icons.requests')

                <span class="font-16 text-gray font-weight-500 text-center pb-10">لطلب شهادة ACP</span>
                <p>
                    تكلف 120 ريال سعودي
                </p>
                <a target="_blank" rel="noopener noreferrer" class="btn btn-primary mt-10" style=""
                    href="https://support.anasacademy.uk/">
                    لتقديم طلب اضغط هنا
                </a>
                <a target="_blank" rel="noopener noreferrer" class="btn btn-primary mt-10" style=""
                    href="https://support.anasacademy.uk/search">
                    لمتابعة طلب سابق اضغط هنا </a>
            </div>

        </div>

    </div>
    <div class="col-12 col-lg-4 mt-35 ">
        <div class="module-box dashboard-stats rounded-sm panel-shadow py-30 d-flex align-items-center justify-content-center mt-0">

            <div class="d-flex flex-column service-card" style="align-items: center; fill: gray">
                @include('web.default.panel.includes.sidebar_icons.requests')

                <span class="font-16 text-gray font-weight-500 text-center pb-10">التحويل من دبلومة لأخري</span>
                <p>
                    تكلف 120 ريال سعودي
                </p>
                <a target="_blank" rel="noopener noreferrer" class="btn btn-primary mt-10" style=""
                    href="https://support.anasacademy.uk/">
                    لتقديم طلب اضغط هنا
                </a>
                <a target="_blank" rel="noopener noreferrer" class="btn btn-primary mt-10" style=""
                    href="https://support.anasacademy.uk/search">
                    لمتابعة طلب سابق اضغط هنا </a>
            </div>

        </div>

    </div>
    <div class="col-12 col-lg-4 mt-35 ">
        <div class="module-box dashboard-stats rounded-sm panel-shadow py-30 d-flex align-items-center justify-content-center mt-0">

            <div class="d-flex flex-column service-card" style="align-items: center; fill: gray">
                @include('web.default.panel.includes.sidebar_icons.requests')

                <span class="font-16 text-gray font-weight-500 text-center pb-10">استرداد ثمن دبلومة</span>
                <p>
                   هذة الخدمة مجانية
                </p>
                <a target="_blank" rel="noopener noreferrer" class="btn btn-primary mt-10" style=""
                    href="https://support.anasacademy.uk/">
                    لتقديم طلب اضغط هنا
                </a>
                <a target="_blank" rel="noopener noreferrer" class="btn btn-primary mt-10" style=""
                    href="https://support.anasacademy.uk/search">
                    لمتابعة طلب سابق اضغط هنا </a>
            </div>

        </div>

    </div>
    <div class="col-12 col-lg-4 mt-35 ">
        <div class="module-box dashboard-stats rounded-sm panel-shadow py-30 d-flex align-items-center justify-content-center mt-0">

            <div class="d-flex flex-column service-card" style="align-items: center; fill: gray">
                @include('web.default.panel.includes.sidebar_icons.requests')

                <span class="font-16 text-gray font-weight-500 text-center pb-10">تأجيل الإلتحاق بدبلومة لوقت لاحق</span>
                <p>
                    تكلف 120 ريال سعودي
                </p>
                <a target="_blank" rel="noopener noreferrer" class="btn btn-primary mt-10" style=""
                    href="https://support.anasacademy.uk/">
                    لتقديم طلب اضغط هنا
                </a>
                <a target="_blank" rel="noopener noreferrer" class="btn btn-primary mt-10" style=""
                    href="https://support.anasacademy.uk/search">
                    لمتابعة طلب سابق اضغط هنا </a>
            </div>

        </div>

    </div>
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
