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

                            {{-- diploma --}}
                            <div class="form-group col-12 col-sm-6">
                                <label for="application"
                                    class="form-label">{{ trans('application_form.application') }}*</label>
                                <select id="mySelect1" name="category_id" required class="form-control"
                                    onchange="toggleHiddenInput()">
                                    <option disabled selected hidden value="">اختر الدرجة العلمية التي تريد دراستها في
                                        اكاديمية انس للفنون </option>
                                    @foreach ($category as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('category_id', $student->category_id ?? null) == $item->id ? 'selected' : '' }}>
                                            {{ $item->title }} </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- specialization --}}
                            <div class="form-group col-12 col-sm-6">
                                <label class="hidden-element" id="hiddenLabel1" for="name">
                                    {{ trans('application_form.specialization') }}*
                                </label>
                                <input type="text" id="bundle_id" name="bundle_id" required
                                    class="hidden-element form-control"
                                    value="{{ old('bundle_id', $student ? $student->bundle_id : '') }}">
                            </div>

                            {{--  education --}}
                            @php
                                $countries = [
                                    'السعودية',
                                    'الامارات العربية المتحدة',
                                    'الاردن',
                                    'البحرين',
                                    'الجزائر',
                                    'العراق',
                                    'المغرب',
                                    'اليمن',
                                    'السودان',
                                    'الصومال',
                                    'الكويت',
                                    'جنوب السودان',
                                    'سوريا',
                                    'لبنان',
                                    'مصر',
                                    'تونس',
                                    'فلسطين',
                                    'جزرالقمر',
                                    'جيبوتي',
                                    'عمان',
                                    'موريتانيا',
                                ];
                            @endphp


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
                    </div>


                    {{-- display errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <button type="submit" class="btn btn-primary">{{ trans('application_form.submit') }}</button>
                    </form>
            </div>
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
                        return `<option value="${bundle.id}" ${isSelected}>${bundle.title}</option>`;
                    }).join('');

                    hiddenInput.outerHTML = '<select id="bundle_id" name="bundle_id"  class="form-control">' +
                        '<option value="" class="placeholder" disabled="" selected="selected">اختر التخصص الذي تود دراسته في اكاديمية انس للفنون</option>' +
                        options +
                        '</select>';
                    hiddenLabel.style.display = "block";

                }
                else {
                    hiddenInput.outerHTML =
                        '<input type="text" id="bundle_id" name="bundle_id" placeholder="ادخل الإسم باللغه العربية فقط"  class="hidden-element form-control">';
                    hiddenLabel.style.display = "none";
                }


            }
        }
        toggleHiddenInput();
    </script>
@endpush