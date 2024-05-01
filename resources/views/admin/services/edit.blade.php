@extends('admin.layouts.app')

@push('libraries_top')
@endpush

@section('content')

@endsection


@push('scripts_bottom')
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>

    <script>
        var undefinedActiveSessionLang = '{{ trans('webinars.undefined_active_session') }}';
        var saveSuccessLang = '{{ trans('webinars.success_store') }}';
        var selectChapterLang = '{{ trans('update.select_chapter') }}';
    </script>
    <script src="/assets/default/js/panel/make_next_session.min.js"></script>
@endpush
