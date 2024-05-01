@extends('admin.layouts.app')


@section('content')
    <section class="section">
        <div class="section-header">
            <h1>قائمة بالخدمات الإالكترونية</h1>
        </div>


        @if (Session::has('success'))
            <div class="container d-flex justify-content-center mt-80">
                <p class="alert alert-success w-75 text-center"> {{ Session::get('success') }} </p>
            </div>
        @endif

        @if (Session::has('error'))
            <div class="container d-flex justify-content-center mt-80">
                <p class="alert alert-success w-75 text-center"> {{ Session::get('error') }} </p>
            </div>
        @endif



        <div class="section-body">

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped font-14 ">
                                    <tr>
                                        <th>{{ 'Index' }}</th>
                                        <th class="text-left">{{ 'الإسم' }}</th>
                                        <th class="text-left">{{ 'الوصف' }}</th>
                                        <th>{{ 'الثمن' }}</th>
                                        <th>{{ 'الحالة' }}</th>
                                        <th>{{ 'المنشئ' }}</th>
                                        <th>{{ 'تاريخ الإنشاء ' }}</th>

                                        <th width="120">{{ 'الأجراءات' }}</th>
                                    </tr>
                                    @foreach ($services as $index => $service)
                                        <tr class="text-center">
                                            <td>{{ ++$index }}</td>
                                            <td class="text-left">{{ $service->bundleStudent->student->registeredUser->user_code }}
                                            </td>
                                            <td class="text-left">
                                                <div class="d-flex align-items-center">
                                                    <figure class="avatar mr-2">
                                                        <img src="{{ $service->bundleStudent->student->registeredUser->getAvatar() }}" alt="{{ $service->bundleStudent->student->registeredUser->full_name }}">
                                                    </figure>
                                                    <div class="media-body ml-1">
                                                        <div class="mt-0 mb-1 font-weight-bold">{{ $service->bundleStudent->student ? $service->bundleStudent->student->ar_name : $service->bundleStudent->student->registeredUser->full_name }}
                                                        </div>

                                                        @if ($service->bundleStudent->student->registeredUser->mobile)
                                                            <div class="text-primary text-small font-600-bold">{{ $service->bundleStudent->student->registeredUser->mobile }}</div>
                                                        @endif

                                                        @if ($service->bundleStudent->student->registeredUser->email)
                                                            <div class="text-primary text-small font-600-bold">{{ $service->bundleStudent->student->registeredUser->email }}</div>
                                                        @endif
                                                    </div>
                                                </div>

                                            </td>


                                            <td>{{ $service->bundleStudent->bundle->category->slug }}</td>

                                            <td>{{ $service->bundleStudent->bundle->title }}</td>

                                            <td>
                                                <a href="/store/{{ $service->identity_attachment }}" target="_blank">
                                                    @if (pathinfo($service->identity_attachment, PATHINFO_EXTENSION) != 'pdf')
                                                        <img src="/store/{{ $service->identity_attachment }}"
                                                            alt="identity_attachment" width="100px">
                                                    @else
                                                        pdf ملف <i class="fas fa-file font-20"></i>

                                                    @endif
                                                </a>
                                            </td>

                                            <td>
                                                <a href="/store/{{ $service->admission_attachment }}" target="_blank" class="text-black">
                                                    pdf ملف <i class="fas fa-file font-20"></i>
                                                </a>
                                            </td>

                                            <td>
                                                @if ($service->status=="pending")
                                                <span class="text-success"> معلق</span>
                                                @elseif($service->status=="approved")
                                                <span class="text-primary"> تم الموافقة عليه</span>
                                                @elseif($service->status=="rejected")
                                                <div class="text-danger">
                                                    <span class=""> تم رفضه</span>
                                                    @include('admin.includes.message_button', [
                                                            'url' => '#',
                                                            'btnClass' =>
                                                                'd-flex align-items-center mt-1',
                                                            'btnText' =>'<span class="ml-2">' .
                                                                ' سبب الرفض</span>',
                                                            'hideDefaultClass' => true,
                                                            'deleteConfirmMsg'=> 'هذا سبب الرفض',
                                                            'message' => $service->message,
                                                            'id' => $service->id,
                                                        ])
                                                </div>
                                                @endif
                                            </td>

                                            <td>{{ $service->admin ? $service->admin->full_name : '' }}
                                            </td>

                                            <td class="font-12">
                                                {{ (Carbon\Carbon::parse($service->created_at))
                                                    ->translatedFormat(handleDateAndTimeFormat('Y M j | H:i')) }}</td>

                                            {{-- actions --}}
                                            <td width="200" class="">

                                                <div class="d-flex justify-content-center align-items-baseline gap-3">
                                                    @can('admin_services_approve')
                                                        {{-- <a href="{{getAdminPanelUrl().'/services/'.$service->id.'/approve'}}" class="btn btn-primary d-flex align-items-center btn-sm mt-1"> <i class="fa fa-check"></i><span class="ml-2"> قبول</a> --}}

                                                        {{-- <a href="{{getAdminPanelUrl().'/services/'.$service->id.'/reject'}}" class="btn btn-danger d-flex align-items-center btn-sm mt-1"> <i class="fa fa-check"></i><span class="ml-2"> رفض</a> --}}
                                                        @include('admin.includes.delete_button', [
                                                            'url' =>
                                                                getAdminPanelUrl() .
                                                                '/services/' .
                                                                $service->id .
                                                                '/approve',
                                                            'btnClass' =>
                                                                'btn btn-primary d-flex align-items-center btn-sm mt-1 ml-3',
                                                            'btnText' =>
                                                                '<i class="fa fa-check"></i><span class="ml-2"> قبول' .
                                                                // trans('admin/main.approve') .
                                                                '</span>',
                                                            'hideDefaultClass' => true,
                                                        ])
                                                    @endcan
                                                    @can('admin_services_reject')
                                                        @include('admin.includes.confirm_delete_button', [
                                                            'url' =>
                                                                getAdminPanelUrl() .
                                                                '/services/' .
                                                                $service->id .
                                                                '/reject',
                                                            'btnClass' =>
                                                                'btn btn-danger d-flex align-items-center btn-sm mt-1',
                                                            'btnText' =>
                                                                '<i class="fa fa-times"></i><span class="ml-2">' .
                                                                trans('admin/main.reject') .
                                                                '</span>',
                                                            'hideDefaultClass' => true,
                                                            'id' => $service->id
                                                        ])
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            {{ $services->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection




@push('libraries_top')
    <link rel="stylesheet" href="/assets/admin/vendor/owl.carousel/owl.carousel.min.css">
    <link rel="stylesheet" href="/assets/admin/vendor/owl.carousel/owl.theme.min.css">
@endpush
