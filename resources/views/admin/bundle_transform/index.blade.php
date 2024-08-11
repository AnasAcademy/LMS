@extends('admin.layouts.app')


@php
    $filters = request()->getQueryString();
@endphp

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.transforms') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.transforms') }}</div>
            </div>
        </div>

        <div class="section-body">


            {{-- search --}}
            <section class="card">
                <div class="card-body">
                    <form method="get" class="mb-0">

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">كود الطالب</label>
                                    <input name='user_code' type="text" class="form-control"
                                        value="{{ request()->get('user_code') }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">اسم الطالب</label>
                                    <input name='user_name' type="text" class="form-control"
                                        value="{{ request()->get('user_name') }}">
                                </div>
                            </div>


                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">بريد الطالب</label>
                                    <input name="email" type="text" class="form-control"
                                        value="{{ request()->get('email') }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">نوع التحويل</label>
                                    <select name="type" data-plugin-selectTwo class="form-control populate">
                                        <option value="">{{ trans('admin/main.all_status') }}</option>
                                        <option value="form_fee" @if (request()->get('type') == 'form_fee') selected @endif>
                                            رسوم حجز مقعد
                                        </option>
                                        <option value="bundle" @if (request()->get('type') == 'bundle') selected @endif>
                                            دفع كامل الرسوم
                                        </option>
                                        <option value="upfront" @if (request()->get('type') == 'upfront') selected @endif>
                                            قسط التسجيل
                                        </option>

                                        <option value="installment_payment"
                                            @if (request()->get('type') == 'installment_payment') selected @endif>
                                            اقساط
                                        </option>
                                        <option value="webinar" @if (request()->get('type') == 'webinar') selected @endif>
                                            دورة
                                        </option>
                                        <option value="service" @if (request()->get('type') == 'service') selected @endif>
                                            خدمات الكترونية
                                        </option>
                                        <option value="scholarship" @if (request()->get('type') == 'scholarship') selected @endif>
                                            منح دراسية
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">عنوان البرنامج</label>
                                    <input name="bundle_title" class="form-control"  value="{{ request()->get('bundle_title') }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.status') }}</label>
                                    <select name="status" data-plugin-selectTwo class="form-control populate">
                                        <option value="">{{ trans('admin/main.all_status') }}</option>
                                        <option value="success" @if (request()->get('status') == 'success') selected @endif>
                                            {{ trans('admin/main.success') }}</option>
                                        <option value="refund" @if (request()->get('status') == 'refund') selected @endif>
                                            {{ trans('admin/main.refund') }}</option>
                                        {{--
                                        <option value="blocked" @if (request()->get('status') == 'blocked') selected @endif>
                                            {{ trans('update.access_blocked') }}</option>
                                             --}}
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 row">

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="input-label">{{ trans('admin/main.start_date') }}</label>
                                        <div class="input-group">
                                            <input type="date" id="from" class="text-center form-control"
                                                name="from" value="{{ request()->get('from') }}"
                                                placeholder="Start Date">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="input-label">{{ trans('admin/main.end_date') }}</label>
                                        <div class="input-group">
                                            <input type="date" id="to" class="text-center form-control"
                                                name="to" value="{{ request()->get('to') }}"
                                                placeholder="End Date">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mt-1">
                                        <label class="input-label mb-4"> </label>
                                        <input type="submit" class="text-center btn btn-primary w-100"
                                            value="{{ trans('admin/main.show_results') }}">
                                    </div>
                                </div>
                            </div>

                        </div>

                    </form>
                </div>
            </section>

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            @can('admin_transforms_export')
                                <a href="{{ getAdminPanelUrl() }}/financial/transforms/export?{{ $filters }}"
                                    class="btn btn-primary">{{ trans('admin/main.export_xls') }}</a>
                            @endcan
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped font-14">
                                    <tr>
                                        <th>#</th>
                                        <th class="text-left">{{ trans('admin/main.student') }}</th>

                                        <th>اسم البرنامج المراد التحويل منه</th>
                                        <th>اسم البرنامج المراد التحويل إليه</th>
                                        <th>{{ trans('admin/main.transform_type') }}</th>
                                        <th>المبلغ</th>
                                        <th>{{ trans('admin/main.date') }}</th>
                                        <th>{{ trans('admin/main.status') }}</th>
                                        <th width="120">{{ trans('admin/main.actions') }}</th>
                                    </tr>

                                    @foreach ($transforms as $index => $transform)
                                        <tr>
                                            <td>{{ ++$index }}</td>

                                            <td class="text-left">
                                                {{ !empty($transform->user) ? $transform->user->full_name : '' }}
                                                <div class="text-primary text-small font-600-bold">
                                                    {{ !empty($transform->user) ? $transform->user->email : '' }}</div>

                                                <div class="text-primary text-small font-600-bold">ID :
                                                    {{ !empty($transform->user) ? $transform->user->id : '' }}</div>
                                                <div class="text-primary text-small font-600-bold">Code :
                                                    {{ !empty($transform->user) ? $transform->user->user_code : '' }}</div>

                                            </td>

                                            <td class="text-left">
                                                {{ $transform->fromBundle->title }}
                                                <div class="text-primary text-small font-600-bold">ID :
                                                    {{ $transform->from_bundle_id }}</div>
                                            </td>
                                            <td class="text-left">
                                                {{ $transform->toBundle->title }}
                                                <div class="text-primary text-small font-600-bold">ID :
                                                    {{ $transform->to_bundle_id }}</div>
                                            </td>
                                            <td class="text-left">
                                                {{ $transform->type }}
                                            </td>

                                            <td>
                                                <span class="">{{ handlePrice($transform->amount ?? 0) }}</span>
                                            </td>

                                            <td>{{ dateTimeFormat($transform->created_at, 'j F Y H:i') }}</td>

                                            <td class="text-left">
                                                {{ $transform->status }}
                                            </td>

                                            {{-- <td>
                                                @if (!empty($transform->refund_at))
                                                    <span class="text-warning">{{ trans('admin/main.refund') }}</span>
                                                    @include('admin.includes.message_button', [
                                                                    'url' => '#',
                                                                    'btnClass' => 'd-flex align-items-center mt-1',
                                                                    'btnText' =>
                                                                        '<span class="ml-2">' .
                                                                        ' سبب الإستيرداد</span>',
                                                                    'hideDefaultClass' => true,
                                                                    'deleteConfirmMsg' => 'سبب  طلب الإستيرداد',
                                                                    'message' => $transform->message,
                                                                    'id' => $transform->id,
                                                                ])
                                                @elseif(!$transform->access_to_purchased_item)
                                                    <span class="text-danger">{{ trans('update.access_blocked') }}</span>
                                                @else
                                                    <span class="text-success">{{ trans('admin/main.success') }}</span>
                                                @endif
                                            </td> --}}


                                        </tr>
                                    @endforeach

                                </table>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            {{ $transforms->appends(request()->input())->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
