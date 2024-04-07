@extends(getTemplate() . '.auth.auth_layout')
@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
@endpush

@section('content')

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
        <div class="col-7 col-md-4 p-0 mb-5 mt-3 mt-md-auto">
            <img src="{{ $siteGeneralSettings['logo'] ?? '' }}" alt="logo" width="100%" class="">
        </div>

        <h1 class="font-20 font-weight-bold mb-3"><svg width="34" height="29" viewBox="0 0 34 29" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M22 27C22 23.3181 17.5228 20.3333 12 20.3333C6.47715 20.3333 2 23.3181 2 27M32 12L25.3333 18.6667L22 15.3333M12 15.3333C8.3181 15.3333 5.33333 12.3486 5.33333 8.66667C5.33333 4.98477 8.3181 2 12 2C15.6819 2 18.6667 4.98477 18.6667 8.66667C18.6667 12.3486 15.6819 15.3333 12 15.3333Z"
                    stroke="#5E0A83" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            {{ trans('auth.signup') }}</h1>

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
                    @include('web.default.auth.register_includes.email_field', ['optional' => true])
                @endif
            @else
                @include('web.default.auth.register_includes.email_field')

                @if ($showOtherRegisterMethod)
                    @include('web.default.auth.register_includes.mobile_field', ['optional' => true])
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


            @if (getFeaturesSettings('timezone_in_register'))
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
            @endif

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
            <div class="custom-control custom-checkbox">
                <input type="checkbox" name="term" value="1"
                    {{ (!empty(old('term')) and old('term') == '1') ? 'checked' : '' }}
                    class="custom-control-input @error('term') is-invalid @enderror" id="term">
                <label class="custom-control-label font-14" for="term">
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
            @enderror
            <!--end-->

            <button type="submit" class="btn btn-primary btn-block mt-20">{{ trans('auth.signup') }}</button>
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
@push('scripts_bottom')
    <script src="/assets/default/vendors/select2/select2.min.js"></script>
@endpush
