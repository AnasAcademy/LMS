@extends(getTemplate() .'.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/chartjs/chart.min.css"/>
    <link rel="stylesheet" href="/assets/default/vendors/apexcharts/apexcharts.css"/>
@endpush
<style>
    .dashboard-banner-container{
        margin-top:0px !important;
    }
</style>
@section('content')
    <section class="dashboard">
         <div class="row p-10">
               <div class="d-block align-items-start align-items-md-center justify-content-between flex-column flex-md-row">
                <h1 class="section-title">{{ trans('panel.dashboard') }}</h1>
    
                @if(!$authUser->isUser())
                    <div class="d-flex align-items-center flex-row-reverse flex-md-row justify-content-start justify-content-md-center mt-20 mt-md-0">
                        <label class="mb-0 mr-10 cursor-pointer text-gray font-14 font-weight-500" for="iNotAvailable">{{ trans('panel.i_not_available') }}</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="disabled" @if($authUser->offline) checked @endif class="custom-control-input" id="iNotAvailable">
                            <label class="custom-control-label" for="iNotAvailable"></label>
                        </div>
                    </div>
                @endif
            </div>
         </div>



        
<div class="row p-20">
          
    <div class="col-12 col-lg-5 mt-35">

            @if(!$authUser->financial_approval and !$authUser->isUser())
                <div class="p-15 mt-20 p-lg-20 not-verified-alert font-weight-500 text-dark-blue rounded-sm panel-shadow">
                    {{ trans('panel.not_verified_alert') }}
                    <a href="/panel/setting/step/7" class="text-decoration-underline">{{ trans('panel.this_link') }}</a>.
                </div>
            @endif
    
            <div class="bg-white dashboard-banner-container position-relative p-35 rounded-sm">
                <h2 class="font-30 text-primary line-height-1">
                    <span class="d-block">{{ trans('panel.hi') }} {{ $authUser->full_name }}</span>
                    <!--<span class="font-16 text-secondary font-weight-bold">{{ trans('panel.have_event',['count' => !empty($unReadNotifications) ? count($unReadNotifications) : 0]) }}</span>-->
                </h2>
                <ul class="mt-15 unread-notification-lists">
                    <h4>بياناتك الاكاديمية</h4>
                    <li class="mt-1 text-gray font-16 font-weight-bold text-left">كود الطالب : {{ $authUser->user_code }}</li>
                    <li class="mt-1 text-gray font-16 font-weight-bold text-left">  البريد الاكاديمي : {{ $authUser->user_code }}@anasacademy.uk</li>
                    <li class="mt-1 text-gray font-16 font-weight-bold text-left">  البرنامج الدراسي : {{ $authUser->user_code }}@anasacademy.uk</li>

               
               
                </ul>
        </div>
        
    </div>
        <div class="col-12 col-lg-3 mt-35">
                <div class="bg-white account-balance rounded-sm p-35">
                    <div class="text-center">
                        @include('web.default.panel.includes.sidebar_icons.financial')
                        <h3 class="font-16 font-weight-500 text-gray mt-25">{{ trans('panel.account_balance') }}</h3>
                        <span class="mt-5 d-block font-30 text-secondary">{{ handlePrice($authUser->getAccountingBalance()) }}</span>
                    </div>

                    @php
                        $getFinancialSettings = getFinancialSettings();
                        $drawable = $authUser->getPayout();
                        $can_drawable = ($drawable > ((!empty($getFinancialSettings) and !empty($getFinancialSettings['minimum_payout'])) ? (int)$getFinancialSettings['minimum_payout'] : 0))
                    @endphp

                    <div class="mt-20 pt-10 border-top border-gray300 d-flex align-items-center @if($can_drawable) justify-content-between @else justify-content-center @endif">
                        @if($can_drawable)
                            <span class="font-16 font-weight-500 text-gray">{{ trans('panel.with_drawable') }}:</span>
                            <span class="font-16 font-weight-bold text-secondary">{{ handlePrice($drawable) }}</span>
                        @else
                            <a href="/panel/financial/account" class="font-16 font-weight-bold text-dark-blue">{{ trans('financial.charge_account') }}</a>
                        @endif
                    </div>
                </div>
        </div>
    </section>

    <section class="dashboard">
        <div class="row p-20">
        

            <div class="col-12 col-lg-2 mt-5">
                <div class="panes aem-GridColumn aem-GridColumn--default--12" data-component-id="77d468126200ece060f2cbc925df1f8b">
                    <div id="panes-uid32b6" data-oc="oc37e0">
                        <div class="row justify-content-center">
                            <div class="col-12 col-md-12">
                                <div class="h-100 text-sm-center">
                                    <div class=" link-group ">
                                        <a class="btn btn-primary btn " style="color: #fff; background-color: #f70387; border: #f70387; padding: 10px;" href="https://support.anasacademy.uk/" target="_blank" rel="noopener" data-bi-cn="DownloadTeams1" data-bi-ecn="Download Teams" data-bi-ct="button" data-bi-pa="body" data-bi-bhvr="41" data-bi-tags="{&quot;BiLinkName&quot;:&quot;DownloadTeams1&quot;}" data-target="https://go.microsoft.com/fwlink/?linkid=2187217&amp;clcid=0x409&amp;culture=en-us&amp;country=us" aria-label="Download Teams app" data-regenerate-fwlink="true">الدعم والتواصل</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
             
             <div class="col-12 col-lg-2 mt-5">
                <a href="@if($authUser->isUser()) /panel/webinars/purchases @else /panel/requirements @endif" class="dashboard-stats rounded-sm panel-shadow p-10 p-md-20 d-flex align-items-center mt-0 mt-md-30">
                    <div class="stat-icon monthly-sales">
                        <img src="/assets/default/img/icons/meeting.svg" alt="">
                    </div>
                    <div class="d-flex flex-column ml-15">
                        <span class="font-16 text-gray font-weight-500">Microsoft Teams app</span>
                    </div>
                </a>
            </div>
            <div class="col-12 col-lg-2 mt-5">
                <a href="@if($authUser->isUser()) /panel/webinars/purchases @else /panel/requirements @endif" class="dashboard-stats rounded-sm panel-shadow p-10 p-md-20 d-flex align-items-center mt-0 mt-md-30">
                    <div class="stat-icon monthly-sales">
                        <img src="/assets/default/img/icons/meeting.svg" alt="">
                    </div>
                    <div class="d-flex flex-column ml-15">
                        <span class="font-16 text-gray font-weight-500">Links</span>
                    </div>
                </a>
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

                    @foreach($authUser->getUnreadNoticeboards() as $getUnreadNoticeboard)
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
                <button type="button" class="js-save-offline-toggle btn btn-primary btn-sm">{{ trans('public.save') }}</button>
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

@if(!empty($giftModal))
    @push('scripts_bottom2')
        <script>
            (function () {
                "use strict";

                handleLimitedAccountModal('{!! $giftModal !!}', 40)
            })(jQuery)
        </script>
    @endpush
@endif
