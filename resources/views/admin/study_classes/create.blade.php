<button
    class="@if (empty($hideDefaultClass) or !$hideDefaultClass) {{ !empty($noBtnTransparent) ? '' : 'btn-transparent' }} text-primary @endif {{ $btnClass ?? '' }}"
    data-toggle="modal" data-target={{ '#confirmModal' . $class->id ?? 0 }} data-confirm-href="{{ $url }}"
    data-confirm-text-yes="{{ trans('admin/main.yes') }}" data-confirm-text-cancel="{{ trans('admin/main.cancel') }}"
    data-confirm-has-message="true">
    @if (!empty($btnText))
        {!! $btnText !!}
    @else
        <i class="fa {{ !empty($btnIcon) ? $btnIcon : 'fa-times' }}" aria-hidden="true"></i>
    @endif
</button>

<!-- Modal -->
<div class="modal fade" id={{ 'confirmModal' . $class->id ?? 0 }} tabindex="-1" aria-labelledby="confirmModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                @if (isset($class->id))
                    <h5 class="modal-title" id="confirmModalLabel">تعديل دفعة </h5>
                @else
                    <h5 class="modal-title" id="confirmModalLabel">إنشاء دفعة جديدة</h5>
                @endif

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="/admin/classes" method="post" class="modal-body">
                @csrf
                <div class="">
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
