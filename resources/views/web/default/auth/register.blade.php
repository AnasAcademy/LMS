@extends(getTemplate() . '.auth.auth_layout')
@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
@endpush

@section('content')
    <style>
        .cs-btn {
            background-color: #ED1088 !important;
        }

        .cs-btn:hover {
            background-color: #5F2B80 !important;
        }

        .custom-control-label::after,
        .custom-control-label::before {
            left: initial !important;
            right: -1.5rem !important;
        }

        .iti__country-list {
            position: absolute;
            z-index: 2;
            list-style: none;
            text-align: left;
            padding: 0;
            margin: 0 0 0 -1px;
            box-shadow: 1px 1px 4px rgba(0, 0, 0, .2);
            background-color: #fff;
            border: 1px solid #ccc;
            white-space: nowrap;
            max-height: 200px;
            overflow-y: scroll;
            -webkit-overflow-scrolling: touch;
            left: 0 !important;
            direction: ltr !important;
        }
    </style>
    @php
        $siteGeneralSettings = getGeneralSettings();
    @endphp
    @php
        $registerMethod = getGeneralSettings('register_method') ?? 'mobile';
        $showOtherRegisterMethod = getFeaturesSettings('show_other_register_method') ?? false;
        $showCertificateAdditionalInRegister = getFeaturesSettings('show_certificate_additional_in_register') ?? false;
        $selectRolesDuringRegistration = getFeaturesSettings('select_the_role_during_registration') ?? null;
    @endphp
    <div class="p-md-4 m-md-3">
        <div class="col-6 col-md-6 p-0 mb-5 mt-3 mt-md-auto">
            <img src="{{ $siteGeneralSettings['logo'] ?? '' }}" alt="logo" width="100%" class="">
        </div>

        <h1 class="font-20 font-weight-bold mb-3">
            <svg width="34" height="29" viewBox="0 0 34 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M22 27C22 23.3181 17.5228 20.3333 12 20.3333C6.47715 20.3333 2 23.3181 2 27M32 12L25.3333 18.6667L22 15.3333M12 15.3333C8.3181 15.3333 5.33333 12.3486 5.33333 8.66667C5.33333 4.98477 8.3181 2 12 2C15.6819 2 18.6667 4.98477 18.6667 8.66667C18.6667 12.3486 15.6819 15.3333 12 15.3333Z"
                    stroke="#5E0A83" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            {{ trans('auth.signup') }}
        </h1>

        {{-- show messages --}}
        @if (!empty(session()->has('msg')))
            <div class="alert alert-info alert-dismissible fade show mt-30" role="alert">
                {{ session()->get('msg') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form method="post" action="/register" class="mt-35">

            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            @if (!empty($selectRolesDuringRegistration) and count($selectRolesDuringRegistration))
                <div class="form-group">
                </div>
            @endif
            <div class="form-group">
                <label class="input-label" for="full_name">الأسم الثلاثي *</label>

                <input name="full_name" type="text" value="{{ old('full_name') }}"
                    class="form-control @error('full_name') is-invalid @enderror" placeholder="أدخل الأسم ">
                @error('full_name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>


            @if ($registerMethod == 'mobile')
                @include('web.default.auth.register_includes.mobile_field')

                @if ($showOtherRegisterMethod)
                    @include('web.default.auth.register_includes.email_field', ['optional' => false])
                @endif
            @else
                @include('web.default.auth.register_includes.email_field')

                <div class="form-group">
                    <label class="input-label" for="email">اعد كتابة الإيميل
                        {{ !empty($optional) ? '(' . trans('public.optional') . ')' : '' }}*</label>
                    <input name="email_confirmation" type="text"
                        class="form-control @error('email_confirmation') is-invalid @enderror"
                        value="{{ old('email_confirmation') }}" id="email" aria-describedby="emailHelp">

                    @error('email_confirmation')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                @if ($showOtherRegisterMethod)
                    @include('web.default.auth.register_includes.mobile_field', ['optional' => false])
                @endif
            @endif




            <div class="password-section">

                <div class="form-group  col-12 p-0">
                    <label class="input-label" for="password">{{ trans('auth.password') }}:</label>
                    <input name="password" type="password" class="form-control @error('password') is-invalid @enderror"
                        id="password" aria-describedby="passwordHelp">
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group  col-12 p-0 pr-1 ">
                    <label class="input-label" for="confirm_password">{{ trans('auth.retype_password') }}:</label>
                    <input name="password_confirmation" type="password"
                        class="form-control @error('password_confirmation') is-invalid @enderror" id="confirm_password"
                        aria-describedby="confirmPasswordHelp">
                    @error('password_confirmation')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

            </div>


            {{-- @if (getFeaturesSettings('timezone_in_register'))
                @php
                    $selectedTimezone = getGeneralSettings('default_time_zone');
                @endphp

                <div class="form-group">
                    <label class="input-label">{{ trans('update.timezone') }}</label>
                    <select name="timezone" class="form-control select2" data-allow-clear="false">
                        <option value="" {{ empty($user->timezone) ? 'selected' : '' }} disabled>
                            {{ trans('public.select') }}</option>
                        @foreach (getListOfTimezones() as $timezone)
                            <option value="{{ $timezone }}" @if ($selectedTimezone == $timezone) selected @endif>
                                {{ $timezone }}</option>
                        @endforeach
                    </select>
                    @error('timezone')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            @endif --}}

            @if (!empty($referralSettings) and $referralSettings['status'])
                <div class="form-group ">
                    <label class="input-label" for="referral_code">{{ trans('financial.referral_code') }}:</label>
                    <input name="referral_code" type="text"
                        class="form-control @error('referral_code') is-invalid @enderror" id="referral_code"
                        value="{{ !empty($referralCode) ? $referralCode : old('referral_code') }}"
                        aria-describedby="confirmPasswordHelp">
                    @error('referral_code')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            @endif

            @if (!empty(getGeneralSecuritySettings('captcha_for_register')))
                @include('web.default.includes.captcha_input')
            @endif
            <!--start-->

            {{-- <div class="custom-control custom-checkbox">
                <input type="checkbox" name="term" value="1"
                    {{ (!empty(old('term')) and old('term') == '1') ? 'checked' : '' }}
                    class="custom-control-input @error('term') is-invalid @enderror" id="term">
                <label class="custom-control-label font-14 mr-20" for="term">
                    <p class="term">
                        {{ trans('auth.i_agree_with') }}

                        <a href="pages/terms" target="_blank"
                            class="text-secondary font-weight-bold font-14">{{ trans('auth.terms_and_rules') }}</a>

                    </p>
                </label>

                @error('term')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            @error('term')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror --}}
            <!--end-->

            {{-- application type --}}
            <div class="form-group col-12 col-sm-12">
                <label class="form-label">حدد نوع التقديم<span class="text-danger">*</span></label>
                <select id="typeSelect" name="type" required class="form-control @error('type') is-invalid @enderror"
                    onchange="toggleHiddenType()">
                    <option selected hidden value="">اختر نوع التقديم التي تريد دراستها في
                        اكاديمية انس للفنون </option>
                    <option value="diplomas" @if (old('type') == 'diplomas') selected @endif>
                        دبلومات </option>
                    <option value="courses" @if (old('type') == 'courses') selected @endif>دورات</option>
                </select>

                @error('type')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- course --}}
            <div class="form-group col-12 col-sm-12 d-none">
                <label for="application2" class="form-label" id="all_course">الدورات التدربيه<span
                        class="text-danger">*</span></label>
                <select id="mySelect2" name="webinar_id"
                    class="form-control @error('webinar_id') is-invalid @enderror">
                    <option selected hidden value="">اختر الدورة التدربيه التي تريد دراستها
                        في
                        اكاديمية انس للفنون </option>

                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" @if (old('webinar_id') == $course->id) selected @endif>
                            {{ $course->title }} </option>
                    @endforeach

                </select>

                @error('webinar_id')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- diplomas --}}
            <section class="d-none" id="diplomas_section">

                {{-- diploma --}}
                <div class="form-group col-12 col-sm-12">
                    <label for="application" class="form-label"
                        id="degree">{{ trans('application_form.application') }}<span
                            class="text-danger">*</span></label>
                    <select id="mySelect1" name="category_id"
                        class="form-control @error('category_id') is-invalid @enderror" onchange="toggleHiddenInput()">
                        <option selected hidden value="">اختر الدرجة العلمية التي تريد
                            دراستها في
                            اكاديمية انس للفنون </option>
                        @foreach ($category as $item)
                            <option value="{{ $item->id }}"
                                {{ old('category_id', null) == $item->id ? 'selected' : '' }}>
                                {{ $item->title }} </option>
                        @endforeach
                    </select>

                    @error('category_id')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- specialization --}}
                <div class="form-group col-12 col-sm-12 d-none">
                    <label class="hidden-element" id="hiddenLabel1" for="bundle_id">
                        {{ trans('application_form.specialization') }}<span class="text-danger">*</span>
                    </label>
                    <input type="text" id="bundle_id" name="bundle_id"
                        class="hidden-element form-control @error('bundle_id') is-invalid @enderror"
                        value="{{ old('bundle_id', '') }}">

                    @error('bundle_id')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="d-none font-14 font-weight-bold mb-10 col-12" id="early_enroll" style="color: #5F2B80;">
                    يرجى ملاحظة أن التسجيل الرسمي سيبدأ في شهر يوليو المقبل. بمجرد فتح التسجيل، ستتمكن من
                    استكمال رفع المتطلبات اللازمة وإتمام إجراءات التسجيل.
                </div>


            </section>

            <button type="submit" class="btn btn-primary btn-block font-16 mt-20 py-10 cs-btn">
                الخطوة التالية <i class="fas fa-arrow-left"></i>
            </button>
        </form>

        <div class="ft-text text-center mt-20 mb-35">
            <span class="text-secondary">
                لديك حساب بالفعل ؟

                <br>
                <a href="/login" class="text-secondary font-weight-bold">تسجيل دخول</a>
            </span>
        </div>



    </div>
@endsection
@php
    $bundlesByCategory = [];
    foreach ($category as $item) {
        $bundlesByCategory[$item->id] = $item->bundles;
    }
@endphp
@push('scripts_bottom')
<script src="/assets/default/vendors/select2/select2.min.js"></script>

@endpush

<script>
    function toggleHiddenInput() {
        var bundles = @json($bundlesByCategory);
        var select = document.getElementById("mySelect1");
        var hiddenInput = document.getElementById("bundle_id");
        var hiddenLabel = document.getElementById("hiddenLabel1");
        if (select.value && hiddenLabel && hiddenInput) {

            var categoryId = select.value;
            var categoryBundles = bundles[categoryId];

            if (categoryBundles) {
                var options = categoryBundles.map(function(bundle) {
                    var isSelected = bundle.id == "{{ old('bundle_id', $student->bundle_id ?? null) }}" ?
                        'selected' : '';
                    return `<option value="${bundle.id}" ${isSelected} early_enroll="${bundle.early_enroll}">${bundle.title}</option>`;
                }).join('');

                hiddenInput.outerHTML =
                    '<select id="bundle_id" name="bundle_id"  class="form-control" >' +
                    '<option value="" class="placeholder" selected hidden>اختر التخصص الذي تود دراسته في اكاديمية انس للفنون</option>' +
                    options +
                    '</select>';
                hiddenLabel.style.display = "block";
                hiddenLabel.closest('div').classList.remove('d-none');
            } else {
                hiddenInput.outerHTML =
                    '<select id="bundle_id" name="bundle_id"  class="form-control" >' +
                    '<option value="" class="placeholder" selected hidden >اختر التخصص الذي تود دراسته في اكاديمية انس للفنون</option> </select>';
                hiddenLabel.style.display = "none";
                hiddenLabel.closest('div').classList.add('d-none');
            }
            var selectedOption = select.options[select.selectedIndex];
            var selectedText = selectedOption.textContent;

        } else {
            hiddenInput.outerHTML =
                '<select id="bundle_id" name="bundle_id"  class="form-control" >' +
                '<option value="" class="placeholder" selected hidden >اختر التخصص الذي تود دراسته في اكاديمية انس للفنون</option> </select>';
            hiddenLabel.style.display = "none";
            hiddenLabel.closest('div').classList.add('d-none');
        }
    }
</script>
<script>
    function toggleHiddenType() {
        console.log('toggle');
        var select = document.getElementById("typeSelect");
        var hiddenDiplomaInput = document.getElementById("mySelect1");
        var hiddenDiplomaLabel = document.getElementById("degree");
        var hiddenBundleInput = document.getElementById("bundle_id");
        var hiddenDiplomaLabel1 = document.getElementById("hiddenLabel1");
        let diplomasSection = document.getElementById("diplomas_section");

        var hiddenCourseInput = document.getElementById("mySelect2");
        var hiddenCourseLabel = document.getElementById("all_course");

        if (select) {
            var type = select.value;
            if (type == 'diplomas') {
                diplomasSection.classList.remove('d-none');
                hiddenCourseInput.closest('div').classList.add('d-none');
                resetSelect(hiddenCourseInput);
                toggleHiddenInput();

            } else if (type == 'courses') {
                hiddenCourseInput.closest('div').classList.remove('d-none');
                diplomasSection.classList.add('d-none');


                resetSelect(hiddenDiplomaInput);
                resetSelect(hiddenBundleInput);

            } else {
                diplomasSection.classList.add('d-none');
                console.log(hiddenCourseInput);
                hiddenCourseInput.closest('div').classList.add('d-none');
                resetSelect(hiddenDiplomaInput);
                resetSelect(hiddenBundleInput);
                resetSelect(hiddenCourseInput);
            }

            toggleHiddenInput();
            // coursesToggle();
        }
    }

    toggleHiddenType();

    function resetSelect(selector) {
        selector.selectedIndex = 0; // This sets the first option as selected
    }
</script>

