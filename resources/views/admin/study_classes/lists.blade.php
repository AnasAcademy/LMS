@extends('admin.layouts.app')

@push('libraries_top')
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>الدفعات الدراسية</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.classes') }}</div>

                <div class="breadcrumb-item">الدفعات الدراسية</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped font-14 ">
                                    <tr>
                                        <th>{{ trans('admin/main.id') }}</th>
                                        <th class="text-left">{{ trans('admin/main.title') }}</th>
                                        <th>عدد الطلبة</th>

                                        <th>{{trans('admin/main.created_at')}}</th>
                                        <th>{{trans('admin/main.updated_at')}}</th>
                                        <th width="120">{{ trans('admin/main.actions') }}</th>
                                    </tr>

                                    @foreach ($classes as $class)
                                        <tr class="text-center">
                                            <td>{{ $class->id }}</td>
                                            <td width="18%" class="text-left">
                                                <a class="text-primary mt-0 mb-1 font-weight-bold"
                                                    href="">{{ $class->title }}</a>
                                                @if (!empty($class->title))
                                                    <div class="text-small">{{ $class->title }}</div>
                                                @else
                                                    <div class="text-small text-warning">
                                                        {{ trans('admin/main.no_category') }}</div>
                                                @endif

                                                 <td>{{ count($class->enrollments) }}</td>
                                            <td class="font-12">{{ dateTimeFormat($class->created_at, 'Y M j | H:i') }}
                                            </td>

                                            <td class="font-12">{{ dateTimeFormat($class->updated_at, 'Y M j | H:i') }}
                                            </td>


                                            <td width="150">
                                                <div class="btn-group dropdown table-actions">
                                                    <button type="button" class="btn-transparent dropdown-toggle"
                                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="fa fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dropdown-menu text-left webinars-lists-dropdown">

                                                        @can('admin_webinar_students_lists')
                                                            <a href="{{ getAdminPanelUrl() }}/classes/{{ $class->id }}/students"
                                                                target="_blank"
                                                                class="d-flex align-items-center text-dark text-decoration-none btn-transparent btn-sm text-primary mt-1 "
                                                                title="{{ trans('admin/main.students') }}">
                                                                <i class="fa fa-users"></i>
                                                                <span class="ml-2">{{ trans('admin/main.students') }}</span>
                                                            </a>
                                                        @endcan



                                                        @can('admin_webinars_delete')
                                                            @include('admin.includes.delete_button', [
                                                                'url' =>
                                                                    getAdminPanelUrl() .
                                                                    '/classes/' .
                                                                    $class->id .
                                                                    '/delete',
                                                                'btnClass' =>
                                                                    'd-flex align-items-center text-dark text-decoration-none btn-transparent btn-sm mt-1',
                                                                'btnText' =>
                                                                    '<i class="fa fa-times"></i><span class="ml-2">' .
                                                                    trans('admin/main.delete') .
                                                                    '</span>',
                                                            ])
                                                        @endcan
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            {{ $classes->appends(request()->input())->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
@endpush
