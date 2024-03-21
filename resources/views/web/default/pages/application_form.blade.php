@extends(getTemplate() . '.layouts.app')

@push('styles_top')
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
        @media(max-width:768px){
            .hero{
                height: 50vh;
            }
            footer img{
                width: 150px !important;
            }
            .img-cover{
                width: 100% !important;
            }
        }
        @media(max-width:576px){
            .form-main-title{
                font-size:25px;
            }


        }
    </style>
@endpush

@section('content')
    <header class="hero">
        <section class=" container-fluid"></section>

    </header>
    <div class="application container-fluid">
        <div class="col-12 col-lg-10 col-md-11 px-0">
            <div class="col-lg-12 col-md-12 px-0">
                <Section class="section1 main-section">
                    <div class="container_form">
                        <!--Form Title-->
                        <h1 class="form-title">نموذج قبول طلب جديد وحجز مقعد دراسي</h1>
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
                                <select id="mySelect1" name="category_id" required class="form-control"
                                    onchange="toggleHiddenInput()">
                                    <option disabled selected hidden value="">اختر الدرجة العلمية التي تريد دراستها في
                                        اكاديمية انس للفنون </option>
                                    @foreach ($category as $item)
                                        <option value="{{ $item->id }}">{{ $item->title }} </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- specialization --}}
                            <div class="form-group col-12 col-sm-6">
                                <label class="hidden-element" id="hiddenLabel1" for="name">
                                    {{ trans('application_form.specialization') }}*
                                </label>
                                <input type="text" id="bundle_id" name="bundle_id" required
                                    class="hidden-element form-control">
                            </div>

                            <h1 class="pr-3 mt-50 mb-25">بيانات المتدرب الأساسية</h1>


                            {{-- personal details --}}
                            <section>
                                <h2 class="form-main-title pr-3">البيانات الشخصية</h2>
                                <section
                                    class="main-container border border-2 border-secondary-subtle rounded p-3 mt-2 mb-25 row mx-0">
                                    {{-- arabic name --}}
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="name">{{ trans('application_form.name') }}*</label>
                                        <input type="text" id="name" name="ar_name"
                                            value="{{ $student ? $student->ar_name : '' }}"
                                            placeholder="ادخل الإسم باللغه العربية فقط" required class="form-control">
                                    </div>

                                    {{-- english name --}}
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="name_en">{{ trans('application_form.name_en') }}*</label>
                                        <input type="text" id="name_en" name="en_name"
                                            value="{{ $student ? $student->en_name : '' }}"
                                            placeholder="ادخل الإسم باللغه الإنجليزيه فقط" required class="form-control">
                                    </div>

                                    {{-- identifier number --}}
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="identifier_num">رقم الهوية الوطنية أو جواز السفر*</label>
                                        <input type="text" id="identifier_num" name="identifier_num" value="{{ $student ? $student->identifier_num : '' }}"
                                            placeholder="الرجاء إدخال الرقم كامًلا والمكون من 10 أرقام للهوية أو 6 أرقام للجواز"
                                            required class="form-control">
                                    </div>

                                    {{-- birthday --}}
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="birthday">{{ trans('application_form.birthday') }}*</label>
                                        <input type="date" id="birthday" name="birthdate"
                                            value="{{ $student ? $student->birthdate : '' }}" required
                                            class="form-control">
                                    </div>

                                    {{-- nationality --}}
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="nationality">{{ trans('application_form.nationality') }}*</label>
                                        <select id="nationality" name="nationality" required class="form-control" onchange="toggleNationality()">
                                            <option value="" class="placeholder" disabled="" selected="selected">
                                                اختر جنسيتك</option>
                                            <option value="سعودي/ة" selected>سعودي/ة</option>
                                            <option value="اماراتي/ة">اماراتي/ة</option>
                                            <option value="اردني/ة">اردني/ة</option>
                                            <option value="بحريني/ة">بحريني/ة</option>
                                            <option value="جزائري/ة">جزائري/ة</option>
                                            <option value="عراقي/ة">عراقي/ة</option>
                                            <option value="مغربي/ة">مغربي/ة</option>
                                            <option value="يمني/ة">يمني/ة</option>
                                            <option value="سوداني/ة">سوداني/ة</option>
                                            <option value="صومالي/ة">صومالي/ة</option>
                                            <option value="كويتي/ة">كويتي/ة</option>
                                            <option value="سوري/ة">سوري/ة</option>
                                            <option value="لبناني/ة">لبناني/ة</option>
                                            <option value="مصري/ة">مصري/ة</option>
                                            <option value="تونسي/ة">تونسي/ة</option>
                                            <option value="فلسطيني/ة">فلسطيني/ة</option>
                                            <option value="عماني/ة">عماني/ة</option>
                                            <option value="قطري/ة">قطري/ة</option>
                                            <option value="موريتاني/ة">موريتاني/ة</option>
                                            <option value="أخرى" id="anotherNationality">أخرى</option>
                                        </select>
                                    </div>

                                    {{-- gender --}}
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="gender">{{ trans('application_form.gender') }}*</label>

                                        <div class="row mr-5 mt-5">
                                            {{-- female --}}
                                            <div class="col-sm-4 col">
                                                <label for="female">
                                                    <input type="radio" id="female" name="gender" value="female"
                                                        required>
                                                    انثي
                                                </label>
                                            </div>

                                            {{-- male --}}
                                            <div class="col">
                                                <label for="male">
                                                    <input type="radio" id="male" name="gender" value="male"
                                                        required>
                                                    ذكر
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- other nationality --}}
                                    <div class="form-group col-12 col-sm-6" id="other_nationality_section"
                                        style="display: none">
                                        <label for="nationality">ادخل الجنسية *</label>
                                        <input type="text" class="form-control" id="other_nationality"
                                            name="other_nationality" placeholder="اكتب الجنسية"
                                            value="{{ $student ? $student->other_nationality : '' }}"
                                            onkeyup="setNationality()">
                                    </div>

                                    {{-- country --}}
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="country">{{ trans('application_form.country') }}*</label>
                                        <select id="mySelect" name="country" required class="form-control"
                                            onchange="toggleHiddenInputs()">
                                            <option value="" class="placeholder" disabled="">اختر دولتك</option>
                                            <option value="السعودية" selected="selected">السعودية</option>
                                            <option value="الامارات العربية المتحدة">الامارات العربية المتحدة</option>
                                            <option value="الاردن">الاردن</option>
                                            <option value="البحرين">البحرين</option>
                                            <option value="الجزائر">الجزائر</option>
                                            <option value="العراق">العراق</option>
                                            <option value="المغرب">المغرب</option>
                                            <option value="اليمن">اليمن</option>
                                            <option value="السودان">السودان</option>
                                            <option value="الصومال">الصومال</option>
                                            <option value="الكويت">الكويت</option>
                                            <option value="جنوب السودان">جنوب السودان</option>
                                            <option value="سوريا">سوريا</option>
                                            <option value="لبنان">لبنان</option>
                                            <option value="مصر">مصر</option>
                                            <option value="تونس">تونس</option>
                                            <option value="فلسطين">فلسطين</option>
                                            <option value="جزرالقمر">جزرالقمر</option>
                                            <option value="جيبوتي">جيبوتي</option>
                                            <option value="عمان">عمان</option>
                                            <option value="موريتانيا">موريتانيا</option>
                                            <option value="أخرى" id="anotherCountry">أخرى</option>
                                        </select>
                                    </div>

                                    {{-- other country --}}
                                    <div class="form-group col-12 col-sm-6" id="anotherCountrySection" style="display: none">
                                        <label for="city" class="form-label">ادخل البلد*</label>
                                        <input type="text" id="city" name="city" class="form-control"
                                            placeholder="ادخل دولتك"
                                            value="{{ $student ? $student->city : '' }}"
                                            onkeyup="setCountry()">
                                    </div>

                                    {{-- region --}}
                                    <div class="form-group col-12 col-sm-6" id="region" style="display: none">
                                        <label for="area" class="form-label">المنطقة*</label>
                                        <input type="text" id="area" name="area" class="form-control"
                                            placeholder="اكتب المنطقة"
                                            value="{{ $student ? $student->area : '' }}">
                                    </div>

                                    {{-- city --}}
                                    <div class="form-group col-12 col-sm-6">
                                        <div id="cityContainer">
                                            <label for="town"
                                                id="cityLabel">{{ trans('application_form.city') }}*</label>
                                            <input type="text" id="town" name="town"
                                                placeholder="اكتب مدينه السكن الحاليه"
                                                value="{{ $student ? $student->town : '' }}"
                                                required class="form-control">
                                        </div>
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
                                            value="{{ $student ? $student->phone : $user->mobile }}"
                                            class="form-control">
                                    </div>

                                    {{-- email --}}
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="email">{{ trans('application_form.email') }}*</label>
                                        <input type="email" id="email" name="email"
                                            value="{{ $student ? $student->email : $user->email }}"
                                            placeholder="تسجيل البريد الإلكتروني" required class="form-control">
                                    </div>

                                    {{-- mobile number --}}
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="mobile">{{ 'رقم الهاتف' }}</label>
                                        <input type="tel" id="mobile" name="mobile"
                                            value="{{ $student ? $student->mobile : $user->mobile }}"
                                            class="form-control">
                                    </div>
                                </section>

                            </section>


                            {{--  education --}}
                            <section id="education">
                                <h2 class="form-main-title">المؤهلات التعليمية</h2>
                                <section
                                    class="main-container border border-2 border-secondary-subtle rounded p-3 mt-2 mb-25 row mx-0">

                                    {{-- المؤهل التعليمي --}}
                                    <div class="form-group col-12 col-sm-6 high_education">
                                        <label for="educational_qualification_country" class="form-label">بلد مصدر شهادة
                                            البكالوريوس*</label>

                                        <select id="educational_qualification_country"
                                            name="educational_qualification_country" required class="form-control" onchange="educationCountryToggle()">
                                            <option value="" class="placeholder" disabled="">اختر دولتك</option>
                                            <option value="السعودية" selected="selected">السعودية</option>
                                            <option value="الامارات العربية المتحدة">الامارات العربية المتحدة</option>
                                            <option value="الاردن">الاردن</option>
                                            <option value="البحرين">البحرين</option>
                                            <option value="الجزائر">الجزائر</option>
                                            <option value="العراق">العراق</option>
                                            <option value="المغرب">المغرب</option>
                                            <option value="اليمن">اليمن</option>
                                            <option value="السودان">السودان</option>
                                            <option value="الصومال">الصومال</option>
                                            <option value="الكويت">الكويت</option>
                                            <option value="جنوب السودان">جنوب السودان</option>
                                            <option value="سوريا">سوريا</option>
                                            <option value="لبنان">لبنان</option>
                                            <option value="مصر">مصر</option>
                                            <option value="تونس">تونس</option>
                                            <option value="فلسطين">فلسطين</option>
                                            <option value="جزرالقمر">جزرالقمر</option>
                                            <option value="جيبوتي">جيبوتي</option>
                                            <option value="عمان">عمان</option>
                                            <option value="موريتانيا">موريتانيا</option>
                                            <option value="أخرى" id="anotherEducationCountryOption">أخرى</option>
                                        </select>
                                    </div>

                                    {{-- مصدر شهادة البكالوريوس --}}
                                    <div class="form-group col-12 col-sm-6" id="anotherEducationCountrySection" style="display: none">

                                        <label for="university" class="form-label">
                                            ادخل مصدر شهادة البكالوريوس*
                                        </label>
                                        <input type="text" id="anotherEducationCountry" class="form-control" name="anotherEducationCountry"
                                            placeholder="ادخل مصدر شهادة البكالوريوس"
                                            value="{{ $student ? $student->anotherEducationCountry : '' }}"

                                            onkeyup="setEducationCountry()">
                                    </div>

                                    {{-- معدل المرحلة الثانوية --}}
                                    <div class="form-group col-12 col-sm-6 secondary_education">
                                        <label for="secondary_school_gpa" class="form-label">
                                            معدل المرحلة الثانوية*
                                        </label>
                                        <input type="text" id="secondary_school_gpa" class="form-control"
                                            name="secondary_school_gpa" placeholder="أدخل معدل المرحلة الثانوية"
                                            value="{{ $student ? $student->secondary_school_gpa : '' }}">
                                    </div>

                                    {{-- المنطقة التعليمية --}}
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="educational_area" class="form-label">
                                            المنطقة التعليمية*
                                        </label>
                                        <input type="text" id="educational_area" class="form-control"
                                            name="educational_area" placeholder="أدخل المنطقة التعليمية"
                                            value="{{ $student ? $student->educational_area : '' }}">
                                    </div>

                                    {{--  سنة الحصول على الشهادة الثانوية --}}
                                    <div class="form-group col-12 col-sm-6 secondary_education">
                                        <label for="secondary_graduation_year" class="form-label">
                                            سنة الحصول على الشهادة الثانوية*
                                        </label>
                                        <input type="text" id="secondary_graduation_year" class="form-control"
                                            name="secondary_graduation_year"
                                            placeholder="أدخل سنة الحصول على الشهادة الثانوية"
                                            value="{{ $student ? $student->secondary_graduation_year : '' }}">
                                    </div>

                                    {{-- المدرسة --}}
                                    <div class="form-group col-12 col-sm-6 secondary_education">
                                        <label for="school" class="form-label">
                                            المدرسة*
                                        </label>
                                        <input type="text" id="school" class="form-control" name="school"
                                            placeholder="أدخل المدرسة"
                                            value="{{ $student ? $student->school : '' }}">
                                    </div>


                                    {{-- الجامعه --}}
                                    <div class="form-group col-12 col-sm-6 high_education">
                                        <label for="university" class="form-label">
                                            الجامعة*
                                        </label>
                                        <input type="text" id="university" class="form-control" name="university"
                                            placeholder="أدخل الجامعة"
                                            value="{{ $student ? $student->university : '' }}">
                                    </div>

                                    {{-- الكليه --}}
                                    <div class="form-group col-12 col-sm-6 high_education">
                                        <label for="faculty" class="form-label">
                                            الكلية*
                                        </label>
                                        <input type="text" id="faculty" class="form-control" name="faculty"
                                            placeholder="أدخل الكلية"
                                            value="{{ $student ? $student->faculty : '' }}">
                                    </div>

                                    {{-- التخصص  --}}
                                    <div class="form-group col-12 col-sm-6 high_education">
                                        <label for="education_specialization" class="form-label">
                                            التخصص*
                                        </label>
                                        <input type="text" id="education_specialization" class="form-control"
                                            name="education_specialization" placeholder="أدخل التخصص"
                                            value="{{ $student ? $student->education_specialization : '' }}">
                                    </div>

                                    {{-- سنة التخرج --}}
                                    <div class="form-group col-12 col-sm-6 high_education">
                                        <label for="graduation_year" class="form-label">
                                            سنة التخرج*
                                        </label>
                                        <input type="text" id="graduation_year" class="form-control"
                                            name="graduation_year" placeholder="أدخل سنة التخرج"
                                            value="{{ $student ? $student->graduation_year : '' }}">
                                    </div>

                                    {{-- المعدل --}}
                                    <div class="form-group col-12 col-sm-6 high_education">
                                        <label for="gpa" class="form-label">
                                            المعدل*
                                        </label>
                                        <input type="text" id="gpa" class="form-control" name="gpa"
                                            placeholder="أدخل المعدل "
                                            value="{{ $student ? $student->gpa : '' }}">
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

                                        <div class="row mr-5 mt-5">
                                            {{-- working status --}}
                                            <div class="col-sm-4 col">
                                                <label for="working">
                                                    <input type="radio" id="working" name="status" value="working"
                                                        required>
                                                    {{ trans('application_form.working') }}
                                                </label>
                                            </div>

                                            {{-- not working status --}}
                                            <div class="col">
                                                <label for="not_working">
                                                    <input type="radio" id="not_working" name="status"
                                                        value="not_working" required>
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
                                                <input type="text" id="job_title" name="job"
                                                    class="form-control" placeholder="أدخل الوظيفة"
                                                    value="{{ $student ? $student->job : '' }}">
                                            </div>

                                            <div class="form-group col-12 col-sm-6">
                                                <label for="employment_type">جهة العمل*</label>
                                                <select id="employment_type" name="job_type" class="form-control">
                                                    <option value="" selected disabled>اختر جهة العمل</option>
                                                    <option value="governmental">حكومية</option>
                                                    <option value="private">خاصة</option>
                                                </select>
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

                                            <div class="row mr-5 mt-5">
                                                {{-- deaf --}}
                                                <div class="col-sm-4 col">
                                                    <label for="deaf">
                                                        <input type="radio" id="deaf" name="deaf"
                                                            value="1" required>
                                                        نعم
                                                    </label>
                                                </div>

                                                {{-- not deaf --}}
                                                <div class="col">
                                                    <label for="not_deaf">
                                                        <input type="radio" id="not_deaf" name="deaf"
                                                            value="0" required>
                                                        لا
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 row">
                                        {{-- disabled --}}
                                        <div class="form-group col-12 col-sm-6">
                                            <label>هل أنت من ذوي الإعاقة؟*</label>

                                            <div class="row mr-5 mt-5">
                                                {{-- disabled --}}
                                                <div class="col-sm-4 col">
                                                    <label for="disabled">
                                                        <input type="radio" id="disabled" name="disabled"
                                                            value="1" required>
                                                        نعم
                                                    </label>
                                                </div>

                                                {{-- not disabled --}}
                                                <div class="col">
                                                    <label for="not_disabled">
                                                        <input type="radio" id="not_disabled" name="disabled"
                                                            value="0" required>
                                                        لا
                                                    </label>
                                                </div>
                                            </div>

                                        </div>

                                        {{-- disabled type --}}
                                        <div class="form-group col-12 col-sm-6" id="disabled_type_section"
                                            style="display: none">
                                            <label for="disabled_type">{{ 'حدد نوع الإعاقة*' }}</label>
                                            <select id="disabled_type" name="disabled_type"
                                                class="form-control">
                                                <option value="" class="placeholder" disabled="" selected>أختر
                                                    نوع
                                                    الإعاقة
                                                </option>
                                                <option value="option1">اوبشن 1 </option>
                                                <option value="option2">اوبشن 2</option>
                                            </select>
                                        </div>

                                    </div>

                                    <div class="col-12 row">
                                        {{-- healthy status --}}
                                        <div class="form-group col-12 col-sm-6">
                                            <label for="healthy">{{ trans('application_form.health_proplem') }}؟*</label>

                                            <div class="row mr-5 mt-5">
                                                {{-- healthy --}}
                                                <div class="col-sm-4 col">
                                                    <label for="healthy">
                                                        <input type="radio" id="healthy" name="healthy"
                                                            value="1" required>
                                                        نعم
                                                    </label>
                                                </div>

                                                {{-- not healthy --}}
                                                <div class="col">
                                                    <label for="not_healthy">
                                                        <input type="radio" id="not_healthy" name="healthy"
                                                            value="0" required>
                                                        لا
                                                    </label>
                                                </div>
                                            </div>

                                        </div>

                                        {{-- healthy problem --}}
                                        <div class="form-group col-12 col-sm-6" id="healthy_problem_section"
                                            style="display: none">
                                            <label for="healthy_problem">ادخل المشكلة الصحية*</label>
                                            <input type="text" id="healthy_problem" class="form-control"
                                                name="healthy_problem" placeholder="ادخل المشكلة الصحية"
                                                value="{{ $student ? $student->healthy_problem : '' }}">

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
                                            value="{{ $student ? $student->referral_person : '' }}"
                                            placeholder="أدخل الأسم الثنائي" required class="form-control">
                                    </div>

                                    <div class="form-group col-12 col-sm-6">
                                        <label for="relation">{{ trans('application_form.referral_state') }}*</label>
                                        <input type="text" id="relation" name="relation"
                                            value="{{ $student ? $student->relation : '' }}"
                                            placeholder="أدخل صلة القرابة" required class="form-control">
                                    </div>

                                    <div class="form-group col-12 col-sm-6">
                                        <label for="referral_email">{{ trans('application_form.email') }}*</label>
                                        <input type="email" id="referral_email" name="referral_email"
                                            value="{{ $student ? $student->referral_email : '' }}"
                                            placeholder="أدخل بريد الكتروني" required class="form-control">

                                    </div>

                                    <div class="form-group col-12 col-sm-6">
                                        <label>{{ trans('application_form.phone') }}*</label>
                                        <input type="tel" id="referral_phone" placeholder="أدخل جوال"
                                            name="referral_phone" value="{{ $student ? $student->referral_phone : '' }}"
                                            class="form-control">
                                    </div>
                                </section>
                            </section>

                            {{-- about us --}}
                            <div class="form-group col-12">
                                <label>{{ trans('application_form.heard_about_us') }}*</label><br>
                                <label for="snapchat">
                                    <input type="radio" id="snapchat" name="about_us" value="snapchat">
                                    {{ trans('application_form.snapchat') }}
                                </label><br>
                                <label for="twitter">
                                    <input type="radio" id="twitter" name="about_us" value="twitter">
                                    {{ trans('application_form.twitter') }}
                                </label><br>
                                <label for="friend">
                                    <input type="radio" id="friend" name="about_us" value="friend">
                                    {{ trans('application_form.friend') }}
                                </label><br>
                                <label for="instagram">
                                    <input type="radio" id="instagram" name="about_us" value="instagram">
                                    {{ trans('application_form.instagram') }}
                                </label><br>
                                <label for="facebook">
                                    <input type="radio" id="facebook" name="about_us" value="facebook">
                                    {{ trans('application_form.facebook') }}
                                </label><br>
                                <label for="other">
                                    <input type="radio" id="other" name="about_us" value="other">
                                    {{ trans('application_form.other') }}
                                </label><br>
                                <label id="otherLabel"style="display:none">أدخل المصدر</label>
                                <input type="text" id="otherInput" placeholder="" name="other_about_us"
                                    class="form-control" style="display:none"><br>


                                <label>
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

            if (select && hiddenLabel && hiddenInput) {
                var categoryId = select.value;
                var categoryBundles = bundles[categoryId];

                if (categoryBundles) {
                    var options = categoryBundles.map(function(bundle) {
                        return '<option value="' + bundle.id + '">' + bundle.title + '</option>';
                    }).join('');

                    hiddenInput.outerHTML = '<select id="bundle_id" name="bundle_id"  class="form-control">' +
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

                if (selectedText.trim() == "دبلوم متوسط") {
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

            if (select.value === "أخرى") {
                anotherCountrySection.style.display = "block";
                anotherCountryOption.value = hiddenInput2.value;
            } else {
                anotherCountrySection.style.display = "none";
                anotherCountryOption.value = "أخرى";

            }
            if (select && cityLabel && town) {
                if (select.value === "السعودية") {
                    town.outerHTML = '<select id="town" name="town"  class="form-control">' +
                        '<option value="الرياض" selected="selected">الرياض</option>' +
                        '<option value="جدة">جدة</option>' +
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
                        '<input type="text" id="town" name="town" placeholder="اكتب مدينه السكن الحاليه"  class="form-control">';
                }
            }
        }
        function setCountry(){
            let anotherCountrySection = document.getElementById("anotherCountrySection");
            let anotherCountryOption = document.getElementById("anotherCountry");
            let another_country = document.getElementById("city");

            if(anotherCountrySection.style.display !="none"){
                // nationality.value = other_nationality.value;
                anotherCountryOption.value = another_country.value;

            }
        }
        toggleHiddenInputs();
    </script>

    {{--  healthy section toggle--}}
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
        function toggleNationality(){
            let other_nationality_section = document.getElementById("other_nationality_section");
            let nationality = document.getElementById("nationality");
            let other_nationality = document.getElementById("other_nationality");
            let anotherNationalityOption = document.getElementById("anotherNationality");
            if(nationality && nationality.value == "أخرى"){
                other_nationality_section.style.display = "block";

                // nationality.value = other_nationality.value;
                anotherNationalityOption.value = other_nationality.value;
            }
            else{
                other_nationality_section.style.display = "none";
                anotherNationalityOption.value = "أخرى";
            }
        }

        function setNationality(){
            let other_nationality_section = document.getElementById("other_nationality_section");
            let nationality = document.getElementById("nationality");
            let other_nationality = document.getElementById("other_nationality");
            let anotherNationalityOption = document.getElementById("anotherNationality");
            if(other_nationality_section.style.display !="none"){
                // nationality.value = other_nationality.value;
                anotherNationalityOption.value = other_nationality.value;

            }
        }

    </script>

    {{-- education section --}}
    <script>
        function educationCountryToggle(){
            let anotherEducationCountrySection = document.getElementById("anotherEducationCountrySection");
            let anotherEducationCountry = document.getElementById("anotherEducationCountry");
            let anotherEducationCountryOption = document.getElementById("anotherEducationCountryOption");
            let educationalQualificationCountry = document.getElementById("educational_qualification_country");

            if(educationalQualificationCountry && educationalQualificationCountry.value == "أخرى"){
                anotherEducationCountrySection.style.display = "block";
                anotherEducationCountryOption.value = anotherEducationCountry.value;

            }else{
                anotherEducationCountrySection.style.display = "none";
                anotherEducationCountryOption.value = "أخرى";
            }

        }
        function setEducationCountry(){
            let anotherEducationCountrySection = document.getElementById("anotherEducationCountrySection");
            let anotherEducationCountry = document.getElementById("anotherEducationCountry");
            let anotherEducationCountryOption = document.getElementById("anotherEducationCountryOption");
            let educationalQualificationCountry = document.getElementById("educational_qualification_country");

            if(anotherEducationCountrySection.style.display !="none"){
                anotherEducationCountryOption.value = anotherEducationCountry.value;
            }

        }
    </script>
@endpush
