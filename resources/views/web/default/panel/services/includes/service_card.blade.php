<style>
    .service-card .img-cover {
        width: 150px;
    }
</style>
<div
    class="module-box dashboard-stats rounded-sm panel-shadow py-30 d-flex align-items-center justify-content-center mt-0 h-100 w-100">
    <div class="d-flex flex-column service-card" style="align-items: center;">
        <img src="{{ asset('assets/default/img/img.png') }}" class="img-cover" alt="anas academy">

        @isset($title)
            <h1 class="text-secondary font-weight-bold text-center pb-10 ">
                {{ $title }}
            </h1>
        @endisset

        @isset($description)
            <p class="text-gray font-weight-500 font-16 mb-5">
                {{ $description }}
            </p>
        @endisset

        @isset($price)
            <p class="text-dark font-weight-bold">

                @if ($price > 0)
                    {{ $price }} ريال سعودي
                @else
                    <span class="text-danger">هذة الخدمه مجانيه</span>
                @endif
            </p>

        @endisset

        @isset($newRequestUrl)
            <a target="_blank" rel="noopener noreferrer" class="btn btn-primary mt-10 px-50" style=""
                href="{{ $newRequestUrl }}">
                تقديم طلب
            </a>
        @endisset


        @isset($reviewOldRequestUrl)
            <a target="_blank" rel="noopener noreferrer" class="mt-10 text-decoration-underline font-weight-500"
                style="" href="{{ $reviewOldRequestUrl }}">
                مراجعة طلب سابق
            </a>
        @endisset
    </div>
</div>
