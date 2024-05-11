@extends(getTemplate() . '.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/owl-carousel2/owl.carousel.min.css">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-WSVP27XBX1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-WSVP27XBX1');
    </script>
    <style>
        .container_form {
            margin-top: 20px;
            /* border: 1px solid #ddd; */
            /* Add border to the container */
            padding: 20px;
            /* Optional: Add padding for spacing */
            border-radius: 16px !important;
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
            font-family: "IBM Plex Sans Arabic" !important;
            font-style: normal;
            font-weight: 400;
            font-size: 22px;
            line-height: 39px;
            color: #5E0A83;
        }

        .form-title {
            font-family: "IBM Plex Sans Arabic" !important;
            font-style: normal;
            font-weight: 700;
            /* font-size: 36px; */
            line-height: 42px;
            color: #fff;
        }

        input {
            text-align: right;
        }

        .main-section {
            background-color: #F6F7F8;
            border-radius: 16px !important;
        }

        .main-container {
            border-width: 2px !important;
            border-radius: 16px !important;
        }

        .secondary_education,
        .high_education,
        #education {
            display: none;
        }

        .hero {
            width: 100%;
            height: 50vh;
            /* background-color: #ED1088; */
            background-image: URL('https://lh3.googleusercontent.com/pw/AM-JKLXva4P7RlMWEJD_UMf699iZq37WokzlPBAqpkLcxYqgkUi3YzPTP5fuglzL3els1W36mjlBVmMNcqjGJMGNtQREe3THVN9pMkRZGNazhM3F5iQSuC4Z435gIA_0xrrPQWa1DGvsV02rmdJBJQxU0XM=w1400-h474-no');
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            display: flex;
            flex-direction: column;
            flex-wrap: nowrap;
            justify-content: center;
            align-items: stretch;
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
                font-size: 20px;
            }


        }
    </style>
@endpush

@section('content')

    {{-- hero section --}}
    @include('web.default.includes.hero_section', [
        'inner' => "<h1 class='form-title font-36'>نموذج قبول طلب جديد وحجز مقعد دراسي</h1>",
    ])

    <div class="application container">
        <div class="col-12 col-lg-10 col-md-11 px-0">
            <div class="col-lg-12 col-md-12 px-0">
                <Section class="section1 main-section">
                    <div class="container_form">
                        <!--Form Title-->

                        <p style="padding: 40px 0;font-size:18px;font-weight:600;line-height:1.5em">
                            يجب الاطلاع على متطلبات القبول في البرامج قبل تقديم طلب قبول جديد
                            <a href="https://anasacademy.uk/admission/" style="color:#f70387 !important;" target="_blank">
                                اضغط هنا
                            </a>
                        </p>
                        <form action="/apply" method="POST" id="myForm">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">

                            {{-- diploma --}}
                            <div class="form-group col-12 col-sm-6">
                                <label for="application"
                                    class="form-label">{{ trans('application_form.application') }}*</label>
                                <select id="mySelect1" name="category_id" required class="form-control @error('category_id') is-invalid @enderror"
                                    onchange="toggleHiddenInput()">
                                    <option disabled selected hidden value="">اختر الدرجة العلمية التي تريد دراستها في
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
                            <div class="form-group col-12 col-sm-6">
                                <label class="hidden-element" id="hiddenLabel1" for="bundle_id">
                                    {{ trans('application_form.specialization') }}*
                                </label>
                                <input type="text" id="bundle_id" name="bundle_id" required
                                    class="hidden-element form-control @error('bundle_id') is-invalid @enderror"
                                    value="{{ old('bundle_id', $student ? $student->bundle_id : '') }}">

                                @error('bundle_id')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- certificate --}}
                            <div class="form-group col-12  d-none" id="certificate_section">
                                <label>{{ trans('application_form.want_certificate') }} ؟ *</label>
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
                                            <input type="radio" id="want_certificate" name="certificate" value="1"
                                                onchange="showCertificateMessage()"   class=" @error('certificate') is-invalid @enderror"
                                                {{ old('certificate', $student->certificate ?? null) === '1' ? 'checked' : '' }}>
                                            نعم
                                        </label>
                                    </div>

                                    {{-- does not want certificate --}}
                                    <div class="col">
                                        <label for="doesn't_want_certificate">
                                            <input type="radio" id="doesn't_want_certificate" name="certificate"
                                                onchange="showCertificateMessage()" value="0"  class="@error('certificate') is-invalid @enderror"
                                                {{ old('certificate', $student->certificate ?? null) === '0' ? 'checked' : '' }}>
                                            لا
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <h1 class=" mt-50 mb-25">بيانات المتدرب الأساسية</h1>


                            {{-- personal details --}}
                            <section>
                                <h2 class="form-main-title">البيانات الشخصية</h2>
                                <section
                                    class="main-container border border-2 border-secondary-subtle rounded p-3 mt-2 mb-25 row mx-0">
                                    {{-- arabic name --}}
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="name">{{ trans('application_form.name') }}*</label>
                                        <input type="text" id="name" name="ar_name" {{-- value="{{ $student ? $student->ar_name : '' }}" --}}
                                            value="{{ old('ar_name', $student ? $student->ar_name : '') }}"
                                            placeholder="ادخل الإسم باللغه العربية فقط" required class="form-control @error('ar_name') is-invalid @enderror">

                                        @error('ar_name')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- english name --}}
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="name_en">{{ trans('application_form.name_en') }}*</label>
                                        <input type="text" id="name_en" name="en_name" {{-- value="{{ $student ? $student->en_name : '' }}" --}}
                                            value="{{ old('en_name', $student ? $student->en_name : '') }}"
                                            placeholder="ادخل الإسم باللغه الإنجليزيه فقط" required class="form-control @error('en_name') is-invalid @enderror">

                                        @error('en_name')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- identifier number --}}
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="identifier_num">رقم الهوية الوطنية أو جواز السفر*</label>
                                        <input type="text" id="identifier_num" name="identifier_num"
                                            {{-- value="{{ $student ? $student->identifier_num : '' }}" --}}
                                            value="{{ old('identifier_num', $student ? $student->identifier_num : '') }}"
                                            placeholder="الرجاء إدخال الرقم كامًلا والمكون من 10 أرقام للهوية أو 6 أرقام للجواز"
                                            required class="form-control  @error('identifier_num') is-invalid @enderror">

                                        @error('identifier_num')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- birthday --}}
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="birthday">{{ trans('application_form.birthday') }}*</label>
                                        <input type="date" id="birthday" name="birthdate" {{-- value="{{ $student ? $student->birthdate : '' }}" --}}
                                            value="{{ old('birthdate', $student ? $student->birthdate : '') }}" required
                                            class="form-control @error('birthdate') is-invalid @enderror">
                                        @error('birthdate')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>

                                    {{-- nationality --}}
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="nationality">{{ trans('application_form.nationality') }}*</label>
                                        @php
                                            $nationalities = [
                                                ' سعودي/ة',
                                                'اماراتي/ة',
                                                'اردني/ة',
                                                'بحريني/ة',
                                                'جزائري/ة',
                                                'عراقي/ة',
                                                'مغربي/ة',
                                                'يمني/ة',
                                                'سوداني/ة',
                                                'صومالي/ة',
                                                'كويتي/ة',
                                                'سوري/ة',
                                                'لبناني/ة',
                                                'مصري/ة',
                                                'تونسي/ة',
                                                'فلسطيني/ة',
                                                'جيبوتي/ة',
                                                'عماني/ة',
                                                'موريتاني/ة',
                                                'قطري/ة',
                                            ];
                                        @endphp
                                        <select id="nationality" name="nationality" required class="form-control  @error('nationality') is-invalid @enderror"
                                            onchange="toggleNationality()">
                                            <option value="" class="placeholder" disabled=""
                                                selected="selected">
                                                اختر جنسيتك</option>
                                            @foreach ($nationalities as $nationality)
                                                <option value="{{ $nationality }}"
                                                    {{ old('nationality', $student->nationality ?? null) == $nationality ? 'selected' : '' }}>
                                                    {{ $nationality }}</option>
                                            @endforeach
                                            <option value="اخرى" id="anotherNationality">اخرى</option>
                                        </select>
                                        @error('nationality')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- gender --}}
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="gender">{{ trans('application_form.gender') }}*</label>

                                        @error('gender')
                                            <div class="invalid-feedback d-inline">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                        <div class="row mr-5 mt-5">
                                            {{-- female --}}
                                            <div class="col-sm-4 col">
                                                <label for="female">
                                                    <input type="radio" id="female" name="gender" value="female" class=" @error('gender') is-invalid @enderror"
                                                        required
                                                        {{ old('gender', $student->gender ?? null) == 'female' ? 'checked' : '' }}>
                                                    انثي
                                                </label>
                                            </div>

                                            {{-- male --}}
                                            <div class="col">
                                                <label for="male">
                                                    <input type="radio" id="male" name="gender" value="male" class=" @error('gender') is-invalid @enderror"
                                                        required
                                                        {{ old('gender', $student->gender ?? null) == 'male' ? 'checked' : '' }}>
                                                    ذكر
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- other nationality --}}
                                    <div class="form-group col-12 col-sm-6" id="other_nationality_section"
                                        style="display: none">
                                        <label for="nationality">ادخل الجنسية *</label>
                                        <input type="text" class="form-control @error('other_nationality') is-invalid @enderror" id="other_nationality"
                                            name="other_nationality" placeholder="اكتب الجنسية" {{-- value="{{ $student ? $student->other_nationality : '' }}" --}}
                                            value="{{ old('other_nationality', $student ? $student->other_nationality : '') }}"
                                            onkeyup="setNationality()" >

                                        @error('other_nationality')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- country --}}
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="country">{{ trans('application_form.country') }}*</label>
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
                                        <select id="mySelect" name="country" required class="form-control @error('country') is-invalid @enderror"
                                            onchange="toggleHiddenInputs()">
                                            <option value="" class="placeholder" disabled="">اختر دولتك</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country }}"
                                                    {{ old('country', $student->country ?? null) == $country ? 'selected' : '' }}>
                                                    {{ $country }}</option>
                                            @endforeach
                                            <option value="اخرى" id="anotherCountry">اخرى</option>

                                        </select>

                                        @error('country')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- other country --}}
                                    <div class="form-group col-12 col-sm-6" id="anotherCountrySection"
                                        style="display: none">
                                        <label for="city" class="form-label">ادخل البلد*</label>
                                        <input type="text" id="city" name="city" class="form-control  @error('city') is-invalid @enderror"
                                            placeholder="ادخل دولتك"
                                            value="{{ old('city', $student ? $student->city : '') }}"
                                            onkeyup="setCountry()">

                                        @error('city')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- region --}}
                                    <div class="form-group col-12 col-sm-6" id="region" style="display: none">
                                        <label for="area" class="form-label">المنطقة*</label>
                                        <input type="text" id="area" name="area" class="form-control  @error('area') is-invalid @enderror"
                                            placeholder="اكتب المنطقة"
                                            value="{{ old('area', $student ? $student->area : '') }}">

                                        @error('area')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- city --}}
                                    <div class="form-group col-12 col-sm-6">
                                        <div id="cityContainer">
                                            <label for="town"
                                                id="cityLabel">{{ trans('application_form.city') }}*</label>
                                            <input type="text" id="town" name="town"
                                                placeholder="اكتب مدينه السكن الحاليه"
                                                value="{{ old('town', $student ? $student->town : '') }}" required
                                                class="form-control @error('town') is-invalid @enderror">
                                        </div>
                                        @error('town')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                </section>
                            </section>

                            {{-- contact details --}}
                            <section>
                                <h2 class="form-main-title">معلومات التواصل</h2>
                                <section
                                    class="main-container border border-2 border-secondary-subtle rounded p-3 mt-2 mb-25 row mx-0">
                                    {{-- phone number --}}
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="phone">{{ trans('application_form.phone') }}*</label>
                                        <input type="tel" id="phone" name="phone"
                                            value="{{ old('phone', $student ? $student->phone : $user->mobile) }}"
                                            class="form-control @error('phone') is-invalid @enderror">

                                        @error('phone')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- email --}}
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="email">{{ trans('application_form.email') }}*</label>
                                        <input type="email" id="email" name="email"
                                            value="{{ old('email', $student ? $student->email : $user->email) }}"
                                            placeholder="تسجيل البريد الإلكتروني" required class="form-control  @error('email') is-invalid @enderror">

                                        @error('email')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- mobile number --}}
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="mobile">{{ 'رقم الهاتف' }}</label>
                                        <input type="tel" id="mobile" name="mobile"
                                            value="{{ old('mobile', $student ? $student->mobile : $user->mobile) }}"
                                            class="form-control  @error('mobile') is-invalid @enderror">

                                        @error('mobile')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </section>

                            </section>


                            {{--  education --}}
                            <section id="education">
                                <h2 class="form-main-title">المؤهلات التعليمية</h2>
                                <section
                                    class="main-container border border-2 border-secondary-subtle rounded p-3 mt-2 mb-25 row mx-0">

                                    {{-- المؤهل التعليمي --}}
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="educational_qualification_country" class="form-label">بلد مصدر شهادة
                                            البكالوريوس*</label>

                                        <select id="educational_qualification_country"
                                            name="educational_qualification_country" required class="form-control @error('educational_qualification_country') is-invalid @enderror"
                                            onchange="educationCountryToggle()">
                                            <option value="" class="placeholder" disabled="">اختر دولتك</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country }}"
                                                    {{ old('educational_qualification_country', $student->educational_qualification_country ?? null) == $country ? 'selected' : '' }}>
                                                    {{ $country }}</option>
                                            @endforeach

                                            <option value="اخرى"
                                                {{ $student && !in_array($student->educational_qualification_country, $countries) ? 'selected' : '' }}
                                                id="anotherEducationCountryOption">اخرى</option>
                                        </select>
                                        @error('educational_qualification_country')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>

                                    {{-- مصدر شهادة البكالوريوس --}}
                                    <div class="form-group col-12 col-sm-6" id="anotherEducationCountrySection"
                                        style="display: none">

                                        <label for="university" class="form-label">
                                            ادخل مصدر شهادة البكالوريوس*
                                        </label>
                                        <input type="text" id="anotherEducationCountry" class="form-control @error('anotherEducationCountry') is-invalid @enderror"
                                            name="anotherEducationCountry" placeholder="ادخل مصدر شهادة البكالوريوس"
                                            value="{{ old('anotherEducationCountry', $student && !in_array($student->educational_qualification_country, $countries) ? $student->educational_qualification_country : '') }}"
                                            onkeyup="setEducationCountry()">

                                        @error('anotherEducationCountry')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>


                                    {{-- معدل المرحلة الثانوية --}}
                                    <div class="form-group col-12 col-sm-6 secondary_education">
                                        <label for="secondary_school_gpa" class="form-label">
                                            معدل المرحلة الثانوية*
                                        </label>
                                        <input type="text" id="secondary_school_gpa" class="form-control @error('secondary_school_gpa') is-invalid @enderror"
                                            name="secondary_school_gpa" placeholder="أدخل معدل المرحلة الثانوية"
                                            value="{{ old('secondary_school_gpa', $student ? $student->secondary_school_gpa : '') }}">

                                        @error('secondary_school_gpa')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- المنطقة التعليمية --}}
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="educational_area" class="form-label">
                                            المنطقة التعليمية*
                                        </label>
                                        <input type="text" id="educational_area" class="form-control @error('educational_area') is-invalid @enderror"
                                            name="educational_area" placeholder="أدخل المنطقة التعليمية"
                                            value="{{ old('educational_area', $student ? $student->educational_area : '') }}">

                                        @error('educational_area')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{--  سنة الحصول على الشهادة الثانوية --}}
                                    <div class="form-group col-12 col-sm-6 secondary_education">
                                        <label for="secondary_graduation_year" class="form-label">
                                            سنة الحصول على الشهادة الثانوية*
                                        </label>
                                        <input type="text" id="secondary_graduation_year" class="form-control @error('secondary_graduation_year') is-invalid @enderror"
                                            name="secondary_graduation_year"
                                            placeholder="أدخل سنة الحصول على الشهادة الثانوية"
                                            value="{{ old('secondary_graduation_year', $student ? $student->secondary_graduation_year : '') }}">

                                        @error('secondary_graduation_year')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- المدرسة --}}
                                    <div class="form-group col-12 col-sm-6 secondary_education">
                                        <label for="school" class="form-label">
                                            المدرسة*
                                        </label>
                                        <input type="text" id="school" class="form-control @error('school') is-invalid @enderror" name="school"
                                            placeholder="أدخل المدرسة"
                                            value="{{ old('school', $student ? $student->school : '') }}">

                                        @error('school')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>


                                    {{-- الجامعه --}}
                                    <div class="form-group col-12 col-sm-6 high_education">
                                        <label for="university" class="form-label">
                                            الجامعة*
                                        </label>
                                        <input type="text" id="university" class="form-control @error('university') is-invalid @enderror" name="university"
                                            placeholder="أدخل الجامعة"
                                            value="{{ old('university', $student ? $student->university : '') }}">

                                        @error('university')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- الكليه --}}
                                    <div class="form-group col-12 col-sm-6 high_education">
                                        <label for="faculty" class="form-label">
                                            الكلية*
                                        </label>
                                        <input type="text" id="faculty" class="form-control @error('faculty') is-invalid @enderror" name="faculty"
                                            placeholder="أدخل الكلية"
                                            value="{{ old('faculty', $student ? $student->faculty : '') }}">

                                        @error('faculty')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- التخصص  --}}
                                    <div class="form-group col-12 col-sm-6 high_education">
                                        <label for="education_specialization" class="form-label">
                                            التخصص*
                                        </label>
                                        <input type="text" id="education_specialization" class="form-control @error('education_specialization') is-invalid @enderror"
                                            name="education_specialization" placeholder="أدخل التخصص"
                                            value="{{ old('education_specialization', $student ? $student->education_specialization : '') }}">

                                        @error('education_specialization')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- سنة التخرج --}}
                                    <div class="form-group col-12 col-sm-6 high_education">
                                        <label for="graduation_year" class="form-label">
                                            سنة التخرج*
                                        </label>
                                        <input type="text" id="graduation_year" class="form-control @error('graduation_year') is-invalid @enderror"
                                            name="graduation_year" placeholder="أدخل سنة التخرج"
                                            value="{{ old('graduation_year', $student ? $student->graduation_year : '') }}">


                                        @error('graduation_year')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- المعدل --}}
                                    <div class="form-group col-12 col-sm-6 high_education">
                                        <label for="gpa" class="form-label">
                                            المعدل*
                                        </label>
                                        <input type="text" id="gpa" class="form-control @error('gpa') is-invalid @enderror" name="gpa"
                                            placeholder="أدخل المعدل "
                                            value="{{ old('gpa', $student ? $student->gpa : '') }}">

                                        @error('gpa')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </section>
                            </section>

                            {{-- working --}}
                            <section>
                                <h2 class="form-main-title">بيانات المهنة </h2>
                                <section
                                    class="main-container border border-2 border-secondary-subtle rounded p-3 mt-2 mb-25 row mx-0">
                                    {{-- work status --}}
                                    <div class="form-group col-12 col-sm-6">
                                        <label>{{ trans('application_form.status') }}*</label>

                                        @error('workStatus')
                                            <div class="invalid-feedback d-inline">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                        <div class="row mr-5 mt-5">
                                            {{-- working status --}}
                                            <div class="col-sm-4 col">
                                                <label for="working">
                                                    <input type="radio" id="working" name="workStatus" class="@error('workStatus') is-invalid @enderror"
                                                        value="1" required
                                                        {{ old('workStatus', $student->job ?? null) != null ? 'checked' : '' }}>
                                                    {{ trans('application_form.working') }}
                                                </label>
                                            </div>

                                            {{-- not working Status --}}
                                            <div class="col">
                                                <label for="not_working">
                                                    <input type="radio" id="not_working" name="workStatus" required class="@error('workStatus') is-invalid @enderror"
                                                        value="0"
                                                        {{ old('workStatus', $student->job ?? null) == null ? 'checked' : '' }}>
                                                    {{ trans('application_form.not_working') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- job details --}}
                                    <div class="col-12" id="job" style="display: none">
                                        <div class="row">
                                            <div class="form-group col-12 col-sm-6">
                                                <label for="job_title">الوظيفة*</label>
                                                <input type="text" id="job_title" name="job" class="form-control @error('job') is-invalid @enderror"
                                                    placeholder="أدخل الوظيفة"
                                                    value="{{ old('job', $student ? $student->job : '') }}">


                                                @error('job')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <div class="form-group col-12 col-sm-6">
                                                <label for="employment_type">جهة العمل*</label>
                                                <select id="employment_type" name="job_type" class="form-control @error('job_type') is-invalid @enderror">
                                                    <option value="" selected disabled>اختر جهة العمل</option>
                                                    <option value="governmental"
                                                        {{ old('job_type', $student->job_type ?? null) == 'governmental' ? 'selected' : '' }}>
                                                        حكومية</option>
                                                    <option value="private"
                                                        {{ old('job_type', $student->job_type ?? null) == 'private' ? 'selected' : '' }}>
                                                        خاصة</option>
                                                </select>

                                                @error('job_type')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </section>

                            {{-- healthy --}}
                            <section>
                                <h2 class="form-main-title">الحالة الصحية</h2>
                                <section
                                    class="main-container border border-2 border-secondary-subtle rounded p-3 mt-2 mb-25 row mx-0">

                                    {{-- deaf status --}}
                                    <div class="col-12 row">
                                        {{-- deaf --}}
                                        <div class="form-group col-12 col-sm-6">
                                            <label for="deaf">{{ trans('application_form.deaf_patient') }}؟ *</label>

                                            @error('deaf')
                                                <div class="invalid-feedback d-inline">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                            <div class="row mr-5 mt-5">
                                                {{-- deaf --}}
                                                <div class="col-sm-4 col">
                                                    <label for="deaf">
                                                        <input type="radio" id="deaf" name="deaf" class="@error('deaf') is-invalid @enderror"
                                                            value="1" required
                                                            {{ old('deaf', $student->deaf ?? null) == 1 ? 'checked' : '' }}>
                                                        نعم
                                                    </label>
                                                </div>

                                                {{-- not deaf --}}
                                                <div class="col">
                                                    <label for="not_deaf">
                                                        <input type="radio" id="not_deaf" name="deaf" class="@error('deaf') is-invalid @enderror"
                                                            value="0" required
                                                            {{ old('deaf', $student->deaf ?? null) == 0 ? 'checked' : '' }}>
                                                        لا
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- disabled --}}
                                    <div class="col-12 row">

                                        {{-- disabled --}}
                                        <div class="form-group col-12 col-sm-6">
                                            <label>هل أنت من ذوي الإعاقة؟*</label>

                                            @error('disabled')
                                                <div class="invalid-feedback d-inline">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                            <div class="row mr-5 mt-5">
                                                {{-- disabled --}}
                                                <div class="col-sm-4 col">
                                                    <label for="disabled">
                                                        <input type="radio" id="disabled" name="disabled" class="@error('disabled') is-invalid @enderror"
                                                            value="1" required
                                                            {{ old('disabled', $student->disabled_type ?? null) != null ? 'checked' : '' }}>
                                                        نعم
                                                    </label>
                                                </div>

                                                {{-- not disabled --}}
                                                <div class="col">
                                                    <label for="not_disabled">
                                                        <input type="radio" id="not_disabled" name="disabled" class="@error('disabled') is-invalid @enderror"
                                                            value="0" required
                                                            {{ old('disabled', $student->disabled_type ?? null) == null ? 'checked' : '' }}>
                                                        لا
                                                    </label>
                                                </div>
                                            </div>

                                        </div>

                                        {{-- disabled type --}}
                                        <div class="form-group col-12 col-sm-6" id="disabled_type_section"
                                            style="display: none">
                                            <label for="disabled_type">{{ 'حدد نوع الإعاقة*' }}</label>
                                            <select id="disabled_type" name="disabled_type" class="form-control @error('disabled_type') is-invalid @enderror">
                                                <option value="" class="placeholder" disabled="" selected>أختر
                                                    نوع
                                                    الإعاقة
                                                </option>
                                                <option value="option1"
                                                    {{ old('disabled_type', $student->disabled_type ?? null) == 'option1' ? 'selected' : '' }}>
                                                    اوبشن 1 </option>
                                                <option value="option2"
                                                    {{ old('disabled_type', $student->disabled_type ?? null) == 'option2' ? 'selected' : '' }}>
                                                    اوبشن 2</option>
                                            </select>

                                            @error('disabled_type')
                                                <div class="invalid-feedback d-block">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                    </div>

                                    {{-- healthy problem --}}
                                    <div class="col-12 row">
                                        {{-- healthy status --}}
                                        <div class="form-group col-12 col-sm-6">
                                            <label
                                                for="healthy">{{ trans('application_form.health_proplem') }}؟*</label>


                                            @error('healthy')
                                                <div class="invalid-feedback d-inline">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                            <div class="row mr-5 mt-5">
                                                {{-- healthy --}}
                                                <div class="col-sm-4 col">
                                                    <label for="healthy">
                                                        <input type="radio" id="healthy" name="healthy" class=" @error('healthy') is-invalid @enderror"
                                                            value="1" required
                                                            {{ old('healthy', $student->healthy_problem ?? null) != null ? 'checked' : '' }}>
                                                        نعم
                                                    </label>
                                                </div>

                                                {{-- not healthy --}}
                                                <div class="col">
                                                    <label for="not_healthy">
                                                        <input type="radio" id="not_healthy" name="healthy" class=" @error('healthy') is-invalid @enderror"
                                                            value="0" required
                                                            {{ old('healthy', $student->healthy_problem ?? null) == null ? 'checked' : '' }}>
                                                        لا
                                                    </label>
                                                </div>
                                            </div>

                                        </div>

                                        {{-- healthy problem --}}
                                        <div class="form-group col-12 col-sm-6" id="healthy_problem_section"
                                            style="display: none">
                                            <label for="healthy_problem">ادخل المشكلة الصحية*</label>
                                            <input type="text" id="healthy_problem" class="form-control @error('healthy_problem') is-invalid @enderror"
                                                name="healthy_problem" placeholder="ادخل المشكلة الصحية"
                                                value="{{ old('healthy_problem', $student ? $student->healthy_problem : '') }}">

                                            @error('healthy_problem')
                                                <div class="invalid-feedback d-block">
                                                    {{ $message }}
                                                </div>
                                            @enderror


                                        </div>
                                    </div>
                                </section>
                            </section>

                            {{-- Relatives --}}
                            <section>
                                <h2 class="form-main-title">معلومات الأقرباء في حال الطوارئ</h2>
                                <section
                                    class="main-container border border-2 border-secondary-subtle rounded p-3 mt-2 mb-25 row mx-0">
                                    <div class="form-group col-12 col-sm-6">
                                        <label
                                            for="referral_person">{{ trans('application_form.referral_name') }}*</label>
                                        <input type="text" id="referral_person" name="referral_person"
                                            value="{{ old('referral_person', $student ? $student->referral_person : '') }}"
                                            placeholder="أدخل الأسم الثنائي" required class="form-control  @error('referral_person') is-invalid @enderror">

                                        @error('referral_person')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>

                                    <div class="form-group col-12 col-sm-6">
                                        <label for="relation">{{ trans('application_form.referral_state') }}*</label>
                                        <input type="text" id="relation" name="relation"
                                            value="{{ old('relation', $student ? $student->relation : '') }}"
                                            placeholder="أدخل صلة القرابة" required class="form-control  @error('relation') is-invalid @enderror">

                                        @error('relation')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-12 col-sm-6">
                                        <label for="referral_email">{{ trans('application_form.email') }}*</label>
                                        <input type="email" id="referral_email" name="referral_email"
                                            value="{{ old('referral_email', $student ? $student->referral_email : '') }}"
                                            placeholder="أدخل بريد الكتروني" required class="form-control  @error('referral_email') is-invalid @enderror">


                                        @error('referral_email')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>

                                    <div class="form-group col-12 col-sm-6">
                                        <label>{{ trans('application_form.phone') }}*</label>
                                        <input type="tel" id="referral_phone" placeholder="أدخل جوال"
                                            name="referral_phone"
                                            value="{{ old('referral_phone', $student ? $student->referral_phone : '') }}"
                                            class="form-control  @error('referral_phone') is-invalid @enderror">

                                        @error('referral_phone')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </section>
                            </section>

                            {{-- about us --}}
                            <div class="form-group col-12">
                                <label>{{ trans('application_form.heard_about_us') }}*</label>

                                @error('about_us')
                                    <div class="invalid-feedback d-inline">
                                        {{ $message }}
                                    </div>
                                @enderror


                                <br>

                                <label for="snapchat">
                                    <input type="radio" id="snapchat" name="about_us" value="snapchat" class=" @error('about_us') is-invalid @enderror"
                                        {{ old('about_us', $student->about_us ?? null) == 'snapchat' ? 'checked' : '' }}>
                                    {{ trans('application_form.snapchat') }}
                                </label><br>
                                <label for="twitter">
                                    <input type="radio" id="twitter" name="about_us" value="twitter" class=" @error('about_us') is-invalid @enderror"
                                        {{ old('about_us', $student->about_us ?? null) == 'twitter' ? 'checked' : '' }}>
                                    {{ trans('application_form.twitter') }}
                                </label><br>
                                <label for="friend">
                                    <input type="radio" id="friend" name="about_us" value="friend" class=" @error('about_us') is-invalid @enderror"
                                        {{ old('about_us', $student->about_us ?? null) == 'friend' ? 'checked' : '' }}>
                                    {{ trans('application_form.friend') }}
                                </label><br>
                                <label for="instagram">
                                    <input type="radio" id="instagram" name="about_us" value="instagram" class=" @error('about_us') is-invalid @enderror"
                                        {{ old('about_us', $student->about_us ?? null) == 'instagram' ? 'checked' : '' }}>
                                    {{ trans('application_form.instagram') }}
                                </label><br>
                                <label for="facebook">
                                    <input type="radio" id="facebook" name="about_us" value="facebook" class=" @error('about_us') is-invalid @enderror"
                                        {{ old('about_us', $student->about_us ?? null) == 'facebook' ? 'checked' : '' }}>
                                    {{ trans('application_form.facebook') }}
                                </label><br>
                                <label for="other">
                                    <input type="radio" id="other" name="about_us" value="other" class=" @error('about_us') is-invalid @enderror"
                                        {{ old('about_us', $student->about_us ?? null) == 'other' ? 'checked' : '' }}>
                                    {{ trans('application_form.other') }}
                                </label><br>
                                <label id="otherLabel"style="display:none">أدخل المصدر</label>
                                <input type="text" id="otherInput" placeholder="" name="other_about_us"
                                    class="form-control @error('about_us') is-invalid @enderror" style="display:none"><br>


                                <label>
                                    <input type="checkbox" id="terms" name="terms" required class="@error('terms') is-invalid @enderror">

                                    اقر أنا المسجل بياناتي اعلاه بموافقتي على لائحة الحقوق والوجبات واحكام وشروط
                                    القبول
                                    والتسجيل، كما أقر بالتزامي التام بمضمونها، وبمسؤوليتي التامة عن أية مخالفات قد
                                    تصدر مني لها
                                    ، مما يترتب عليه كامل الأحقية للاكاديمية في مسائلتي عن تلك المخالفات والتصرفات
                                    المخالفة
                                    للوائح المشار إليها في عقد اتفاقية التحاق متدربـ/ـة <a target="_blank"
                                        href="https://anasacademy.uk/wp-content/uploads/2024/05/contract.pdf">انقر
                                        هنا
                                        لمشاهدة</a>

                                </label>

                                @error('terms')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
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
                            <button type="submit"
                                class="btn btn-primary">{{ trans('application_form.submit') }}</button>
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
    <script src="/assets/default/vendors/swiper/swiper-bundle.min.js"></script>
    <script src="/assets/default/vendors/owl-carousel2/owl.carousel.min.js"></script>
    <script src="/assets/default/vendors/parallax/parallax.min.js"></script>
    <script src="/assets/default/js/parts/home.min.js"></script>

    {{-- job script --}}
    <script>
        var working = document.getElementById("working");
        var notWorking = document.getElementById("not_working");
        var job = document.getElementById("job");

        function toggleJobFields() {
            if (working.checked) {
                job.style.display = "block";
            } else {
                job.style.display = "none";
            }
        }

        working.addEventListener("change", toggleJobFields);
        notWorking.addEventListener("change", toggleJobFields);
        toggleJobFields();
    </script>

    {{-- about us script --}}
    <script>
        var otherLabel = document.getElementById("otherLabel");
        var otherInput = document.getElementById("otherInput");

        var radioButtons = document.querySelectorAll('input[name="about_us"]');

        radioButtons.forEach(function(radioButton) {
            radioButton.addEventListener("change", function() {
                if (radioButton.value === "other" && radioButton.checked) {
                    otherLabel.style.display = "block";
                    otherInput.style.display = "block";
                    radioButton.value = otherInput.value;
                } else {
                    otherLabel.style.display = "none";
                    otherInput.style.display = "none";
                }
            });
        });

        otherInput.addEventListener("change", function() {
            let radioButton = document.getElementById('other');
            radioButton.value = otherInput.value;
        })
    </script>

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
                        return `<option value="${bundle.id}" ${isSelected} has_certificate="${bundle.has_certificate}">${bundle.title}</option>`;
                    }).join('');

                    hiddenInput.outerHTML =
                        '<select id="bundle_id" name="bundle_id"  class="form-control" onchange="CertificateSectionToggle()" required>' +
                        '<option value="" class="placeholder" disabled="" selected="selected">اختر التخصص الذي تود دراسته في اكاديمية انس للفنون</option>' +
                        options +
                        '</select>';
                    hiddenLabel.style.display = "block";
                } else {
                    hiddenInput.outerHTML =
                        '<input type="text" id="bundle_id" name="bundle_id" placeholder="ادخل الإسم باللغه العربية فقط"  class="hidden-element form-control">';
                    hiddenLabel.style.display = "none";
                }
                var selectedOption = select.options[select.selectedIndex];
                var selectedText = selectedOption.textContent;
                education.style.display = "block";

                console.log(selectedOption.getAttribute('education'));

                if (selectedOption.getAttribute('education') == "0") {

                    secondary_education.forEach(function(element) {
                        element.style.display = "block";
                    });

                    high_education.forEach(function(element) {
                        element.style.display = "none";
                    });

                } else {
                    secondary_education.forEach(function(element) {
                        element.style.display = "none";
                    });

                    high_education.forEach(function(element) {
                        element.style.display = "block";
                    });

                }

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

        CertificateSectionToggle();
    </script>

    {{-- certificate message  --}}
    <script>
        function showCertificateMessage() {

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

    {{-- city and country toggle --}}
    <script>
        function toggleHiddenInputs() {
            var select = document.getElementById("mySelect");
            var hiddenInput = document.getElementById("area");
            var hiddenLabel = document.getElementById("hiddenLabel");
            var hiddenInput2 = document.getElementById("city");
            var hiddenLabel2 = document.getElementById("hiddenLabel2");
            var cityLabel = document.getElementById("cityLabel");
            var town = document.getElementById("town");
            var anotherCountrySection = document.getElementById("anotherCountrySection");
            var region = document.getElementById("region");
            let anotherCountryOption = document.getElementById("anotherCountry");

            if (select && select.value !== "السعودية") {
                region.style.display = "block";
            } else {
                region.style.display = "none";
            }

            if (select.value === "اخرى") {
                anotherCountrySection.style.display = "block";
                anotherCountryOption.value = hiddenInput2.value;
            } else {
                anotherCountrySection.style.display = "none";
                anotherCountryOption.value = "اخرى";

            }
            if (select && cityLabel && town) {
                if (select.value === "السعودية") {
                    town.outerHTML = '<select id="town" name="town"  class="form-control">' +
                        '<option value="الرياض" selected="selected">الرياض</option>' +
                        '<option value="جدة>"جدة</option>' +
                        '<option value="مكة المكرمة">مكة المكرمة</option>' +
                        '<option value="المدينة المنورة">المدينة المنورة</option>' +
                        '<option value="الدمام">الدمام</option>' +
                        '<option value="الطائف">الطائف</option>' +
                        '<option value="تبوك">تبوك</option>' +
                        '<option value="الخرج">الخرج</option>' +
                        '<option value="بريدة">بريدة</option>' +
                        '<option value="خميس مشيط">خميس مشيط</option>' +
                        '<option value="الهفوف">الهفوف</option>' +
                        '<option value="المبرز">المبرز</option>' +
                        '<option value="حفر الباطن">حفر الباطن</option>' +
                        '<option value="حائل">حائل</option>' +
                        '<option value="نجران">نجران</option>' +
                        '<option value="الجبيل">الجبيل</option>' +
                        '<option value="أبها">أبها</option>' +
                        '<option value="ينبع">ينبع</option>' +
                        '<option value="الخبر">الخبر</option>' +
                        '<option value="عنيزة">عنيزة</option>' +
                        '<option value="عرعر">عرعر</option>' +
                        '<option value="سكاكا">سكاكا</option>' +
                        '<option value="جازان">جازان</option>' +
                        '<option value="القريات">القريات</option>' +
                        '<option value="الظهران">الظهران</option>' +
                        '<option value="القطيف">القطيف</option>' +
                        '<option value="الباحة">الباحة</option>' +
                        '</select>';
                } else {

                    town.outerHTML =
                        `<input type="text" id="town" name="town" placeholder="اكتب مدينه السكن الحاليه" class="form-control" value="{{ old('town', $student ? $student->town : '') }}" >`;
                }
            }
        }

        function setCountry() {
            let anotherCountrySection = document.getElementById("anotherCountrySection");
            let anotherCountryOption = document.getElementById("anotherCountry");
            let another_country = document.getElementById("city");

            if (anotherCountrySection.style.display != "none") {
                // nationality.value = other_nationality.value;
                anotherCountryOption.value = another_country.value;

            }
        }
        toggleHiddenInputs();
    </script>

    {{--  healthy section toggle --}}
    <script>
        // healthy section display
        function toggleHealthyProblemSection() {
            let healthyProblemSection = document.getElementById("healthy_problem_section");
            let healthyStatus = document.getElementById("healthy");
            if (healthyStatus.checked) {
                healthyProblemSection.style.display = "block";
            } else {
                healthyProblemSection.style.display = "none";
            }

        }

        let healthy = document.getElementById("healthy");
        let notHealthy = document.getElementById("not_healthy");
        healthy.addEventListener("change", toggleHealthyProblemSection);
        notHealthy.addEventListener("change", toggleHealthyProblemSection);
        toggleHealthyProblemSection();
    </script>

    {{-- disabled section toggle --}}

    <script>
        // disabled section display
        function toggleDisabledSection() {
            let disabledTypeSection = document.getElementById("disabled_type_section");
            let disabledStatus = document.getElementById("disabled");
            if (disabledStatus.checked) {
                disabledTypeSection.style.display = "block";
            } else {
                disabledTypeSection.style.display = "none";
            }

        }
        let disabled = document.getElementById("disabled");
        let notDisabled = document.getElementById("not_disabled");
        disabled.addEventListener("change", toggleDisabledSection);
        notDisabled.addEventListener("change", toggleDisabledSection);
        toggleDisabledSection();
    </script>

    {{-- nationality toggle --}}
    <script>
        function toggleNationality() {
            let other_nationality_section = document.getElementById("other_nationality_section");
            let nationality = document.getElementById("nationality");
            let other_nationality = document.getElementById("other_nationality");
            let anotherNationalityOption = document.getElementById("anotherNationality");
            if (nationality && nationality.value == "اخرى") {
                other_nationality_section.style.display = "block";

                // nationality.value = other_nationality.value;
                anotherNationalityOption.value = other_nationality.value;
            } else {
                other_nationality_section.style.display = "none";
                anotherNationalityOption.value = "اخرى";
            }
        }

        function setNationality() {
            let other_nationality_section = document.getElementById("other_nationality_section");
            let nationality = document.getElementById("nationality");
            let other_nationality = document.getElementById("other_nationality");
            let anotherNationalityOption = document.getElementById("anotherNationality");
            if (other_nationality_section.style.display != "none") {
                // nationality.value = other_nationality.value;
                anotherNationalityOption.value = other_nationality.value;

            }
        }
    </script>

    {{-- education section --}}
    <script>
        function educationCountryToggle() {
            let anotherEducationCountrySection = document.getElementById("anotherEducationCountrySection");
            let anotherEducationCountry = document.getElementById("anotherEducationCountry");
            let anotherEducationCountryOption = document.getElementById("anotherEducationCountryOption");
            let educationalQualificationCountry = document.getElementById("educational_qualification_country");

            if (educationalQualificationCountry && educationalQualificationCountry.value == "اخرى") {
                anotherEducationCountrySection.style.display = "block";
                anotherEducationCountryOption.value = anotherEducationCountry.value;

            } else {
                anotherEducationCountrySection.style.display = "none";
                anotherEducationCountryOption.value = "اخرى";
            }

        }

        function setEducationCountry() {
            let anotherEducationCountrySection = document.getElementById("anotherEducationCountrySection");
            let anotherEducationCountry = document.getElementById("anotherEducationCountry");
            let anotherEducationCountryOption = document.getElementById("anotherEducationCountryOption");
            let educationalQualificationCountry = document.getElementById("educational_qualification_country");

            if (anotherEducationCountrySection.style.display != "none") {
                anotherEducationCountryOption.value = anotherEducationCountry.value;
            }

        }
        educationCountryToggle();
    </script>
@endpush
