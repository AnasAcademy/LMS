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

                <div class="breadcrumb-item">الدفعات الدراسية</div>
            </div>
        </div>

        <div class="section-body">
            <div class="d-flex justify-content-end align-items-center mb-10">
                <button id="userAddExperiences" type="button" data-toggle="modal" data-target="#exampleModal"
                    class="btn btn-primary btn-sm mb-3">إنشاء دفعة جديدة</button>
            </div>
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

                                        <th>{{ trans('admin/main.created_at') }}</th>
                                        <th>{{ trans('admin/main.updated_at') }}</th>
                                        <th width="120">{{ trans('admin/main.actions') }}</th>
                                    </tr>

                                    @foreach ($classes as $class)
                                        <tr class="text-center">
                                            <td>{{ $class->id }}</td>
                                            <td width="18%" class="text-left">
                                                <p class="text-primary mt-0 mb-1 font-weight-bold" href="">
                                                    {{ $class->title }}
                                                </p>

                                            <td>{{ count($class->enrollments) }}</td>
                                            <td class="font-12">{{ $class->created_at }}
                                            </td>

                                            <td class="font-12">{{ $class->updated_at }}
                                            </td>


                                            <td width="150">
                                                <div class="btn-group dropdown table-actions">
                                                    <button type="button" class="btn-transparent dropdown-toggle"
                                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="fa fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dropdown-menu text-left webinars-lists-dropdown">


                                                        <a href="{{ getAdminPanelUrl() }}/classes/{{ $class->id }}/students"
                                                            target="_self"
                                                            class="d-flex align-items-center text-dark text-decoration-none btn-transparent btn-sm text-primary mt-1 "
                                                            title="{{ trans('admin/main.students') }}">
                                                            <i class="fa fa-users"></i>
                                                            <span class="ml-2">{{ trans('admin/main.students') }}</span>
                                                        </a>




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


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">إنشاء دفعة جديدة</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="/admin/classes" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title">عنوان الدفعة الدراسية</label>
                        <input type="text" name="title" id="title" class="form-control">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary ml-3" data-dismiss="modal">الغاء</button>
                    <button type="submit" class="btn btn-danger" id="confirmAction">حفظ</button>
                </div>

            </form>
        </div>
    </div>
</div>

@push('scripts_bottom')
@endpush
