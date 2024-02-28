@extends(getTemplate() . '.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
@endpush

@section('content')
    @include('web.default.panel.requirements.requirements_includes.progress')


    @if (Session::has('success'))
        <div class="container d-flex justify-content-center mt-80">
            <p class="alert alert-success w-75 text-center"> {{ Session::get('success') }} </p>
        </div>
    @else
        @if (!empty($requirementUploaded) and $requirementUploaded == true)
            @if ($requirementApproved == "approved")
                @if ($currentStep == 1)
                    <div class="container d-flex justify-content-center mt-80 flex-column align-items-center">
                        <p class="alert alert-success w-75 text-center">
                            لقد تم بالفعل رفع متطلبات القبول وتم الموافقة عليها يرجي الذهاب للخطوة التاليه للدفع
                        </p>

                        <a href="/bundles/{{$bundle->slug}}" class="btn btn-primary p-5 mt-20 w-25">للذهاب للدفع رسوم البرنامج اضغط هنا</a>
                    </div>
                @elseif ($currentStep == 2)
                    @include('web.default.panel.requirements.requirements_includes.financial')
                @endif
            @elseif($requirementApproved == "pending")
                <div class="container d-flex justify-content-center mt-80">
                    <p class="alert alert-info w-75 text-center">
                        لقد تم بالفعل رفع متطلبات القبول يرجي الانتظار حتي يتم مراجعتها
                    </p>
                </div>
            @elseif($requirementApproved == "rejected")
                <div class="container d-flex justify-content-center mt-80">
                    <p class="alert alert-danger w-75 text-center text-white">
                        لقد تم رفض الملفات التي قمت برفعها يرجي مراجعة الميل لمشاهدة السبب ثم ارفع الملفات مرة اخري
                    </p>
                </div>

                @include('web.default.panel.requirements.requirements_includes.basic_information')
            @endif
        @else
            @if (!empty($currentStep) and $currentStep == 1)
                @include('web.default.panel.requirements.requirements_includes.basic_information')
            @endif
        @endif





    @endif
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
