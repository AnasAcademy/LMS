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
            @include('web.default.panel.services.includes.service_card', ['title'=> 'طلب شهادة CPR', 'description'=>'', 'price'=>100, 'newRequestUrl'=> 'gg', 'reviewOldRequestUrl'=> ''])
    </div>
    <div class="col-12 col-lg-4 mt-35 ">
            @include('web.default.panel.services.includes.service_card', ['title'=> 'التحويل إلى برنامج آخر', 'price'=>120, 'newRequestUrl'=> 'gg', 'reviewOldRequestUrl'=> ''])
    </div>
    <div class="col-12 col-lg-4 mt-35 ">
            @include('web.default.panel.services.includes.service_card', ['title'=> 'استرداد رسوم البرنامج', 'description'=> '', 'price'=>0, 'newRequestUrl'=> 'gg', 'reviewOldRequestUrl'=> ''])
    </div>
    <div class="col-12 col-lg-4 mt-35 ">
            @include('web.default.panel.services.includes.service_card', ['title'=> 'تجميد الإشتراك بالبرنامج', 'description'=> '', 'price'=>200, 'newRequestUrl'=> 'gg', 'reviewOldRequestUrl'=> ''])
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
