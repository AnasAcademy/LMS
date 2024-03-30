@extends(getTemplate().'.layouts.app')

@section('content')
<style>

    .login-container {
      
        display: flex;
        margin-right: -15px;
        margin-left: -15px;
        flex-wrap: wrap;
        align-content: center;
        justify-content: center;
        align-items: center;
        margin: 120px 0 70px;
        border-radius: 0px;
        border: 0px;
    }
    .login-card{
        border-radius: 16px;
        border: 1px solid #fff;
        background-color:#f6f7f8;
        
    }
    .login-card h1{
        text-align:center;
        color:#5E0A83;
        font-size:30px;
    }
    .forgetpw a,.registertext a{
        color:#56137E;
        font-weight:400;
    }
    /*.hero {*/
    /*    width: 100%;*/
    /*    height: 80vh;*/
        /* background-color: #ED1088; */
    /*    background-image: linear-gradient(90deg, #5E0A83 19%, #F70387 100%);*/
    /*}*/
</style>
<!--<header class="hero">-->
<!--<section class=" container-fluid"></section>-->
<!--</header>-->
    <div class="container">
        @if(!empty(session()->has('msg')))
            <div class="alert alert-info alert-dismissible fade show mt-30" role="alert">
                {{ session()->get('msg') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row login-container">

            <!--<div class="col-12 col-md-6 pl-0">-->
            <!--    <img src="{{ getPageBackgroundSettings('login') }}" class="img-cover" alt="Login">-->
            <!--</div>-->
            <div class="col-12 col-md-6">
                <div class="login-card">
                    <h1 class="font-20 font-weight-bold"><svg width="34" height="29" viewBox="0 0 34 29" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M22 27C22 23.3181 17.5228 20.3333 12 20.3333C6.47715 20.3333 2 23.3181 2 27M32 12L25.3333 18.6667L22 15.3333M12 15.3333C8.3181 15.3333 5.33333 12.3486 5.33333 8.66667C5.33333 4.98477 8.3181 2 12 2C15.6819 2 18.6667 4.98477 18.6667 8.66667C18.6667 12.3486 15.6819 15.3333 12 15.3333Z" stroke="#5E0A83" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
{{ trans('auth.login_h1') }}</h1>

                    <form method="Post" action="/login" class="mt-35">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <!--<label class="input-label" for="username">{{ trans('auth.email_or_mobile') }}:</label>-->
                            <label class="input-label" for="username">البريد الإلكتروني </label>

                            <input name="username" type="text" class="form-control @error('username') is-invalid @enderror" id="username"
                                   value="{{ old('username') }}" aria-describedby="emailHelp">
                            @error('username')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="input-label" for="password">{{ trans('auth.password') }}</label>
                            <input name="password" type="password" class="form-control @error('password')  is-invalid @enderror" id="password" aria-describedby="passwordHelp">

                            @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    <div class="text-left forgetpw">
                        <!--<a href="/forget-password" target="_blank">{{ trans('auth.forget_your_password') }}</a>-->
                        <a href="/forget-password" target="_blank">نسيت كلمة المرور ؟</a>

                    </div>
                        @if(!empty(getGeneralSecuritySettings('captcha_for_login')))
                            @include('web.default.includes.captcha_input')
                        @endif

                        <button type="submit" class="btn btn-primary btn-block mt-20">{{ trans('auth.login') }}</button>
                    </form>

                    @if(session()->has('login_failed_active_session'))
                        <div class="d-flex align-items-center mt-20 p-15 danger-transparent-alert ">
                            <div class="danger-transparent-alert__icon d-flex align-items-center justify-content-center">
                                <i data-feather="alert-octagon" width="18" height="18" class=""></i>
                            </div>
                            <div class="ml-10">
                                <div class="font-14 font-weight-bold ">{{ session()->get('login_failed_active_session')['title'] }}</div>
                                <div class="font-12 ">{{ session()->get('login_failed_active_session')['msg'] }}</div>
                            </div>
                        </div>
                    @endif

                    <!-- <div class="text-center mt-20">
                        <span class="badge badge-circle-gray300 text-secondary d-inline-flex align-items-center justify-content-center">{{ trans('auth.or') }}</span>
                    </div> -->

                    <!-- @if(!empty(getFeaturesSettings('show_google_login_button')))
                        <a href="/google" target="_blank" class="social-login mt-20 p-10 text-center d-flex align-items-center justify-content-center">
                            <img src="/assets/default/img/auth/google.svg" class="mr-auto" alt=" google svg"/>
                            <span class="flex-grow-1">{{ trans('auth.google_login') }}</span>
                        </a>
                    @endif -->

                    <!-- @if(!empty(getFeaturesSettings('show_facebook_login_button')))
                        <a href="{{url('/facebook/redirect')}}" target="_blank" class="social-login mt-20 p-10 text-center d-flex align-items-center justify-content-center ">
                            <img src="/assets/default/img/auth/facebook.svg" class="mr-auto" alt="facebook svg"/>
                            <span class="flex-grow-1">{{ trans('auth.facebook_login') }}</span>
                        </a>
                    @endif -->

                    <!--<div class="mt-30 text-center">-->
                        <!--<a href="/forget-password" target="_blank">{{ trans('auth.forget_your_password') }}</a>-->
                    <!--    <a href="/forget-password" target="_blank">نسيت كلمة المرور؟</a>-->

                    <!--</div>-->

                    <!--<div class="mt-20 text-center">-->
                    <!--    <span>{{ trans('auth.dont_have_account') }}</span>-->
                    <!--    <a href="/register" class="text-secondary font-weight-bold">{{ trans('auth.signup') }}</a>-->
                    <!--</div>-->
                 <div class="mt-20 text-center registertext">
                        <span>ليس لديك حساب ؟</span>
                        <br>
                        <a href="/register" class="text-secondary font-weight-bold">{{ trans('auth.signup') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
