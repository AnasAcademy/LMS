@extends('admin.layouts.app')


@section('content')
    <section class="section">
        <div class="section-header">
            <h1>قائمة بالخدمات الإالكترونية</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a
                        href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a>
                </div>
                <div class="breadcrumb-item active">
                    <a href="{{ getAdminPanelUrl() }}/services">الخدمات الإلكترونية</a>
                </div>
                <div class="breadcrumb-item">
                    قائمة
                </div>
            </div>
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
                                        <th class="text-center">{{ 'Index' }}</th>
                                        <th class="text-center">{{ 'العنوان' }}</th>
                                        <th class="text-center">{{ 'الوصف' }}</th>
                                        <th class="text-center">{{ 'السعر' }}</th>
                                        <th class="text-center">{{ 'الحالة' }}</th>
                                        <th class="text-center">{{ 'المنشئ' }}</th>
                                        <th class="text-center">{{ 'تاريخ الإنشاء ' }}</th>

                                        <th width="120">{{ 'الأجراءات' }}</th>
                                    </tr>
                                    @foreach ($services as $service)
                                        <tr class="text-center">
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="text-center">{{ $service->title }}</td>
                                            <td class="text-center">{{ $service->description }}</td>
                                            <td class="text-center">{{ $service->price }}</td>
                                            <td class="text-center">{{ trans('admin/main.'.$service->status) }}</td>
                                            <td class="text-center">{{ $service->created_by ?  $service->createdBy->full_name  : '' }}</td>

                                            <td class="font-12">
                                                {{ Carbon\Carbon::parse($service->created_at)->translatedFormat(handleDateAndTimeFormat('Y M j | H:i')) }}
                                            </td>

                                            {{-- actions --}}
                                            <td width="200" class="">

                                                <div class="d-flex justify-content-center align-items-baseline gap-3">

                                                    {{-- <a href="{{ getAdminPanelUrl() }}/services/{{ $service->id }}" class="btn-transparent  text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                        <i class="fa fa-eye"></i>
                                                    </a> --}}

                                                    @include('admin.services.show', [
                                                        'url' => getAdminPanelUrl() . '/services/' . $service->id,
                                                        'btnClass' => 'btn-transparent  text-primary',
                                                        'btnText' => '<i class="fa fa-eye"></i>',
                                                        'hideDefaultClass' => true,
                                                        'service' => $service,
                                                    ])

                                                    <a href="{{ getAdminPanelUrl() }}/services/{{ $service->id }}/edit" class="btn-transparent  text-primary ml-2" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                        <i class="fa fa-edit"></i>
                                                    </a>

                                                    @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/services/'.$service->id.'/delete' , 'btnClass' => '', 'deleteConfirmMsg' => trans('admin/main.delete_confirm_msg')])

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