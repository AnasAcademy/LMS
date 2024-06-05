@extends(getTemplate() . '.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/owl-carousel2/owl.carousel.min.css">
    <style>
        .container_form {
            margin-top: 20px;
            /* border: 1px solid #ddd; */
            /* Add border to the container */
            padding: 20px;
            /* Optional: Add padding for spacing */
            border-radius: 10px !important;
            box-shadow: 2px 5px 10px #ddd;
            margin: 60px auto;
        }

        .hidden-element {
            display: none;
        }

        .application {
            display: flex;
            flex-direction: column;
            align-content: stretch;
            justify-content: flex-start;
            align-items: center;
            flex-wrap: wrap;
        }

        .section1 .form-title {
            text-align: center !important;
            padding: 10px;
            color: #5F2B80;
        }

        a {
            color: #ED1088;
        }

        .form-main-title {
            font-family: 'Inter';
            font-style: normal;
            font-weight: 400;
            font-size: 32px;
            line-height: 39px;
            color: #5E0A83;
        }

        .form-title {


            font-family: 'IBM Plex Sans';
            font-style: normal;
            font-weight: 700;
            font-size: 32px;
            line-height: 42px;
            color: #000000;

        }

        input {
            text-align: right;
        }

        .main-section {
            background-color: #F6F7F8;
        }

        .main-container {
            border-width: 2px !important;
        }

        .secondary_education,
        .high_education,
        #education {
            display: none;
        }

        .hero {
            width: 100%;
            height: 80vh;
            /* background-color: #ED1088; */
            background-image: linear-gradient(90deg, #5E0A83 19%, #F70387 100%);
        }

        @media(max-width:768px) {
            .hero {
                height: 50vh;
            }

            footer img {
                width: 150px !important;
            }

            .img-cover {
                width: 100% !important;
            }
        }

        @media(max-width:576px) {
            .form-main-title {
                font-size: 25px;
            }


        }
    </style>
@endpush

@section('content')
    <div class="application container-fluid">
        <div class="col-12 col-lg-10 col-md-11 px-0">
            <div class="col-lg-12 col-md-12 px-0">
                <Section class="section1 main-section">
                    <h2 class="section-title">طلب تسجيل جديد</h2>
                    <div class="container_form">
                        <form action="/apply" method="POST" id="myForm">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">

                            {{-- application type --}}
                            <div class="form-group col-12 col-sm-6">
                                <label class="form-label">حدد نوع التقديم<span class="text-danger">*</span></label>
                                <select id="typeSelect" name="type" required
                                    class="form-control @error('type') is-invalid @enderror" onchange="toggleHiddenType()">
                                    <option disabled selected hidden value="">اختر نوع التقديم التي تريد دراستها في
                                        اكاديمية انس للفنون </option>
                                    <option value="diplomas" @if (old('type') == 'diplomas') selected @endif>
                                        دبلومات </option>
                                    <option value="courses" @if (old('type') == 'courses') selected @endif>دورات</option>
                                </select>

                                @error('type')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- course --}}
                            <div class="form-group col-12 col-sm-6">
                                <label for="application2" class="form-label" id="all_course">الدورات التدربيه<span
                                        class="text-danger">*</span></label>
                                <select id="mySelect2" name="webinar_id"
                                    class="form-control @error('webinar_id') is-invalid @enderror">
                                    <option  selected hidden value="">اختر الدورة التدربيه التي تريد دراستها
                                        في
                                        اكاديمية انس للفنون </option>

                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}">
                                            {{ $course->title }} </option>
                                    @endforeach

                                </select>

                                @error('webinar_id')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- diplomas --}}
                            <section class="d-none" id="diplomas_section">
                                {{-- diploma --}}
                                <div class="form-group col-12 col-sm-6">
                                    <label for="application" class="form-label"
                                        id="degree">{{ trans('application_form.application') }}<span
                                            class="text-danger">*</span></label>
                                    <select id="mySelect1" name="category_id"
                                        class="form-control @error('category_id') is-invalid @enderror"
                                        onchange="toggleHiddenInput()">
                                        <option selected value="">اختر الدرجة العلمية التي تريد دراستها في
                                            اكاديمية انس للفنون </option>
                                        @foreach ($category as $item)
                                            <option value="{{ $item->id }}" education= "{{ $item->education }}"
                                                {{ old('category_id', $student->category_id ?? null) == $item->id ? 'selected' : '' }}>
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
                                <div class="form-group col-12 col-sm-6 d-none">
                                    <label class="hidden-element" id="hiddenLabel1" for="bundle_id">
                                        {{ trans('application_form.specialization') }}<span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="bundle_id" name="bundle_id"
                                        class="hidden-element form-control @error('bundle_id') is-invalid @enderror"
                                        value="{{ old('bundle_id', $student ? $student->bundle_id : '') }}">

                                    @error('bundle_id')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="d-none font-14 font-weight-bold mb-10 col-12" id="early_enroll"
                                    style="color: #5F2B80;">
                                    يرجى ملاحظة أن التسجيل الرسمي سيبدأ في شهر يوليو المقبل. بمجرد فتح التسجيل، ستتمكن من
                                    استكمال رفع المتطلبات اللازمة وإتمام إجراءات التسجيل.
                                </div>

                                {{-- certificate --}}
                                <div class="form-group col-12  d-none" id="certificate_section">
                                    <label>{{ trans('application_form.want_certificate') }} ؟ <span
                                            class="text-danger">*</span></label>
                                    <span class="text-danger font-12 font-weight-bold" id="certificate_message"> </span>
                                    @error('certificate')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <div class="row mr-5 mt-5">
                                        {{-- want certificate --}}
                                        <div class="col-sm-4 col">
                                            <label for="want_certificate">
                                                <input type="radio" id="want_certificate" name="certificate"
                                                    value="1" onchange="showCertificateMessage()"
                                                    class=" @error('certificate') is-invalid @enderror"
                                                    {{ old('certificate', $student->certificate ?? null) === '1' ? 'checked' : '' }}>
                                                نعم
                                            </label>
                                        </div>

                                        {{-- does not want certificate --}}
                                        <div class="col">
                                            <label for="doesn't_want_certificate">
                                                <input type="radio" id="doesn't_want_certificate" name="certificate"
                                                    onchange="showCertificateMessage()" value="0"
                                                    class="@error('certificate') is-invalid @enderror"
                                                    {{ old('certificate', $student->certificate ?? null) === '0' ? 'checked' : '' }}>
                                                لا
                                            </label>
                                        </div>
                                    </div>
                                </div>

                            </section>

                            <label class="mt-30">
                                <input type="checkbox" id="terms" name="terms" required>
                                <!--{{ trans('application_form.agree_terms_conditions') }}-->
                                اقر أنا المسجل بياناتي اعلاه بموافقتي على لائحة الحقوق والوجبات واحكام وشروط
                                القبول
                                والتسجيل، كما أقر بالتزامي التام بمضمونها، وبمسؤوليتي التامة عن أية مخالفات قد
                                تصدر مني لها
                                ، مما يترتب عليه كامل الأحقية للاكاديمية في مسائلتي عن تلك المخالفات والتصرفات
                                المخالفة
                                للوائح المشار إليها في عقد اتفاقية التحاق متدربـ/ـة <a target="_blank"
                                    href="https://anasacademy.uk/wp-content/uploads/2024/02/Contract.pdf">انقر
                                    هنا
                                    لمشاهدة</a>

                            </label>
                            <button type="submit" class="btn btn-primary">{{ trans('application_form.submit') }}</button>
                        </form>
                    </div>


                    {{-- display errors --}}
                    {{-- @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif --}}
                </Section>
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


            if (select.value && hiddenLabel && hiddenInput) {

                var categoryId = select.value;
                var categoryBundles = bundles[categoryId];

                if (categoryBundles) {
                    var options = categoryBundles.map(function(bundle) {
                        var isSelected = bundle.id == "{{ old('bundle_id', $student->bundle_id ?? null) }}" ?
                            'selected' : '';
                        return `<option value="${bundle.id}" ${isSelected} has_certificate="${bundle.has_certificate}" early_enroll="${bundle.early_enroll}">${bundle.title}</option>`;
                    }).join('');

                    hiddenInput.outerHTML =
                        '<select id="bundle_id" name="bundle_id"  class="form-control" onchange="CertificateSectionToggle()" >' +
                        '<option value="" class="placeholder selected>اختر التخصص الذي تود دراسته في اكاديمية انس للفنون</option>' +
                        options +
                        '</select>';
                    hiddenLabel.style.display = "block";
                            hiddenLabel.closest('div').classList.remove('d-none');
                } else {
                    hiddenInput.outerHTML =
                        '<input type="text" id="bundle_id" name="bundle_id" placeholder="ادخل الإسم باللغه العربية فقط"  class="hidden-element form-control">';
                    hiddenLabel.style.display = "none";
                     hiddenLabel.closest('div').classList.add('d-none');
                }
            }
        }


        toggleHiddenInput();
    </script>


    {{-- type toggle --}}
    <script>
        function toggleHiddenType() {
            var select = document.getElementById("typeSelect");
            var hiddenDiplomaInput = document.getElementById("mySelect1");
            var hiddenDiplomaLabel = document.getElementById("degree");
            var hiddenBundleInput = document.getElementById("bundle_id");
            var hiddenDiplomaLabel1 = document.getElementById("hiddenLabel1");
            let certificateSection = document.getElementById("certificate_section");
            let diplomasSection = document.getElementById("diplomas_section");

            var hiddenCourseInput = document.getElementById("mySelect2");
            var hiddenCourseLabel = document.getElementById("all_course");

            if (select) {
                var type = select.value;

                if (type == 'diplomas') {
                    console.log(type);
                    diplomasSection.classList.remove('d-none');
                    hiddenCourseInput.closest('div').classList.add('d-none');
                } else if (type == 'courses') {
                    hiddenCourseInput.closest('div').classList.remove('d-none');
                    console.log(type);
                    console.log(hiddenDiplomaInput.options);
                    console.log(hiddenDiplomaInput.options[0]);
                    diplomasSection.classList.add('d-none');
                } else {
                    diplomasSection.classList.add('d-none');
                    hiddenCourseInput.closest('div').classList.add('d-none');
                }

                // if (type == 'diplomas') {
                //     hiddenDiplomaLabel.style.display = "block";
                //     hiddenDiplomaInput.style.display = "block";
                //     hiddenDiplomaInput.setAttribute('required','required')
                //     hiddenCourseInput.style.display = "none"
                //     hiddenCourseLabel.style.display = "none"

                //     // hiddenBundleInput.style.display = "block";
                //     // hiddenDiplomaLabel1.style.display = "block";

                // } else if (type == 'courses') {
                //     hiddenCourseInput.style.display = "block"
                //     hiddenCourseLabel.style.display = "block"
                //     hiddenDiplomaLabel.style.display = "none";
                //     hiddenDiplomaInput.style.display = "none";
                //     hiddenDiplomaInput.removeAttribute('required')
                //     hiddenBundleInput.removeAttribute('required')
                //     hiddenBundleInput.style.display = "none";
                //     hiddenDiplomaLabel1.style.display = "none";
                //     certificateSection.classList.add("d-none");
                //     hiddenDiplomaInput.options[0].setAttribute('selected','selected');
                //     hiddenBundleInput.outerHTML =
                //         '<input type="text" id="bundle_id" name="bundle_id" placeholder="ادخل الإسم باللغه العربية فقط"  class="hidden-element form-control">';
                //         hiddenDiplomaLabel1.style.display = "none";
                // } else {
                //     hiddenDiplomaLabel.style.display = "none";
                //     hiddenDiplomaInput.style.display = "none";
                //     hiddenDiplomaInput.removeAttribute('required')
                //     hiddenBundleInput.removeAttribute('required')
                //     hiddenCourseInput.style.display = "none"
                //     hiddenCourseLabel.style.display = "none"
                //     hiddenBundleInput.style.display = "none";
                //     hiddenDiplomaLabel1.style.display = "none";
                //     certificateSection.classList.add("d-none");
                //     hiddenDiplomaInput.options[0].setAttribute('selected','selected');
                //     hiddenBundleInput.options[0].setAttribute('selected','selected');
                // }
                toggleHiddenInput();
                CertificateSectionToggle();

            }
        }
        toggleHiddenType();
    </script>


    {{-- Certificate Section Toggle --}}
    <script>
        function CertificateSectionToggle() {
            let certificateSection = document.getElementById("certificate_section");
            let earlyEnroll = document.getElementById("early_enroll");
            let bundleSelect = document.getElementById("bundle_id");
            let certificateInputs = document.querySelectorAll("input[name='certificate']");

            console.log('index: ', bundleSelect.selectedIndex);
            console.log(bundleSelect.options[bundleSelect.selectedIndex]);
            console.log(bundleSelect);
            // Get the selected option
            var selectedOption = bundleSelect.options[bundleSelect.selectedIndex];
            if (selectedOption.getAttribute('has_certificate') == 1) {
                certificateSection.classList.remove("d-none");

                certificateInputs.forEach(function(element) {
                    element.setAttribute("required", "required");
                });
            } else {
                certificateSection.classList.add("d-none");

                certificateInputs.forEach(function(element) {
                    element.removeAttribute("required", "required");
                });

            }

            if (selectedOption.getAttribute('early_enroll') == 1) {

                earlyEnroll.classList.remove("d-none");

            } else {
                earlyEnroll.classList.add("d-none");
            }
        }

        CertificateSectionToggle();
    </script>
    {{-- certificate message  --}}
    <script>
        function showCertificateMessage(event) {

            let messageSection = document.getElementById("certificate_message");
            let certificateOption = document.querySelector("input[name='certificate']:checked");
            if (certificateOption.value === "1") {

                messageSection.innerHTML = "سوف تحصل على خصم 23%"
            } else if (certificateOption.value === "0") {
                messageSection.innerHTML = "بيفوتك الحصول على خصم 23%"

            } else {
                messageSection.innerHTML = ""

            }
        }

        showCertificateMessage();
    </script>
@endpush
