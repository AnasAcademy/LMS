@extends(getTemplate() . '.panel.layouts.panel_layout')

@push('styles_top')
@endpush
@php
    $totalWebinars = 0;
    if (!empty($sales) and !$sales->isEmpty()) {
        foreach ($sales as $sale) {
            $item = $sale->bundle;
            if (!empty($item) and !empty($item->bundleWebinars) and !$item->bundleWebinars->isEmpty()) {
                $totalWebinars += $item->bundleWebinars->count();
            }
        }
    }
@endphp
@section('content')
    <section>
        <h2 class="section-title">{{ trans('panel.my_activity') }}</h2>
        
        <div class="activities-container mt-25 p-20 p-lg-35">
          {{--  <div class="row">
               {{-- <div class="col-12 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/webinars.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5"> {{ $totalWebinars }}</strong>
                        <span class="font-16 text-gray font-weight-500">إجمالي عدد المقررات</span>
                    </div>
                </div> --}}

                {{-- <div class="col-4 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/hours.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ convertMinutesToHourAndMinute($hours) }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('home.hours') }}</span>
                    </div>
                </div>--}}

             {{--   <div class="col-4 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/upcoming.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $upComing }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('panel.upcoming') }}</span>
                    </div>
                </div> --}}

            </div> --}}
        </div>
    </section> 

    <section class="mt-25">
        @if (!empty($sales) and !$sales->isEmpty())
            @foreach ($sales as $sale)
                @php
                    $item = !empty($sale->webinar) ? $sale->webinar : $sale->bundle;

                    $lastSession = !empty($sale->webinar) ? $sale->webinar->lastSession() : null;
                    $nextSession = !empty($sale->webinar) ? $sale->webinar->nextSession() : null;
                    $isProgressing = false;

                    if (
                        !empty($sale->webinar) and
                        $sale->webinar->start_date <= time() and
                        !empty($lastSession) and
                        $lastSession->date > time()
                    ) {
                        $isProgressing = true;
                    }

                @endphp

                @if (!empty($item))
                    <section class="mt-80">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 class="section-title after-line">{{ trans('product.courses') }} {{ $item->title }}</h2>
                        </div>
                        <div class="row mt-10">
                            <div class="col-12">
                                @if (!empty($item->bundleWebinars) and !$item->bundleWebinars->isEmpty())
                                    <div class="table-responsive">
                                        <table class="table table-striped text-center font-14">

                                            <tr>
                                                <th>ID</th>
                                                <th>{{ trans('public.title') }}</th>
                                                <th class="text-left">{{ trans('public.instructor') }}</th>
                                                <th>{{ trans('public.start_date') }}</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                            @php
                                                $totalHours = 0;
                                            @endphp
                                            @foreach ($item->bundleWebinars->sortBy('webinar.start_date') as $bundleWebinar)
                                                @php
                                                    $totalHours += $bundleWebinar->webinar->duration;
                                                @endphp
                                                @if (!empty($bundleWebinar->webinar->title))
                                                    <tr>
                                                        <td>{{ $bundleWebinar->webinar->id }}</td>
                                                        <th>{{ substr($bundleWebinar->webinar->title, 1, -3)}}</th>
                                                        <td class="text-left">
                                                            {{ $bundleWebinar->webinar->teacher->full_name }}</td>
                                                        <td>{{ dateTimeFormat($bundleWebinar->webinar->start_date, 'j F Y | H:i') }}
                                                        </td>
                                                        <td>
                                                            @if($bundleWebinar->webinar->duration !=0)
                                                                @if($bundleWebinar->webinar->video_demo)
                                                                    <a target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="width:190px;height:50px"
                                                                    href="{{ $bundleWebinar->webinar->video_demo }}">اضغط هنا للذهاب للمحاضرا</a>
                                                                @else
                                                                    <button class="btn btn-primary" style="width:190px;height:50px" disabled>اضغط هنا للذهاب للمحاضرا</button>
                                                                @endif

                                                                <a class="btn btn-primary"
                                                                    href="{{ $bundleWebinar->getWebinarUrl() }}"
                                                                    target="_blank" rel="noopener noreferrer">للذهاب للتسجيل</a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            <tr>
                                                <th colspan="4">إجمالي عدد الساعات:
                                                    {{ convertMinutesToHourAndMinute($totalHours) }}</th>
                                            </tr>
                                        </table>
                                    </div>
                                @else
                                    @include('admin.includes.no-result', [
                                        'file_name' => 'comment.png',
                                        'title' => trans('update.bundle_webinar_no_result'),
                                        'hint' => trans('update.bundle_webinar_no_result_hint'),
                                    ])
                                @endif
                            </div>
                        </div>
                    </section>
                @endif
            @endforeach
        @else
            @include(getTemplate() . '.includes.no-result', [
                'file_name' => 'student.png',
                'title' => trans('panel.no_result_purchases'),
                'hint' => trans('panel.no_result_purchases_hint'),
                'btn' => ['url' => '/classes?sort=newest', 'text' => trans('panel.start_learning')],
            ])
        @endif
    </section>

    <div class="my-30">
        {{ $sales->appends(request()->input())->links('vendor.pagination.panel') }}
    </div>

    @include('web.default.panel.webinar.join_webinar_modal')
@endsection

@push('scripts_bottom')
    <script>
        var undefinedActiveSessionLang = '{{ trans('webinars.undefined_active_session') }}';
    </script>

    <script src="/assets/default/js/panel/join_webinar.min.js"></script>
@endpush
