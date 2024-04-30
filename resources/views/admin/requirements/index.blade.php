@extends('admin.layouts.app')


@section('content')
    <section class="section">
        <div class="section-header">
            <h1>قائمة بمتطلبات القبول</h1>
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
        @error('message')
            <div class="container d-flex justify-content-center mt-80">
                <p class="alert alert-danger w-75 text-center fs-3"> {{ 'يرجي تسجيل سبب الرفض ' }} </p>
            </div>
        @enderror


        <div class="section-body">

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped font-14 ">
                                    <tr>
                                        <th>{{ 'Index' }}</th>
                                        <th class="text-left">{{ 'كود الطالب' }}</th>
                                        <th class="text-left">{{ 'اسم الطالب' }}</th>
                                        <th>{{ 'البرنامج المسجل اليه' }}</th>
                                        <th>{{ 'التخصص' }}</th>
                                        <th>{{ 'مرفق الهوية' }}</th>
                                        <th>{{ 'مرفق متطلبات القبول' }}</th>
                                        <th>{{ 'حالة الطلب' }}</th>
                                        <th>{{ 'الأدمن' }}</th>
                                        <th>{{ 'تاريخ ارسال الطلب' }}</th>

                                        <th width="120">{{ 'الأجراءات' }}</th>
                                    </tr>
                                    @foreach ($requirements as $index => $requirement)
                                        <tr class="text-center">
                                            <td>{{ ++$index }}</td>
                                            <td class="text-left">{{ $requirement->bundleStudent->student->registeredUser->user_code }}
                                            </td>
                                            <td class="text-left">
                                                <div class="d-flex align-items-center">
                                                    <figure class="avatar mr-2">
                                                        <img src="{{ $requirement->bundleStudent->student->registeredUser->getAvatar() }}" alt="{{ $requirement->bundleStudent->student->registeredUser->full_name }}">
                                                    </figure>
                                                    <div class="media-body ml-1">
                                                        <div class="mt-0 mb-1 font-weight-bold">{{ $requirement->bundleStudent->student ? $requirement->bundleStudent->student->ar_name : $requirement->bundleStudent->student->registeredUser->full_name }}
                                                        </div>

                                                        @if ($requirement->bundleStudent->student->registeredUser->mobile)
                                                            <div class="text-primary text-small font-600-bold">{{ $requirement->bundleStudent->student->registeredUser->mobile }}</div>
                                                        @endif

                                                        @if ($requirement->bundleStudent->student->registeredUser->email)
                                                            <div class="text-primary text-small font-600-bold">{{ $requirement->bundleStudent->student->registeredUser->email }}</div>
                                                        @endif
                                                    </div>
                                                </div>

                                            </td>


                                            <td>{{ $requirement->bundleStudent->bundle->category->slug }}</td>

                                            <td>{{ $requirement->bundleStudent->bundle->title }}</td>

                                            <td>
                                                <a href="/store/{{ $requirement->identity_attachment }}" target="_blank">
                                                    @if (pathinfo($requirement->identity_attachment, PATHINFO_EXTENSION) != 'pdf')
                                                        <img src="/store/{{ $requirement->identity_attachment }}"
                                                            alt="identity_attachment" width="100px">
                                                    @else
                                                        pdf ملف <i class="fas fa-file font-20"></i>

                                                    @endif
                                                </a>
                                            </td>

                                            <td>
                                                <a href="/store/{{ $requirement->admission_attachment }}" target="_blank" class="text-black">
                                                    pdf ملف <i class="fas fa-file font-20"></i>
                                                </a>
                                            </td>

                                            <td>
                                                @if ($requirement->status=="pending")
                                                <span class="text-success"> معلق</span>
                                                @elseif($requirement->status=="approved")
                                                <span class="text-primary"> تم الموافقة عليه</span>
                                                @elseif($requirement->status=="rejected")
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
                                                            'message' => $requirement->message,
                                                            'id' => $requirement->id,
                                                        ])
                                                </div>
                                                @endif
                                            </td>

                                            <td>{{ $requirement->admin ? $requirement->admin->full_name : '' }}
                                            </td>

                                            <td class="font-12">
                                                {{ (Carbon\Carbon::parse($requirement->created_at))
                                                    ->translatedFormat(handleDateAndTimeFormat('Y M j | H:i')) }}</td>

                                            {{-- actions --}}
                                            <td width="200" class="">

                                                <div class="d-flex justify-content-center align-items-baseline gap-3">
                                                    @can('admin_requirements_approve')
                                                        {{-- <a href="{{getAdminPanelUrl().'/requirements/'.$requirement->id.'/approve'}}" class="btn btn-primary d-flex align-items-center btn-sm mt-1"> <i class="fa fa-check"></i><span class="ml-2"> قبول</a> --}}

                                                        {{-- <a href="{{getAdminPanelUrl().'/requirements/'.$requirement->id.'/reject'}}" class="btn btn-danger d-flex align-items-center btn-sm mt-1"> <i class="fa fa-check"></i><span class="ml-2"> رفض</a> --}}
                                                        @include('admin.includes.delete_button', [
                                                            'url' =>
                                                                getAdminPanelUrl() .
                                                                '/requirements/' .
                                                                $requirement->id .
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
                                                    @can('admin_requirements_reject')
                                                        @include('admin.includes.confirm_delete_button', [
                                                            'url' =>
                                                                getAdminPanelUrl() .
                                                                '/requirements/' .
                                                                $requirement->id .
                                                                '/reject',
                                                            'btnClass' =>
                                                                'btn btn-danger d-flex align-items-center btn-sm mt-1',
                                                            'btnText' =>
                                                                '<i class="fa fa-times"></i><span class="ml-2">' .
                                                                trans('admin/main.reject') .
                                                                '</span>',
                                                            'hideDefaultClass' => true,
                                                            'id' => $requirement->id
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
                            {{ $requirements->links() }}
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
