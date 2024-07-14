@extends('admin.layouts.app')

@push('libraries_top')
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.students') }} {{ trans('admin/main.list') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a>{{ trans('admin/main.students') }}</a></div>
                <div class="breadcrumb-item"><a href="#">{{ trans('admin/main.users_list') }}</a></div>
            </div>
        </div>
    </section>


    <section class="card">
        <div class="card-body">
            <form method="get" class="mb-0">

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="input-label">كود الطالب</label>
                            <input name="user_code" type="text" class="form-control"
                                value="{{ request()->get('user_code') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="input-label">بريد الطالب</label>
                            <input name="email" type="text" class="form-control" value="{{ request()->get('email') }}">
                        </div>
                    </div>


                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="input-label">اسم الطالب</label>
                            <input
                                name={{ request()->is(getAdminPanelUrl('/students/users', false)) ? 'ar_name' : 'full_name' }}
                                type="text" class="form-control"
                                value="{{ request()->get('ar_name') }}{{ request()->get('full_name') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="input-label">هاتف الطالب</label>
                            <input name="mobile" type="text" class="form-control"
                                value="{{ request()->get('mobile') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group mt-1">
                            <label class="input-label mb-4"> </label>
                            <input type="submit" class="text-center btn btn-primary w-100"
                                value="{{ trans('admin/main.show_results') }}">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
   

    <div class="card">
        <div class="card-header">
            @can('admin_users_export_excel')
                <a href="{{ getAdminPanelUrl() }}/students/excelEnroller?{{ http_build_query(request()->all()) }}"
                    class="btn btn-primary">{{ trans('admin/main.export_xls') }}</a>
            @endcan
            <div class="h-10"></div>
        </div>

        <div class="card-body">
            <div class="table-responsive text-center">
                <table class="table table-striped font-14">
                    <tr>
                        <th>{{ '#' }}</th>
                        <th>كود الطالب</th>

                        <th>{{ trans('admin/main.name') }}</th>

                        <th> الدبلومات المسجلة</th>

                        <th>{{ trans('admin/main.register_date') }}</th>
                        <th>{{ trans('admin/main.status') }}</th>
                        <th width="120">{{ trans('admin/main.actions') }}</th>
                    </tr>

                    @foreach ($enrollments as $enrollment)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $enrollment->student->registeredUser->user_code }}</td>

                            <td class="text-left">
                                <div class="d-flex align-items-center">
                                    <figure class="avatar mr-2">
                                        <img src="{{  $enrollment->student->registeredUser->getAvatar() }}"
                                            alt="{{ $enrollment->student ? $enrollment->student->ar_name : null }}">
                                    </figure>
                                    <div class="media-body ml-1">
                                        <div class="mt-0 mb-1 font-weight-bold">
                                            {{ $enrollment->student ? $enrollment->student->ar_name : null }}</div>

                                        @if ($enrollment->student->registeredUser->mobile)
                                            <div class="text-primary text-left font-600-bold" style="font-size:12px;">
                                                {{ $enrollment->student->registeredUser->mobile }}</div>
                                        @endif

                                        @if ($enrollment->student->registeredUser->email)
                                            <div class="text-primary text-small font-600-bold">
                                                {{ $enrollment->student->registeredUser->email }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>


                            <td>

                                {{ $enrollment->bundle->title }}

                            </td>


                            <td>
                                {{ dateTimeFormat($enrollment->created_at, 'j M Y | H:i') }}
                            </td>

                            <td>
                                @if ( $enrollment->student->registeredUser->ban and !empty( $enrollment->student->registeredUser->ban_end_at) and  $enrollment->student->registeredUser->ban_end_at > time())
                                    <div class="mt-0 mb-1 font-weight-bold text-danger">{{ trans('admin/main.ban') }}
                                    </div>
                                    <div class="text-small font-600-bold">Until
                                        {{ dateTimeFormat( $enrollment->student->registeredUser->ban_end_at, 'Y/m/j') }}</div>
                                @else
                                    <div
                                        class="mt-0 mb-1 font-weight-bold {{  $enrollment->student->registeredUser->status == 'active' ? 'text-success' : 'text-warning' }}">
                                        {{ trans('admin/main.' .  $enrollment->student->registeredUser->status) }}</div>
                                @endif
                            </td>

                            <td class="text-center mb-2" width="120">

                                @can('admin_users_impersonate')
                                    <a href="{{ getAdminPanelUrl() }}/users/{{  $enrollment->student->registeredUser->id }}/impersonate" target="_blank"
                                        class="btn-transparent  text-primary" data-toggle="tooltip" data-placement="top"
                                        title="{{ trans('admin/main.login') }}">
                                        <i class="fa fa-user-shield"></i>
                                    </a>
                                @endcan

                                @can('admin_users_edit')
                                    <a href="{{ getAdminPanelUrl() }}/users/{{  $enrollment->student->registeredUser->id }}/edit"
                                        class="btn-transparent  text-primary" data-toggle="tooltip" data-placement="top"
                                        title="{{ trans('admin/main.edit') }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                @endcan

                                @can('admin_users_delete')
                                    @include('admin.includes.delete_button', [
                                        'url' => getAdminPanelUrl() . '/users/' .  $enrollment->student->registeredUser->id . '/delete',
                                        'btnClass' => '',
                                        'deleteConfirmMsg' => trans('update.user_delete_confirm_msg'),
                                    ])
                                @endcan
                            </td>

                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

        <div class="card-footer text-center">
            {{ $enrollments->appends(request()->input())->links() }}
        </div>
    </div>


    <section class="card">
        <div class="card-body">
            <div class="section-title ml-0 mt-0 mb-3">
                <h5>{{ trans('admin/main.hints') }}</h5>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">
                            {{ trans('admin/main.students_hint_title_1') }}</div>
                        <div class=" text-small font-600-bold">{{ trans('admin/main.students_hint_description_1') }}
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">
                            {{ trans('admin/main.students_hint_title_2') }}</div>
                        <div class=" text-small font-600-bold">{{ trans('admin/main.students_hint_description_2') }}
                        </div>
                    </div>
                </div>


                <div class="col-md-4">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">
                            {{ trans('admin/main.students_hint_title_3') }}</div>
                        <div class="text-small font-600-bold">{{ trans('admin/main.students_hint_description_3') }}</div>
                    </div>
                </div>


            </div>
        </div>
    </section>
@endsection
