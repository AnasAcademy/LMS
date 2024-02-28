@extends(getTemplate().'.layouts.app')

@section('content')
    @php
        $registerMethod = getGeneralSettings('register_method') ?? 'mobile';
    @endphp


    <div class="container">
        <div class="row login-container">
            <div class="col-12 col-md-6 pl-0">
                <img src="{{ getPageBackgroundSettings('remember_pass') }}" class="img-cover" alt="Login">
            </div>

            <div class="col-12 col-md-6">

                <div class="login-card">
                    <h1 class="font-20 font-weight-bold">{{ trans('auth.forget_password') }}</h1>

                    <form method="post" action="/forget-password" class="mt-35">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        @if($registerMethod == 'mobile')
                            <div class="d-flex align-items-center wizard-custom-radio mb-20">
                                <div class="wizard-custom-radio-item flex-grow-1">
                                    <input type="radio" name="type" value="email" id="emailType" class="" {{ (empty(old('type')) or old('type') == "email") ? 'checked' : '' }}>
                                    <label class="font-12 cursor-pointer px-15 py-10" for="emailType">{{ trans('public.email') }}</label>
                                </div>

                                <div class="wizard-custom-radio-item flex-grow-1">
                                    <input type="radio" name="type" value="mobile" id="mobileType" class="" {{ (old('type') == "mobile") ? 'checked' : '' }}>
                                    <label class="font-12 cursor-pointer px-15 py-10" for="mobileType">{{ trans('public.mobile') }}</label>
                                </div>
                            </div>
                        @endif

                        <div class="js-email-fields form-group {{ (old('type') == "mobile") ? 'd-none' : '' }}">
                            <label class="input-label" for="email">{{ trans('public.email') }}:</label>
                            <input name="email" type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                   value="{{ old('email') }}" aria-describedby="emailHelp">
                            @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        @if($registerMethod == 'mobile')
                            <div class="js-mobile-fields {{ (old('type') == "mobile") ? '' : 'd-none' }}">
                                @include('web.default.auth.register_includes.mobile_field')
                            </div>
                        @endif

                        @if(!empty(getGeneralSecuritySettings('captcha_for_forgot_pass')))
                            @include('web.default.includes.captcha_input')
                        @endif


                        <button type="submit" class="btn btn-primary btn-block mt-20">{{ trans('auth.reset_password') }}</button>
                    </form>

                    <div class="text-center mt-20">
                        <span class="badge badge-circle-gray300 text-secondary d-inline-flex align-items-center justify-content-center">or</span>
                    </div>

                    <div class="text-center mt-20">
                        <span class="text-secondary">
                            <a href="/login" class="text-secondary font-weight-bold">{{ trans('auth.login') }}</a>
                        </span>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/js/parts/forgot_password.min.js"></script>
@endpush
