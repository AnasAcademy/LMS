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

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{ trans('admin/main.total_students') }}</h4>
                        </div>
                        <div class="card-body">
                            {{ $totalStudents }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                                <input name="email" type="text" class="form-control"
                                    value="{{ request()->get('email') }}">
                            </div>
                        </div>


                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">اسم الطالب</label>
                                <input
                                    name={{ request()->is(getAdminPanelUrl('/students/courses', false)) ? 'full_name':'ar_name'  }}
                                    type="text" class="form-control"
                                    value="{{ request()->get('full_name') }}{{ request()->get('ar_name') }}">
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
    </div>

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
                        <th> الدورات المسجلة</th>
                        <th>{{ trans('admin/main.register_date') }}</th>
                        <th>{{ trans('admin/main.status') }}</th>
                        <th width="120">{{ trans('admin/main.actions') }}</th>
                    </tr>

                    @foreach ($users as $index => $user)
                        <tr>
                            <td>{{ ++$index }}</td>
                            <td>{{ $user->user_code }}</td>

                            <td class="text-left">
                                <div class="d-flex align-items-center">
                                    <figure class="avatar mr-2">
                                        <img src="{{ $user->getAvatar() }}"
                                            alt="{{ $user->student ? $user->student->ar_name : null }}">
                                    </figure>
                                    <div class="media-body ml-1">
                                        <div class="mt-0 mb-1 font-weight-bold">
                                            {{ $user->student ? $user->student->ar_name : $user->full_name }}</div>

                                        @if ($user->mobile)
                                            <div class="text-primary text-left font-600-bold" style="font-size:12px;">
                                                {{ $user->mobile }}</div>
                                        @endif

                                        @if ($user->email)
                                            <div class="text-primary text-small font-600-bold">{{ $user->email }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td>

                                @foreach ($user->webinarsEnrollments as $enrollment)

                                    {{ $enrollment->title }}
                                    @if (!$loop->last)
                                        &nbsp;و&nbsp;
                                    @endif
                                @endforeach
                            </td>

                            {{-- <td>
                                {{ !empty($user->student) ? 'تم حجز مقعد' : 'لم يتم حجز مقعد' }}
                            </td> --}}
                            {{-- <td>
                                {{ $user->user_code }}
                            </td> --}}

                            <td>
                                @foreach ($user->purchasedBundles() as $purchasedBundle)
                                    {{ dateTimeFormat($purchasedBundle->created_at, 'j M Y | H:i') }}
                                    @if (!$loop->last)
                                        &nbsp;و&nbsp;
                                    @endif
                                @endforeach
                            </td>

                            <td>
                                @if ($user->ban and !empty($user->ban_end_at) and $user->ban_end_at > time())
                                    <div class="mt-0 mb-1 font-weight-bold text-danger">{{ trans('admin/main.ban') }}
                                    </div>
                                    <div class="text-small font-600-bold">Until
                                        {{ dateTimeFormat($user->ban_end_at, 'Y/m/j') }}</div>
                                @else
                                    <div
                                        class="mt-0 mb-1 font-weight-bold {{ $user->status == 'active' ? 'text-success' : 'text-warning' }}">
                                        {{ trans('admin/main.' . $user->status) }}</div>
                                @endif
                            </td>

                            <td class="text-center mb-2" width="120">
                                {{-- @can('admin_users_transform')
                                    @if (!empty($user->student))
                                        @include('admin.includes.confirm_transform_button', [
                                            'url' => getAdminPanelUrl() . '/users/' . $user->id . '/transform',
                                            'btnClass' => 'btn-transparent  text-primary',
                                            'btnText' => '<i class="fa fa-retweet"></i>',
                                            'hideDefaultClass' => true,
                                            'id' => $user->id,
                                        ])
                                    @endif
                                @endcan --}}

                                @can('admin_users_impersonate')
                                    <a href="{{ getAdminPanelUrl() }}/users/{{ $user->id }}/impersonate" target="_blank"
                                        class="btn-transparent  text-primary" data-toggle="tooltip" data-placement="top"
                                        title="{{ trans('admin/main.login') }}">
                                        <i class="fa fa-user-shield"></i>
                                    </a>
                                @endcan

                                @can('admin_users_edit')
                                    <a href="{{ getAdminPanelUrl() }}/users/{{ $user->id }}/edit"
                                        class="btn-transparent  text-primary" data-toggle="tooltip" data-placement="top"
                                        title="{{ trans('admin/main.edit') }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                @endcan

                                @can('admin_users_delete')
                                    @include('admin.includes.delete_button', [
                                        'url' => getAdminPanelUrl() . '/users/' . $user->id . '/delete',
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
            {{ $users->appends(request()->input())->links() }}
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

@php
    $bundlesByCategory = [];
    foreach ($category as $item) {
        $bundlesByCategory[$item->id] = $item->bundles;
    }
@endphp

@push('scripts_bottom')
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>

    <script>
        var undefinedActiveSessionLang = '{{ trans('webinars.undefined_active_session') }}';
        var saveSuccessLang = '{{ trans('webinars.success_store') }}';
        var selectChapterLang = '{{ trans('update.select_chapter') }}';
    </script>

    <script src="/assets/default/js/panel/make_next_session.min.js"></script>
    {{-- bundle toggle and education section toggle --}}
    <script>
        function toggleHiddenInput(event) {
            var bundles = @json($bundlesByCategory);

            let selectInput = event.target;
            let myForm = selectInput.closest('form');
            let hiddenInput = myForm.bundle_id;
            let certificateSection = myForm.certificate_section;

            if (selectInput.value && hiddenInput) {
                var categoryId = selectInput.value;
                var categoryBundles = bundles[categoryId];

                if (categoryBundles) {
                    var options = categoryBundles.map(function(bundle) {
                        var isSelected = bundle.id == "{{ old('toDiploma', $student->bundle_id ?? null) }}" ?
                            'selected' : '';
                        return `<option value="${bundle.id}" ${isSelected} has_certificate="${bundle.has_certificate}">${bundle.title}</option>`;
                    }).join('');

                    hiddenInput.outerHTML =
                        '<select id="bundle_id" name="toDiploma"  class="form-control" onchange="CertificateSectionToggle(event)" required>' +
                        '<option value="" class="placeholder" disabled="" selected="selected">اختر التخصص الذي تود دراسته في اكاديمية انس للفنون</option>' +
                        options +
                        '</select>';
                }
            }
        }
    </script>


    {{-- Certificate Section Toggle --}}
    <script>
        function CertificateSectionToggle(event) {

            let myForm = event.target.closest('form');

            let certificateSection = myForm.querySelector("#certificate_section");
            let bundleSelect = myForm.querySelector("#bundle_id");
            // Get the selected option
            var selectedOption = bundleSelect.options[bundleSelect.selectedIndex];
            if (selectedOption.getAttribute('has_certificate') == 1) {
                certificateSection.classList.remove("d-none");
            } else {
                certificateSection.classList.add("d-none");

            }
        }

        function showCertificateMessage(event) {
            let myForm = event.target.closest('form');
            let messageSection = myForm.querySelector("#certificate_message");
            let certificateOption = myForm.querySelector("input[name='certificate']:checked");
            if (certificateOption.value === "1") {
                messageSection.innerHTML = "سوف يحصل على خصم 23%"
            } else if (certificateOption.value === "0") {
                messageSection.innerHTML = "بيفوته الحصول علي خصم 23%"

            } else {
                messageSection.innerHTML = ""

            }
        }
    </script>
@endpush
