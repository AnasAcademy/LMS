@extends(getTemplate().'.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/owl-carousel2/owl.carousel.min.css">
    <style>


        .container_form {
            margin-top: 20px;
            /* border: 1px solid #ddd; *//* Add border to the container */
            padding: 20px; /* Optional: Add padding for spacing */
            border-radius: 10px !important;
            box-shadow: 2px 5px 10px #ddd;
            margin: 60px auto;
        }
        .hidden-element {
          display: none;
        }
        .application{
                display: flex;
                flex-direction: column;
                align-content: stretch;
                justify-content: flex-start;
                align-items: center;
                flex-wrap: wrap;
            }
        .section1 .form-title{
            text-align:center !important;
            padding:10px;
            color: #5F2B80;
        }
        a{
            color:#ED1088;
        }
    </style>
@endpush

@section('content')
<div class="application container-fluid">
    <div class="col-lg-8 col-md-8 col-sm-6">
        <div class="col-lg-12 col-md-12 col-sm-6">
            <Section class="section1">
                <div class="container_form">
                    <!--Form Title-->
                    <h1 class="form-title" >نموذج قبول طلب جديد وحجز مقعد دراسي</h1>
                    <p style="padding: 40px 0;font-size:18px;font-weight:600;line-height:1.5em">
                    يجب الاطلاع على متطلبات القبول في البرامج قبل تقديم طلب قبول جديد
                    <a href="https://anasacademy.uk/admission/" style="color:#f70387 !important;" target="_blank">
                    اضغط هنا
                    </a>
                    </p>
                    <form action="/apply" method="POST" id="myForm">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $user->id }}">

                        <label for="application">{{ trans('application_form.application') }}</label>
                        <select id="mySelect1" name="category_id" required class="form-control" onchange="toggleHiddenInput()">
                             <option disabled selected hidden value="">اختار البرنامج المراد اللتسجيل فيه </option>
                             @foreach($category as $item)
                            <option value="{{$item->id}}">{{$item->title}} </option>
                            @endforeach
                        </select><br>
                        <label class="hidden-element" id="hiddenLabel1" for="name">التخصص المطلوب</label>
                        <input type="text" id="bundle_id" name="bundle_id"
                        required class="hidden-element form-control">

                        <label for="name">{{ trans('application_form.name') }}</label>
                        <input type="text" id="name" name="ar_name" placeholder="ادخل الإسم باللغه العربية فقط" required class="form-control"><br>

                        <label for="name_en">{{ trans('application_form.name_en') }}</label>
                        <input type="text" id="name_en" name="en_name" value="{{ $user->full_name }}"
                        placeholder="ادخل الإسم باللغه الإنجليزيه فقط" required class="form-control"><br>

                        <label  for="country">{{ trans('application_form.country') }}</label>
                        <select id="mySelect" name="country" required class="form-control" onchange="toggleHiddenInputs()">
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
                        <option value="أخرى">أخرى</option>
                        </select><br>
                        <div id="hiddenInputContainer2">
                                <label for="city" class="hidden-element" id="hiddenLabel2">ادخل البلد:</label>
                                <input type="text" id="city" name="city" class="hidden-element form-control" placeholder="ادخل دولتك" >
                        </div><br>
                        <div id="hiddenInputContainer">
                              <label for="area" class="hidden-element" id="hiddenLabel">المنطقة:</label>
                              <input type="text" id="area" name="area" class="hidden-element form-control" placeholder="اكتب المنطقة" >
                        </div><br>
                        <!--<label for="city">{{ trans('application_form.city') }}</label>-->
                        <!--<input type="text" id="name_en" name="name_en" placeholder="اكتب مدينه السكن الحاليه" required class="form-control"><br>-->
                         <div id="cityContainer">
                            <label for="cityInput" id="cityLabel">{{ trans('application_form.city') }}</label>
                            <input type="text" id="cityInput" name="cityInput" placeholder="اكتب مدينه السكن الحاليه" required class="form-control">
                          </div>

                        <label for="email">{{ trans('application_form.email') }}</label>
                        <input type="email" id="email" name="email" value="{{ $user->email }}"
                        placeholder="تسجيل البريد الإلكتروني" required class="form-control"><br>

                        <label for="birthday">{{ trans('application_form.birthday') }}</label>
                        <input type="date" id="birthday" name="birthdate" required class="form-control"><br>

                        <label for="phone">{{ trans('application_form.phone') }}</label>
                        <input type="tel" id="phone" name="phone" value="{{ $user->mobile }}" class="form-control"><br>

                        <label for="deaf">{{ trans('application_form.deaf_patient') }}</label>
                        <select id="deaf" name="deaf" required class="form-control">
                            <option value="" class="placeholder" disabled="" selected >اختر </option>
                            <option value="1">نعم</option>
                            <option value="0">لا</option>
                        </select><br>

                        <label for="gender">{{ trans('application_form.gender') }}</label>
                        <select id="gender" name="gender" required class="form-control">
                            <option value="" class="placeholder" disabled="" selected >اختر </option>
                            <option value="male">انثي</option>
                            <option value="female">ذكر</option>
                        </select><br>

                        <label for="healthy">{{ trans('application_form.health_proplem') }}</label>
                        <select id="healthy" name="healthy" required class="form-control">
                            <option value="" class="placeholder" disabled="" selected >اختر </option>
                            <option value="1">نعم</option>
                            <option value="0">لا</option>
                        </select><br>

                        <label for="nationality">{{ trans('application_form.nationality') }}</label>
                        <select id="nationality" name="nationality" required class="form-control">
                        <option value="" class="placeholder" disabled="" selected="selected">اختر جنسيتك</option>
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
                        <option value="أخرى">أخرى</option>
                        </select><br>

                        <label>{{ trans('application_form.status') }}</label><br>
                        <label for="working">
                            <input type="radio" id="working" name="status" value="working" required>
                            {{ trans('application_form.working') }}
                        </label>

                        <label for="not_working">
                            <input type="radio" id="not_working" name="status" value="not_working" required>
                            {{ trans('application_form.not_working') }}
                        </label><br>
                        <div id="job" style="display:none;">
                            <label for="job_title">الوظيفة</label>
                            <input type="text" id="job_title" name="job_title" class="form-control"><br>

                            <label for="employment_type">جهة العمل</label>
                            <select id="employment_type" name="employment_type" class="form-control">
                                <option value="" selected disabled>اختر جهة العمل</option>
                                <option value="governmental">حكومية</option>
                                <option value="private">خاصة</option>
                            </select><br>
                        </div>

                        <label for="referral_person">{{ trans('application_form.referral_name') }}</label>
                        <input type="text" id="referral_person" name="referral_person" placeholder="أدخل اسم شخص للتواصل معه عند الضرورة" required class="form-control"><br>

                        <label for="relation">{{ trans('application_form.referral_state') }}</label>
                        <input type="text" id="relation" name="relation" placeholder="أدخل صلة القرابة" required class="form-control"><br>

                        <label for="referral_email">{{ trans('application_form.email') }}</label>
                        <input type="email" id="referral_email" name="referral_email" placeholder="أدخل بريد الكتروني" required class="form-control"><br>

                        <label>{{ trans('application_form.phone') }}</label>
                        <input type="tel" id="referral_phone" placeholder="أدخل جوال" name="referral_phone" class="form-control"><br>

                        <label>{{ trans('application_form.heard_about_us') }}</label><br>
                        <label for="snapchat">
                            <input type="radio" id="snapchat" name="about_us" value="snapchat" >
                            {{ trans('application_form.snapchat') }}
                        </label><br>
                        <label for="twitter">
                            <input type="radio" id="twitter" name="about_us" value="twitter" >
                            {{ trans('application_form.twitter') }}
                        </label><br>
                        <label for="friend">
                            <input type="radio" id="friend" name="about_us" value="friend" >
                            {{ trans('application_form.friend') }}
                        </label><br>
                        <label for="instagram">
                            <input type="radio" id="instagram" name="about_us" value="instagram" >
                            {{ trans('application_form.instagram') }}
                        </label><br>
                        <label for="facebook">
                            <input type="radio" id="facebook" name="about_us" value="facebook" >
                            {{ trans('application_form.facebook') }}
                        </label><br>
                        <label for="other">
                            <input type="radio" id="other" name="about_us" value="other" >
                            {{ trans('application_form.other') }}
                        </label><br>
                         <label id="otherLabel"style="display:none" >أدخل المصدر</label>
                         <input type="text" id="otherInput" placeholder="" name="other_about_us" class="form-control" style="display:none"><br>
                        
                        
                         <label>
                            <input type="checkbox" id="terms" name="terms" required>
                            <!--{{ trans('application_form.agree_terms_conditions') }}-->
                            اقر أنا المسجل بياناتي اعلاه بموافقتي على لائحة الحقوق والوجبات واحكام وشروط القبول والتسجيل، كما أقر بالتزامي التام بمضمونها، وبمسؤوليتي التامة عن أية مخالفات قد تصدر مني لها ، مما يترتب عليه كامل الأحقية للاكاديمية في مسائلتي عن تلك المخالفات والتصرفات المخالفة للوائح المشار إليها في عقد اتفاقية التحاق متدربـ/ـة <a target="_blank" href="https://anasacademy.uk/wp-content/uploads/2024/02/Contract.pdf">انقر هنا لمشاهدة</a>

                        </label><br>
                             @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach($errors->all() as $error)
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
    <script src="/assets/default/vendors/swiper/swiper-bundle.min.js"></script>
    <script src="/assets/default/vendors/owl-carousel2/owl.carousel.min.js"></script>
    <script src="/assets/default/vendors/parallax/parallax.min.js"></script>
    <script src="/assets/default/js/parts/home.min.js"></script>
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
  <script>
    var otherLabel = document.getElementById("otherLabel");
    var otherInput = document.getElementById("otherInput");

    var radioButtons = document.querySelectorAll('input[name="about_us"]');

    radioButtons.forEach(function (radioButton) {
        radioButton.addEventListener("change", function() {
            if (radioButton.value === "other" && radioButton.checked) {
                otherLabel.style.display = "block";
                otherInput.style.display = "block";
                radioButton.value=otherInput.value;
            } else {
                otherLabel.style.display = "none";
                otherInput.style.display = "none";
            }
        });
    });
    
    otherInput.addEventListener("change", function() {
      let radioButton=document.getElementById('other');
      radioButton.value=otherInput.value;
    })

    function toggleHiddenInput() {
      var bundles = @json($bundlesByCategory);
      var select = document.getElementById("mySelect1");
      var hiddenInput = document.getElementById("bundle_id");
      var hiddenLabel = document.getElementById("hiddenLabel1");

      if (select && hiddenLabel && hiddenInput) {
        var categoryId = select.value;
        var categoryBundles = bundles[categoryId];

        if (categoryBundles) {
          var options = categoryBundles.map(function(bundle) {
            return '<option value="' + bundle.id + '">' + bundle.title + '</option>';
          }).join('');

          hiddenInput.outerHTML = '<select id="bundle_id" name="bundle_id"  class="form-control">' +
            '<option value="" class="placeholder" disabled="" selected="selected">اختر التخصص </option>' +
            options +
            '</select>';
          hiddenLabel.style.display = "block";
        } else {
          hiddenInput.outerHTML = '<input type="text" id="bundle_id" name="bundle_id" placeholder="ادخل الإسم باللغه العربية فقط"  class="hidden-element form-control">';
          hiddenLabel.style.display = "none";
        }
      }
    }


    function toggleHiddenInputs() {
      var select = document.getElementById("mySelect");
      var hiddenInput = document.getElementById("area");
      var hiddenLabel = document.getElementById("hiddenLabel");
      var hiddenInput2 = document.getElementById("city");
      var hiddenLabel2 = document.getElementById("hiddenLabel2");
      var cityLabel = document.getElementById("cityLabel");
      var cityInput = document.getElementById("cityInput");
      if (select && hiddenInput && hiddenLabel && select.value !== "السعودية") {
        hiddenInput.style.display = "block";
        hiddenLabel.style.display = "block";
      } else {
        hiddenInput.style.display = "none";
        hiddenLabel.style.display = "none";
      }

      if (select.value === "أخرى") {
        hiddenInput2.style.display = "block";
        hiddenLabel2.style.display = "block";
        select.value = hiddenInput2.value;
      } else {
        hiddenInput2.style.display = "none";
        hiddenLabel2.style.display = "none";
      }
        if (select && cityLabel && cityInput) {
        if (select.value === "السعودية") {
          cityInput.outerHTML =  '<select id="cityInput" name="cityInput"  class="form-control">' +
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
          cityLabel.textContent = "{{ trans('application_form.city') }}";
        } else {
          cityInput.outerHTML = '<input type="text" id="cityInput" name="cityInput" placeholder="اكتب مدينه السكن الحاليه"  class="form-control">';
          cityLabel.textContent = "{{ trans('application_form.city') }}";
        }
      }
    }

    toggleHiddenInputs();
  </script>

@endpush
