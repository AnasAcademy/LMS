@extends(getTemplate() . '.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/chartjs/chart.min.css" />
    <link rel="stylesheet" href="/assets/default/vendors/apexcharts/apexcharts.css" />
@endpush
<style>
    .dashboard-banner-container {
        margin-top: 0px !important;
    }

    .module-box {
        flex-wrap: wrap;
        align-content: center;
        justify-content: center;
        align-items: center;
        gap: 20px;
    }
</style>
@section('content')
    <section class="dashboard">
        <div class="row p-10">
            <div class="d-block align-items-start align-items-md-center justify-content-between flex-column flex-md-row">
                <h1 class="section-title">{{ trans('panel.dashboard') }}</h1>

                @if (!$authUser->isUser())
                    <div
                        class="d-flex align-items-center flex-row-reverse flex-md-row justify-content-start justify-content-md-center mt-20 mt-md-0">
                        <label class="mb-0 mr-10 cursor-pointer text-gray font-14 font-weight-500"
                            for="iNotAvailable">{{ trans('panel.i_not_available') }}</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="disabled" @if ($authUser->offline) checked @endif
                                class="custom-control-input" id="iNotAvailable">
                            <label class="custom-control-label" for="iNotAvailable"></label>
                        </div>
                    </div>
                @endif
            </div>
        </div>




        <div class="row p-20">

            <div class="col-12 col-lg-5 mt-35">

                @if (!$authUser->financial_approval and !$authUser->isUser())
                    <div
                        class="p-15 mt-20 p-lg-20 not-verified-alert font-weight-500 text-dark-blue rounded-sm panel-shadow">
                        {{ trans('panel.not_verified_alert') }}
                        <a href="/panel/setting/step/7"
                            class="text-decoration-underline">{{ trans('panel.this_link') }}</a>.
                    </div>
                @endif

                <div class="bg-white dashboard-banner-container position-relative p-40 rounded-sm">
                    <h2 class="font-30 text-primary line-height-1">
                        <span class="d-block">{{ trans('panel.hi') }} {{ $authUser->full_name }}</span>
                        <!--<span class="font-16 text-secondary font-weight-bold">{{ trans('panel.have_event', ['count' => !empty($unReadNotifications) ? count($unReadNotifications) : 0]) }}</span>-->
                    </h2>
                    <ul class="mt-15 unread-notification-lists">
                        <h4>بياناتك الاكاديمية</h4>
                        <li class="mt-1 text-gray font-16 font-weight-bold text-left">كود الطالب :
                            {{ $authUser->user_code }}</li>
                        <li class="mt-1 text-gray font-16 font-weight-bold text-left"> البريد الاكاديمي :
                            {{ $authUser->user_code }}@anasacademy.uk</li>
                        <li class="mt-1 text-gray font-16 font-weight-bold text-left"> البرنامج الدراسي :

                            @if (!empty($bundleSales) and !$bundleSales->isEmpty())
                                @foreach ($bundleSales as $bundleSale)
                                    {{ $bundleSale->bundle->title }}
                                    @if ($loop->index + 1 < $bundleSales->count())
                                        و
                                    @endif
                                @endforeach
                            @endif
                        </li>


                    </ul>
                </div>

            </div>
            <div class="col-12 col-lg-3 mt-35">
                <div class="bg-white account-balance rounded-sm p-25">
                    <div class="text-center">
                        {{--  @include('web.default.panel.includes.sidebar_icons.financial') --}}
                        <svg width="63" viewBox="0 0 63 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M62.4835 35.1357C62.4188 34.5078 62.2896 33.8734 62.0208 33.2815C61.6058 32.3658 61.2043 31.4436 60.7893 30.5247C60.2416 29.3049 59.7143 28.0786 59.1258 26.8785C58.5985 25.8026 58.1562 24.6973 57.6323 23.6181C56.9724 22.261 56.4961 20.8188 55.8225 19.4682C55.2544 18.3237 54.8121 17.1333 54.2236 15.9953C53.6215 14.8377 53.1452 13.6081 52.5975 12.4145C52.1757 11.4923 51.8015 10.5472 51.3286 9.65116C50.8965 8.83689 50.4543 8.01607 50.1039 7.17564C49.7229 6.26326 49.182 5.45879 48.5662 4.73609C47.4708 3.45745 46.2019 2.36848 44.5962 1.6098C43.2933 0.991732 41.9325 0.59604 40.5513 0.344236C39.0953 0.0793519 37.5849 0.0760817 36.0982 0.027029C34.5606 -0.0220237 33.0195 0.0106781 31.4818 0.0106781C28.6446 0.0106781 25.804 -0.0252938 22.9702 0.154566C21.9837 0.216699 20.9971 0.170917 20.0072 0.170917C19.2622 0.170917 18.5103 0.180727 17.7619 0.180727C16.8842 0.180727 16.0031 0.197078 15.1254 0.206889C14.1627 0.21997 13.2 0.112054 12.2372 0.295184C11.1826 0.501205 10.4648 1.22718 10.4206 2.25402C10.39 2.95711 10.5397 3.62095 10.8118 4.25537L10.8492 4.23902L16.8366 17.7187V17.7285C16.9148 17.8757 16.9931 18.0195 17.0713 18.1667C17.5136 18.9777 17.8402 19.8443 18.2484 20.6717C18.4015 20.9791 18.6872 21.3453 18.626 21.6135L21.5653 27.8693L21.5108 27.8922C21.5857 28.0426 21.6605 28.1963 21.7388 28.3468C22.5212 29.8641 23.1744 31.4273 24.2834 32.7844C25.5829 34.3704 27.1002 35.6491 29.0154 36.4764C29.9918 36.9016 31.0124 37.3038 32.0738 37.3725C33.9142 37.4902 35.7648 37.4477 37.6121 37.4608C38.8232 37.4706 40.0376 37.4542 41.2487 37.4575C42.4224 37.4575 43.5926 37.4804 44.7697 37.4771C45.6338 37.4771 46.4945 37.451 47.362 37.4477C47.4878 37.4477 47.6001 37.464 47.6885 37.5H50.5427C50.5904 37.4837 50.655 37.4738 50.7332 37.4738C52.3492 37.4738 53.9617 37.4509 55.5742 37.4444C57.1799 37.4346 58.7856 37.4575 60.3913 37.415C61.6194 37.3823 62.6093 36.3064 62.4903 35.1357H62.4835Z"
                                fill="#F70387" fill-opacity="0.5" />
                            <g clip-path="url(#clip0_10_1564)">
                                <path
                                    d="M23.9077 8.34321C27.3259 8.34321 30.7441 8.31652 34.1623 8.35163C37.2891 8.38394 40.2215 9.08763 42.7449 10.981C45.5409 13.0794 46.9984 15.9082 47.4857 19.2567C47.6554 20.4253 47.5858 21.5996 47.593 22.771C47.5945 23.0828 47.683 23.2893 47.9687 23.4592C49.1622 24.1713 49.8424 25.2346 49.9134 26.5689C50.0091 28.3851 50.0628 30.211 49.8757 32.0257C49.751 33.2238 49.1129 34.1705 48.0615 34.818C47.7091 35.0343 47.58 35.2745 47.5945 35.6706C47.667 37.6735 47.593 39.668 46.9056 41.5866C45.2059 46.3271 41.708 48.9831 36.6553 49.8006C35.6561 49.9621 34.6438 50 33.6301 50C26.9561 49.9972 20.2821 50.0057 13.6081 49.9944C10.9802 49.9902 8.46261 49.5056 6.17702 48.1881C2.71095 46.188 0.834332 43.1626 0.209278 39.3576C0.0308981 38.2845 -0.0010072 37.1988 0.000443043 36.1116C0.00769425 31.2069 -0.00970865 26.3021 0.0091445 21.3973C0.0192962 18.8887 0.532682 16.4869 1.87561 14.3056C3.96831 10.9094 7.14144 9.11572 11.1151 8.53704C12.2796 8.36708 13.4529 8.33618 14.629 8.3404C17.7224 8.35023 20.8143 8.34321 23.9077 8.34321ZM23.8439 46.4872V46.49C27.0997 46.49 30.3569 46.49 33.6127 46.49C34.39 46.49 35.1659 46.4928 35.9389 46.3847C38.6653 46.0054 40.9263 44.8762 42.4664 42.5923C43.8572 40.5318 44.0922 38.2143 44.0472 35.8363C44.0414 35.5343 43.8384 35.5133 43.6034 35.5133C42.7536 35.5133 41.9023 35.5343 41.0524 35.5077C37.6023 35.4009 34.6235 32.7786 34.4727 29.5101C34.3248 26.3428 36.6481 23.4339 39.8618 22.9269C41.0684 22.7359 42.2794 22.8539 43.4889 22.8356C43.9036 22.8286 44.0631 22.726 44.0602 22.299C44.0574 21.5265 44.0312 20.7568 43.9442 19.9913C43.6426 17.3479 42.5621 15.109 40.2635 13.5331C38.5986 12.3912 36.7032 11.8771 34.6989 11.8673C27.8799 11.8308 21.0594 11.8518 14.2389 11.8504C13.4253 11.8504 12.6146 11.8504 11.804 11.9558C8.90493 12.3322 6.54103 13.5443 4.99653 16.029C4.05822 17.5375 3.64345 19.2034 3.63475 20.9436C3.6101 26.0942 3.6246 31.2462 3.62315 36.3967C3.62315 37.1496 3.6217 37.901 3.73047 38.6511C4.13363 41.4195 5.40259 43.6626 7.91731 45.1458C9.45457 46.0518 11.1557 46.4605 12.9337 46.4774C16.5695 46.5125 20.2052 46.4872 23.8424 46.4872H23.8439ZM43.2771 31.985C43.2771 31.985 43.2771 31.9864 43.2771 31.9878C44.0719 31.9878 44.8666 31.985 45.6613 31.9878C46.1022 31.9892 46.3371 31.7954 46.3371 31.3571C46.3371 29.9048 46.3357 28.4539 46.3371 27.0016C46.3371 26.5718 46.1297 26.3512 45.6802 26.3526C44.0008 26.3569 42.32 26.3231 40.642 26.3737C39.3107 26.4144 37.9431 27.7502 38.0765 29.3135C38.2085 30.8557 39.5021 31.9471 41.109 31.9822C41.8312 31.9976 42.5534 31.985 43.2757 31.985H43.2771Z"
                                    fill="#5E0A83" />
                                <path
                                    d="M20.3106 24.1601C17.9975 24.1601 15.6844 24.1657 13.3712 24.1559C12.4866 24.1516 11.7847 23.5533 11.628 22.7148C11.4816 21.938 11.9732 21.1262 12.7607 20.8256C12.9855 20.7399 13.2161 20.7245 13.4524 20.7245C18.0425 20.7245 22.6325 20.7217 27.2225 20.7259C28.2928 20.7259 29.0933 21.4633 29.1035 22.4296C29.1151 23.4086 28.2971 24.1573 27.1949 24.1601C24.9007 24.1657 22.6049 24.1615 20.3106 24.1601Z"
                                    fill="#5E0A83" />
                            </g>
                            <defs>
                                <clipPath id="clip0_10_1564">
                                    <rect width="50" height="41.6667" fill="white" transform="translate(0 8.33334)" />
                                </clipPath>
                            </defs>
                        </svg>

                        <h3 class="font-16 font-weight-500 text-gray mt-25">{{ trans('panel.account_balance') }}</h3>
                        <span
                            class="mt-5 d-block font-30 text-secondary">{{ handlePrice($authUser->getAccountingBalance()) }}</span>
                    </div>

                    @php
                        $getFinancialSettings = getFinancialSettings();
                        $drawable = $authUser->getPayout();
                        $can_drawable =
                            $drawable >
                            ((!empty($getFinancialSettings) and !empty($getFinancialSettings['minimum_payout']))
                                ? (int) $getFinancialSettings['minimum_payout']
                                : 0);
                    @endphp

                    <div
                        class="mt-20 pt-10 border-top border-gray300 d-flex align-items-center @if ($can_drawable) justify-content-between @else justify-content-center @endif">
                        @if ($can_drawable)
                            <span class="font-16 font-weight-500 text-gray">{{ trans('panel.with_drawable') }}:</span>
                            <span class="font-16 font-weight-bold text-secondary">{{ handlePrice($drawable) }}</span>
                        @else
                            <a href="/panel/financial/account"
                                class="font-16 font-weight-bold text-dark-blue">{{ trans('financial.charge_account') }}</a>
                        @endif
                    </div>
                </div>
            </div>
            {{-- SCT Team --}}
            <div class="col-12 col-lg-3 mt-35 ">
                <div class="module-box dashboard-stats rounded-sm panel-shadow p-10 p-md-15 d-flex align-items-center mt-0">

                    <div class="d-flex flex-column pt-35 pb-50" style="align-items: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48px" height="48px"><path fill="#5059c9" d="M44,22v8c0,3.314-2.686,6-6,6s-6-2.686-6-6V20h10C43.105,20,44,20.895,44,22z M38,16 c2.209,0,4-1.791,4-4c0-2.209-1.791-4-4-4s-4,1.791-4,4C34,14.209,35.791,16,38,16z"/><path fill="#7b83eb" d="M35,22v11c0,5.743-4.841,10.356-10.666,9.978C19.019,42.634,15,37.983,15,32.657V20h18 C34.105,20,35,20.895,35,22z M25,17c3.314,0,6-2.686,6-6s-2.686-6-6-6s-6,2.686-6,6S21.686,17,25,17z"/><circle cx="25" cy="11" r="6" fill="#7b83eb"/><path d="M26,33.319V20H15v12.657c0,1.534,0.343,3.008,0.944,4.343h6.374C24.352,37,26,35.352,26,33.319z" opacity=".05"/><path d="M15,20v12.657c0,1.16,0.201,2.284,0.554,3.343h6.658c1.724,0,3.121-1.397,3.121-3.121V20H15z" opacity=".07"/><path d="M24.667,20H15v12.657c0,0.802,0.101,1.584,0.274,2.343h6.832c1.414,0,2.56-1.146,2.56-2.56V20z" opacity=".09"/><linearGradient id="DqqEodsTc8fO7iIkpib~Na" x1="4.648" x2="23.403" y1="14.648" y2="33.403" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#5961c3"/><stop offset="1" stop-color="#3a41ac"/></linearGradient><path fill="url(#DqqEodsTc8fO7iIkpib~Na)" d="M22,34H6c-1.105,0-2-0.895-2-2V16c0-1.105,0.895-2,2-2h16c1.105,0,2,0.895,2,2v16 C24,33.105,23.105,34,22,34z"/><path fill="#fff" d="M18.068,18.999H9.932v1.72h3.047v8.28h2.042v-8.28h3.047V18.999z"/></svg>
                        <span class="font-16 text-gray font-weight-500 text-center pb-10">برنامج ميكروسوفت تيمز</span>
                        <a target="_blank" rel="noopener noreferrer" class="btn btn-primary" style=""
                            href="https://go.microsoft.com/fwlink/?linkid=2187217&amp;clcid=0x409&amp;culture=en-us&amp;country=us/">اضغط
                            هنا لتنزيل البرنامج</a>
                    </div>

                </div>
                {{-- <div
                    class="module-box dashboard-stats rounded-sm panel-shadow p-10 p-md-15 d-flex align-items-center mt-20">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48px" height="48px"><path fill="#5059c9" d="M44,22v8c0,3.314-2.686,6-6,6s-6-2.686-6-6V20h10C43.105,20,44,20.895,44,22z M38,16 c2.209,0,4-1.791,4-4c0-2.209-1.791-4-4-4s-4,1.791-4,4C34,14.209,35.791,16,38,16z"/><path fill="#7b83eb" d="M35,22v11c0,5.743-4.841,10.356-10.666,9.978C19.019,42.634,15,37.983,15,32.657V20h18 C34.105,20,35,20.895,35,22z M25,17c3.314,0,6-2.686,6-6s-2.686-6-6-6s-6,2.686-6,6S21.686,17,25,17z"/><circle cx="25" cy="11" r="6" fill="#7b83eb"/><path d="M26,33.319V20H15v12.657c0,1.534,0.343,3.008,0.944,4.343h6.374C24.352,37,26,35.352,26,33.319z" opacity=".05"/><path d="M15,20v12.657c0,1.16,0.201,2.284,0.554,3.343h6.658c1.724,0,3.121-1.397,3.121-3.121V20H15z" opacity=".07"/><path d="M24.667,20H15v12.657c0,0.802,0.101,1.584,0.274,2.343h6.832c1.414,0,2.56-1.146,2.56-2.56V20z" opacity=".09"/><linearGradient id="DqqEodsTc8fO7iIkpib~Na" x1="4.648" x2="23.403" y1="14.648" y2="33.403" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#5961c3"/><stop offset="1" stop-color="#3a41ac"/>
                    </linearGradient><path fill="url(#DqqEodsTc8fO7iIkpib~Na)"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     d="M22,34H6c-1.105,0-2-0.895-2-2V16c0-1.105,0.895-2,2-2h16c1.105,0,2,0.895,2,2v16 C24,33.105,23.105,34,22,34z"/><path fill="#fff" d="M18.068,18.999H9.932v1.72h3.047v8.28h2.042v-8.28h3.047V18.999z"/></svg>
                    <div class="d-flex flex-column">
                        <span class="font-16 text-gray font-weight-500 text-center pb-10">برنامج ميكروسوفت تيمز</span>

                        <a target="_blank" rel="noopener noreferrer" class="btn btn-primary" style=""
                            href="https://go.microsoft.com/fwlink/?linkid=2187217&amp;clcid=0x409&amp;culture=en-us&amp;country=us/">اضغط
                            هنا لتنزيل البرنامج</a>

                    </div>
                </div>--}}
            </div>
    </section>

    <section class="dashboard">
        <div class="row p-20">

            {{-- Calender --}}
            <div class="col-12 col-lg-5 mt-5 rounded-sm">
                @include('web.default.panel.includes.calender')
            </div>

            {{-- download files --}}
            <div class="col-12 col-lg-3 mt-5 rounded-sm">
                @include('web.default.panel.includes.downloadfiles')
            </div>
            <div class="col-12 col-lg-3 mt-5 ">
                <div class="module-box dashboard-stats rounded-sm panel-shadow p-10 p-md-15 d-flex align-items-center mt-0">

                    <div class="d-flex flex-column pt-35 pb-50" style="align-items: center;">
<svg width="40" height="40" viewBox="0 0 24 21" fill="none" xmlns="http://www.w3.org/2000/svg">
<g clip-path="url(#clip0_84_3297)">
<path d="M23.9835 10.1376C23.9835 10.0414 23.9848 9.94589 23.9879 9.85099C23.9905 9.75036 23.9937 9.64782 23.9937 9.54273C23.9937 9.38733 23.9867 9.22747 23.96 9.06378C23.8109 8.14982 23.43 7.38363 22.8556 6.82252C22.2805 6.2614 21.5163 5.90983 20.6194 5.80665C20.5883 5.80347 20.5654 5.79774 20.5489 5.79264C20.5331 5.78691 20.5229 5.78118 20.514 5.77481C20.5051 5.7678 20.4975 5.76016 20.4874 5.74615C20.4772 5.73214 20.4652 5.71176 20.4518 5.68182C19.932 4.51374 19.2319 3.46794 18.3045 2.61449C16.4333 0.889755 14.3641 -0.000636563 12.1419 3.41445e-07H12.1393C11.0666 3.41445e-07 9.96028 0.206357 8.82473 0.619071C7.61937 1.0579 6.58031 1.71582 5.70564 2.56545C4.82971 3.41508 4.11817 4.45451 3.56087 5.65698C3.545 5.69137 3.5304 5.71621 3.51707 5.73341C3.50438 5.75124 3.49359 5.76143 3.48216 5.77035C3.47074 5.77863 3.45804 5.78564 3.43836 5.79264C3.41932 5.79901 3.39266 5.80538 3.35648 5.8092C2.46087 5.91684 1.69665 6.27542 1.12475 6.84099C0.55222 7.40656 0.176457 8.17657 0.0393536 9.09053C0.00825157 9.29561 0 9.49751 0 9.6924C0 9.94143 0.0133295 10.179 0.0133295 10.3936C0.0133295 10.4312 0.0126947 10.4688 0.01206 10.507V11.5993C0.0126947 12.6795 0.433525 13.6304 1.09365 14.3087C1.75314 14.9876 2.6532 15.3965 3.61292 15.3965C4.00391 15.3965 4.40507 15.329 4.80114 15.1832C5.03726 15.0972 5.22895 14.9793 5.36542 14.8201C5.43334 14.7405 5.48539 14.6507 5.5203 14.5545C5.55521 14.4577 5.57298 14.3545 5.57298 14.2482V14.2444C5.57298 14.0335 5.50887 13.8151 5.39589 13.5839H5.39526C4.81003 12.4005 4.51297 11.1732 4.51297 9.90322C4.51297 9.38096 4.56312 8.85169 4.66467 8.31478C5.37621 4.55641 8.48261 1.97122 11.9952 1.97122C12.4161 1.97122 12.8432 2.00816 13.2736 2.08522C16.8173 2.71703 19.4826 5.97034 19.4826 9.78094V9.78221C19.4826 10.2306 19.4464 10.6866 19.3696 11.1471C19.0859 12.8514 18.4537 14.2246 17.5028 15.2895C16.552 16.3538 15.2781 17.113 13.6938 17.5696C13.6456 17.583 13.6087 17.5887 13.5884 17.5894H13.5859C13.5707 17.5887 13.563 17.5875 13.558 17.5856C13.5535 17.5836 13.5484 17.5817 13.5389 17.5754H13.5383C13.5383 17.5754 13.5383 17.5747 13.537 17.5741C13.5211 17.562 13.4907 17.5282 13.4551 17.4658C13.0591 16.7792 12.357 16.3907 11.6176 16.3914C11.4157 16.3914 11.2113 16.42 11.0089 16.4793C10.7816 16.5455 10.5709 16.6512 10.3811 16.7882C10.1913 16.9251 10.0231 17.0932 9.88284 17.283C9.60355 17.6613 9.43154 18.1276 9.42202 18.6173V18.6199C9.42139 18.6371 9.42139 18.6524 9.42139 18.6664V18.6683C9.42139 19.2014 9.58769 19.6988 9.87649 20.0969C10.1653 20.4949 10.5772 20.7936 11.0666 20.9261C11.2501 20.9758 11.4348 21 11.6163 21C12.4338 21 13.1949 20.5147 13.5776 19.709C13.6087 19.644 13.6354 19.5994 13.6551 19.5797C13.6678 19.5676 13.6767 19.56 13.6887 19.553C13.7008 19.5466 13.7166 19.5396 13.7433 19.5338H13.7446C14.9956 19.2683 16.1223 18.7931 17.1182 18.1065C18.1147 17.42 18.9792 16.5226 19.7092 15.4182C19.7358 15.378 19.7555 15.3596 19.7638 15.3545C19.7638 15.3545 19.7644 15.3545 19.7644 15.3538C19.7701 15.3507 19.772 15.35 19.7765 15.3488C19.7803 15.3475 19.7879 15.3462 19.8025 15.3456H19.8038C19.8152 15.3456 19.8317 15.3468 19.8526 15.3507C20.0361 15.3825 20.2195 15.3972 20.401 15.3972C22.1091 15.3952 23.6522 14.066 23.9327 12.2521C23.986 11.9082 23.9994 11.5675 23.9994 11.2305C23.9987 10.8611 23.9829 10.4962 23.9829 10.1376H23.9835ZM2.41771 12.9502C2.11558 12.6617 1.91627 12.27 1.9023 11.863C1.88326 11.3254 1.85597 10.7904 1.85597 10.2599C1.85597 9.95099 1.86485 9.64336 1.88961 9.33765C1.91627 9.00582 2.06607 8.66572 2.29267 8.39248C2.44564 8.20651 2.63288 8.05301 2.836 7.95047C2.72111 8.60075 2.66335 9.2453 2.66335 9.88348C2.66335 11.0751 2.8652 12.2464 3.27778 13.3966C2.96358 13.3425 2.65954 13.182 2.41771 12.9502ZM12.3196 18.6995C12.3196 18.8957 12.2472 19.0753 12.1228 19.2065C11.9978 19.3396 11.8213 19.4179 11.6284 19.4224H11.6106L11.6087 19.423C11.4164 19.4237 11.2386 19.353 11.1117 19.2224C10.9847 19.0918 10.9156 18.9103 10.913 18.7122V18.6982C10.913 18.4951 10.9841 18.3084 11.1104 18.1715C11.2355 18.0352 11.4151 17.9524 11.6119 17.9524H11.6157C11.8112 17.9524 11.9883 18.0358 12.1139 18.1683C12.2409 18.3014 12.3171 18.4842 12.3202 18.6842V18.6995H12.3196ZM20.7184 13.4049C21.1335 12.2483 21.3379 11.0713 21.3379 9.87965C21.3379 9.23829 21.2789 8.59247 21.1615 7.94347C21.4731 8.10715 21.708 8.33325 21.8743 8.62432C22.0514 8.93704 22.1478 9.3281 22.1478 9.79877C22.1478 9.85227 22.1466 9.90641 22.144 9.96182C22.1199 10.5019 22.1193 11.0382 22.1186 11.5719C22.1174 12.0884 21.9511 12.5279 21.6712 12.8527C21.4319 13.1291 21.1081 13.3234 20.7184 13.4049Z" fill="black"/>
</g>
<defs>
<clipPath id="clip0_84_3297">
<rect width="24" height="21" fill="white"/>
</clipPath>
</defs>
</svg>

                        <span class="font-16 text-gray font-weight-500 text-center pb-10">فريق الدعم والتواصل </span>
                        <a target="_blank" rel="noopener noreferrer" class="btn btn-primary mt-10" style=""
                            href="https://support.anasacademy.uk/">
لتقديم طلب اضغط هنا                        
                        </a>
                          <a target="_blank" rel="noopener noreferrer" class="btn btn-primary mt-10" style=""
                            href="https://support.anasacademy.uk/search">
لمتابعة طلب سابق اضغط هنا                        </a>
                    </div>

                </div>
               
            </div>

            {{-- <div class="col-12 col-lg-3 mt-35">
                <div class="bg-white account-balance rounded-sm panel-shadow py-15 py-md-15 px-10 px-md-20">
                    <div data-percent="{{ !empty($nextBadge) ? $nextBadge['percent'] : 0 }}" data-label="{{ (!empty($nextBadge) and !empty($nextBadge['earned'])) ? $nextBadge['earned']->title : '' }}" id="nextBadgeChart" class="text-center">
                    </div>
                    <div class="mt-10 pt-10 border-top border-gray300 d-flex align-items-center justify-content-between">
                        <span class="font-16 font-weight-500 text-gray">{{ trans('panel.next_badge') }}:</span>
                        <span class="font-16 font-weight-bold text-secondary">{{ (!empty($nextBadge) and !empty($nextBadge['badge'])) ? $nextBadge['badge']->title : trans('public.not_defined') }}</span>
                    </div>
                </div>
            </div> --}}
        </div>

        {{-- <div class="row">
            <div class="col-12 col-lg-6 mt-35">
                <div class="bg-white noticeboard rounded-sm panel-shadow py-10 py-md-20 px-15 px-md-30">
                    <h3 class="font-16 text-dark-blue font-weight-bold">{{ trans('panel.noticeboard') }}</h3>

                    @foreach ($authUser->getUnreadNoticeboards() as $getUnreadNoticeboard)
                        <div class="noticeboard-item py-15">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h4 class="js-noticeboard-title font-weight-500 text-secondary">{!! truncate($getUnreadNoticeboard->title,150) !!}</h4>
                                    <div class="font-12 text-gray mt-5">
                                        <span class="mr-5">{{ trans('public.created_by') }} {{ $getUnreadNoticeboard->sender }}</span>
                                        |
                                        <span class="js-noticeboard-time ml-5">{{ dateTimeFormat($getUnreadNoticeboard->created_at,'j M Y | H:i') }}</span>
                                    </div>
                                </div>

                                <div>
                                    <button type="button" data-id="{{ $getUnreadNoticeboard->id }}" class="js-noticeboard-info btn btn-sm btn-border-white">{{ trans('panel.more_info') }}</button>
                                    <input type="hidden" class="js-noticeboard-message" value="{{ $getUnreadNoticeboard->message }}">
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>

            <div class="col-12 col-lg-6 mt-35">
                <div class="bg-white monthly-sales-card rounded-sm panel-shadow py-10 py-md-20 px-15 px-md-30">
                    <div class="d-flex align-items-center justify-content-between">
                        <h3 class="font-16 text-dark-blue font-weight-bold">{{ ($authUser->isUser()) ? trans('panel.learning_statistics') : trans('panel.monthly_sales') }}</h3>

                        <span class="font-16 font-weight-500 text-gray">{{ dateTimeFormat(time(),'M Y') }}</span>
                    </div>

                    <div class="monthly-sales-chart">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
        </div> --}}
    </section>


    <div class="d-none" id="iNotAvailableModal">
        <div class="offline-modal">
            <h3 class="section-title after-line">{{ trans('panel.offline_title') }}</h3>
            <p class="mt-20 font-16 text-gray">{{ trans('panel.offline_hint') }}</p>

            <div class="form-group mt-15">
                <label>{{ trans('panel.offline_message') }}</label>
                <textarea name="message" rows="4" class="form-control ">{{ $authUser->offline_message }}</textarea>
                <div class="invalid-feedback"></div>
            </div>

            <div class="mt-30 d-flex align-items-center justify-content-end">
                <button type="button"
                    class="js-save-offline-toggle btn btn-primary btn-sm">{{ trans('public.save') }}</button>
                <button type="button" class="btn btn-danger ml-10 close-swl btn-sm">{{ trans('public.close') }}</button>
            </div>
        </div>
    </div>

    <div class="d-none" id="noticeboardMessageModal">
        <div class="text-center">
            <h3 class="modal-title font-20 font-weight-500 text-dark-blue"></h3>
            <span class="modal-time d-block font-12 text-gray mt-25"></span>
            <p class="modal-message font-weight-500 text-gray mt-4"></p>
        </div>
    </div>

@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/apexcharts/apexcharts.min.js"></script>
    <script src="/assets/default/vendors/chartjs/chart.min.js"></script>

    <script>
        var offlineSuccess = '{{ trans('panel.offline_success') }}';
        var $chartDataMonths = @json($monthlyChart['months']);
        var $chartData = @json($monthlyChart['data']);
    </script>

    <script src="/assets/default/js/panel/dashboard.min.js"></script>
@endpush

@if (!empty($giftModal))
    @push('scripts_bottom2')
        <script>
            (function() {
                "use strict";

                handleLimitedAccountModal('{!! $giftModal !!}', 40)
            })(jQuery)
        </script>
    @endpush
@endif
