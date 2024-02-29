@extends(getTemplate() . '.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
@endpush

<style>
.requirement-card{
    /* width: 100%;
    background-color: white; */
    padding: 70px;
    box-shadow: 0px 0px 10px 0px #00000073;
}
.requirement-head{
    top: -15px;
    right: 30px;
    max-width: 85%;
}
</style>

@section('content')

@include('web.default.panel.requirements.requirements_includes.progress')

    <section class="container d-flex mt-80 flex-wrap flex-md-nowrap">

        @if (!empty($studentBundles))
            @foreach ($studentBundles as $studentBundle)
                <div class="requirement-card bg-white w-100 position-relative d-flex justify-content-center align-items-center rounded-sm mb-80 ml-50">
                    <h2 class="position-absolute bg-white p-5 requirement-head">
                    متطلبات القبول ل {{ clean($studentBundle->bundle->title, 't') }}</h2>
                    @if (empty($studentBundle->studentRequirement))
                        <div class="w-100 text-center">
                            <p class="alert alert-info text-center">
                                لم يتم رفع متطلبات القبول بعد ، يرجي الضعط علي الزر للذهاب لصفحة متطلبات القبول
                            </p>
                            <a href="/panel/bundles/{{ $studentBundle->id}}/requirements"
                                class="btn btn-success p-5 mt-20 bg-secondary">للذهاب لرفع ملفات متطلبات القبول اضغط هنا</a>
                        </div>
                    @else
                        @if ($studentBundle->studentRequirement->status == 'pending')
                            <div  class="w-100 text-center">
                                <p class="alert alert-info text-center">
                                    لقد تم بالفعل رفع متطلبات القبول يرجي الانتظار حتي يتم مراجعتها
                                </p>
                            </div>
                            </div>
                        @elseif ($studentBundle->studentRequirement->status == 'approved')
                            <div  class="w-100 text-center">
                                <p class="alert alert-success text-center">
                                    لقد تم بالفعل رفع متطلبات القبول وتم الموافقة عليها يرجي الذهاب للخطوة التاليه للدفع
                                </p>
                                <a href="/bundles/{{ $studentBundle->bundle->slug }}"
                                    class="btn btn-primary p-5 mt-20">للذهاب للدفع رسوم البرنامج اضغط هنا</a>
                            </div>
                        @elseif ($studentBundle->studentRequirement->status == 'rejected')
                            <div  class="w-100 text-center">
                                <p class="alert alert-danger text-center text-white">
                                    لقد تم رفض الملفات التي قمت برفعها يرجي مراجعة الميل لمشاهدة السبب ثم ارفع الملفات مرة
                                    اخري
                                </p>
                                <a href="/panel/bundles/{{ $studentBundle->id }}/requirements"
                                    class="btn btn-primary p-5 mt-20">للذهاب لرفع الملفات مرة اخري اضغط هنا</a>
                            </div>
                        @endif
                    @endif
                </div>
            @endforeach
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
