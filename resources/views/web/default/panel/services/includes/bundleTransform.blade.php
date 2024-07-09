@extends(getTemplate() . '.panel.layouts.panel_layout')



@section('content')
    <!-- Modal -->
    <div class="" id='confirmModal' tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel"> طلب تحويل من دبلومة </h5>
                </div>
                <form class="modal-body" method="post" action="/panel/services/{{ $service->id }}/bundleTransform">
                    @csrf
                    @php
                        $user = auth()->user();
                        $purchasedFormBundles = $user->purchasedFormBundle();
                    @endphp
                    <label class="input-label">محول من برنامج :</label>
                    <select class="form-control" name="from_bundle" id="diploma1">
                        <option value="" class="placeholder" disabled selected>اختر التخصص الذي تود التحويل منه </option>
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
                    @error('from_bundle')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                    <label class="input-label">تحويل الي برنامج :</label><br>
                    <div class="container_form mt-25">
                        {{-- diploma --}}
                        <div class="form-group">
                            <label for="application" class="form-label">{{ trans('application_form.application') }}*</label>
                            <select id="mySelect" name="category" required class="form-control"
                                onchange="toggleHiddenInput()">
                                <option selected hidden value="">اختر
                                    الدرجة العلمية التي تريد دراستها في اكاديمية انس
                                    للفنون </option>
                                @foreach ($category as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('category') == $item->id ? 'selected' : '' }}>
                                        {{ $item->title }} </option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- specialization --}}
                        <div class="form-group">
                            <label class="hidden-element" id="hiddenLabel1" for="name">
                                {{ trans('application_form.specialization') }}*
                            </label>
                            <input type="text" id="bundle_id" name="to_bundle" required
                                class="hidden-element form-control"
                                value="{{ old('to_bundle') }}">
                            @error('to_bundle')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- certificate --}}
                        <div class="form-group col-12  d-none" id="certificate_section">
                            <label style="width: auto">{{ trans('application_form.want_certificate') }} ؟
                                *</label>
                            <span class="text-danger font-12 font-weight-bold" id="certificate_message"> </span>
                            @error('certificate')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="row mr-5 mt-3">
                                {{-- want certificate --}}
                                <div class="col-sm-4 col">
                                    <label for="want_certificate">
                                        <input type="radio" id="want_certificate" name="certificate" value="1"
                                            onchange="showCertificateMessage()"
                                            class=" @error('certificate') is-invalid @enderror"
                                            {{ old('certificate', $user->student->certificate ?? null) === '1' ? 'checked' : '' }}>
                                        نعم
                                    </label>
                                </div>

                                {{-- does not want certificate --}}
                                <div class="col">
                                    <label for="doesn't_want_certificate">
                                        <input type="radio" id="doesn't_want_certificate" name="certificate"
                                            onchange="showCertificateMessage()" value="0"
                                            class="@error('certificate') is-invalid @enderror"
                                            {{ old('certificate', $user->student->certificate ?? null) === '0' ? 'checked' : '' }}>
                                        لا
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">

                        <button type="submit" class="btn btn-danger" id="confirmAction">ارسال</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@php
    $bundlesByCategory = [];
    foreach ($category as $item) {
        $bundlesByCategory[$item->id] = $item->bundles;
    }
@endphp
@push('scripts_bottom')
    {{-- bundle toggle and education section toggle --}}
    <script>
        function toggleHiddenInput() {
            var bundles = @json($bundlesByCategory);

            let selectInput = document.getElementById("mySelect");

            let myForm = selectInput.closest('form');
            let hiddenInput = myForm.bundle_id;
            let certificateSection = myForm.certificate_section;
            var hiddenLabel = document.getElementById("hiddenLabel1");
            if (selectInput.value && hiddenInput) {
                var categoryId = selectInput.value;
                var categoryBundles = bundles[categoryId];

                if (categoryBundles) {
                    console.log(selectInput);
                    var options = categoryBundles.map(function(bundle) {
                        var isSelected = bundle.id == "{{ old('to_bundle', $student->bundle_id ?? null) }}" ?
                            'selected' : '';
                        return `<option value="${bundle.id}" ${isSelected} has_certificate="${bundle.has_certificate}">${bundle.title}</option>`;
                    }).join('');

                    hiddenInput.outerHTML =
                        '<select id="bundle_id" name="to_bundle"  class="form-control" onchange="CertificateSectionToggle()" required>' +
                        '<option value="" class="placeholder" disabled="" selected="selected">اختر التخصص الذي تود دراسته في اكاديمية انس للفنون</option>' +
                        options +
                        '</select>';


                        hiddenLabel.style.display = "block";
                    hiddenLabel.closest('div').classList.remove('d-none');
                }
            } else {
                hiddenInput.outerHTML =
                    '<select id="bundle_id" name="to_bundle"  class="form-control" onchange="CertificateSectionToggle()" >' +
                    '<option value="" class="placeholder" selected hidden >اختر التخصص الذي تود دراسته في اكاديمية انس للفنون</option> </select>';
                hiddenLabel.style.display = "none";
                hiddenLabel.closest('div').classList.add('d-none');
            }
        }
        toggleHiddenInput();
    </script>


    {{-- Certificate Section Toggle --}}
    <script>
        function CertificateSectionToggle() {

             let certificateSection = document.getElementById("certificate_section");

            let bundleSelect = document.getElementById("bundle_id");
            // let certificateInputs = document.querySelectorAll("input[name='certificate']");

            // let myForm = event.target.closest('form');

            // let certificateSection = myForm.querySelector("#certificate_section");
            // let bundleSelect = myForm.querySelector("#bundle_id");
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
                messageSection.innerHTML = "سوف يحصل على خصم 23%"
            } else if (certificateOption.value === "0") {
                messageSection.innerHTML = "بيفوته الحصول علي خصم 23%"

            } else {
                messageSection.innerHTML = ""

            }
        }
        CertificateSectionToggle();
        showCertificateMessage();
    </script>
@endpush
