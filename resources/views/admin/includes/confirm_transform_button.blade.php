<button class="@if(empty($hideDefaultClass) or !$hideDefaultClass) {{ !empty($noBtnTransparent) ? '' : 'btn-transparent' }} text-primary @endif {{ $btnClass ?? '' }}"
        data-toggle="modal" data-target={{"#confirmModal".$id}}
        data-confirm-href="{{ $url }}"
        data-confirm-text-yes="{{ trans('admin/main.yes') }}"
        data-confirm-text-cancel="{{ trans('admin/main.cancel') }}"
        data-confirm-has-message="true"
>
    @if(!empty($btnText))
        {!! $btnText !!}
    @else
        <i class="fa {{ !empty($btnIcon) ? $btnIcon : 'fa-times' }}" aria-hidden="true"></i>
    @endif
</button>

<!-- Modal -->
<div class="modal fade" id={{"confirmModal".$id}} tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true" data-confirm-href="{{ $url }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">تحويل الدبلومة</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="modal-body" method="post" action="{{ $url }}" id="form{{$id}}">
                @csrf
                @php
                    $purchasedFormBundles = $user->purchasedFormBundle();
                @endphp
                <label class="input-label">محول من برنامج :</label>
                <select class="form-control" name="fromDiploma" id="diploma1">
                    @foreach ($purchasedFormBundles as $bundleSale)
                        @php
                            $bundle = optional($bundleSale->bundle);
                        @endphp
                        @if ($bundle)
                            <option value="{{ $bundle->id }}">
                                {{ $bundle->title }}
                            </option>
                        @endif
                    @endforeach
                </select><br>
                @error('category_id')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
                <label class="input-label">تحويل الي برنامج :</label><br>
                <div class="container_form mt-25">
                    {{-- diploma --}}
                    <div class="form-group">
                        <label for="application"
                            class="form-label">{{ trans('application_form.application') }}*</label>
                        <select id="mySelect{{$id}}" name="category_id" required
                            class="form-control" onchange="toggleHiddenInput(event)">
                            <option disabled selected hidden value="">اختر
                                الدرجة العلمية التي تريد دراستها في اكاديمية انس
                                للفنون </option>
                            @foreach ($category as $item)
                                <option value="{{ $item->id }}"
                                    {{ old('category_id', $user->student->category_id ?? null) == $item->id ? 'selected' : '' }}>
                                    {{ $item->title }} </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- specialization --}}
                    <div class="form-group">
                        <label class="hidden-element" id="hiddenLabel1"
                            for="name">
                            {{ trans('application_form.specialization') }}*
                        </label>
                        <input type="text" id="bundle_id" name="toDiploma"
                            required class="hidden-element form-control"
                            value="{{ old('toDiploma', $user->student ? $user->student->bundle_id : '') }}">
                        @error('toDiploma')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- certificate --}}
                    <div class="form-group col-12  d-none"
                        id="certificate_section">
                        <label>{{ trans('application_form.want_certificate') }} ؟
                            *</label>
                        <span class="text-danger font-12 font-weight-bold"
                            id="certificate_message"> </span>
                        @error('certificate')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                        <div class="row mr-5 mt-3">
                            {{-- want certificate --}}
                            <div class="col-sm-4 col">
                                <label for="want_certificate">
                                    <input type="radio" id="want_certificate"
                                        name="certificate" value="1"
                                        onchange="showCertificateMessage()"
                                        class=" @error('certificate') is-invalid @enderror"
                                        {{ old('certificate', $user->student->certificate ?? null) === '1' ? 'checked' : '' }}>
                                    نعم
                                </label>
                            </div>

                            {{-- does not want certificate --}}
                            <div class="col">
                                <label for="doesn't_want_certificate">
                                    <input type="radio"
                                        id="doesn't_want_certificate"
                                        name="certificate"
                                        onchange="showCertificateMessage()"
                                        value="0"
                                        class="@error('certificate') is-invalid @enderror"
                                        {{ old('certificate', $user->student->certificate ?? null) === '0' ? 'checked' : '' }}>
                                    لا
                                </label>
                            </div>
                        </div>
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
        function toggleHiddenInput() {
            var bundles = @json($bundlesByCategory);
            var select = document.getElementById("mySelect1");
            var hiddenInput = document.getElementById("bundle_id");
            var hiddenLabel = document.getElementById("hiddenLabel1");
            let education = document.getElementById("education");
            let high_education = document.getElementsByClassName("high_education");
            let secondary_education = document.getElementsByClassName("secondary_education");
            let certificateSection = document.getElementById("certificate_section");
            if (select.value && hiddenLabel && hiddenInput) {
                var categoryId = select.value;
                var categoryBundles = bundles[categoryId];

                if (categoryBundles) {
                    var options = categoryBundles.map(function(bundle) {
                        var isSelected = bundle.id == "{{ old('toDiploma', $student->bundle_id ?? null) }}" ?
                            'selected' : '';
                        return `<option value="${bundle.id}" ${isSelected} has_certificate="${bundle.has_certificate}">${bundle.title}</option>`;
                    }).join('');

                    hiddenInput.outerHTML =
                        '<select id="bundle_id" name="toDiploma"  class="form-control" onchange="CertificateSectionToggle()" required>' +
                        '<option value="" class="placeholder" disabled="" selected="selected">اختر التخصص الذي تود دراسته في اكاديمية انس للفنون</option>' +
                        options +
                        '</select>';
                    hiddenLabel.style.display = "block";

                } else {
                    hiddenInput.outerHTML =
                        '<input type="text" id="bundle_id" name="toDiploma" placeholder="ادخل الإسم باللغه العربية فقط"  class="hidden-element form-control">';
                    hiddenLabel.style.display = "none";
                }
                var selectedOption = select.options[select.selectedIndex];
                var selectedText = selectedOption.textContent;


            }
        }
        toggleHiddenInput();
    </script>


    {{-- Certificate Section Toggle --}}
    <script>
        function CertificateSectionToggle() {
            let certificateSection = document.getElementById("certificate_section");
            let bundleSelect = document.getElementById("bundle_id");
            // Get the selected option
            var selectedOption = bundleSelect.options[bundleSelect.selectedIndex];
            if (selectedOption.getAttribute('has_certificate') == 1) {
                certificateSection.classList.remove("d-none");
            } else {
                certificateSection.classList.add("d-none");

            }
        }

        function showCertificateMessage() {
            let messageSection = document.getElementById("certificate_message");
            let certificateOption = document.querySelector("input[name='certificate']:checked");
            if (certificateOption.value === "1") {
                messageSection.innerHTML = "سوف تحصل على خصم 23%"
            } else if (certificateOption.value === "0") {
                messageSection.innerHTML = "بيفوتك الحصول علي خصم 23%"

            } else {
                messageSection.innerHTML = ""

            }
        }

        showCertificateMessage();


        CertificateSectionToggle();
    </script>
@endpush
