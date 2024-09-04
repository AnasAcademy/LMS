<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Mixins\Certificate\MakeCertificate;
use App\Models\Bundle;
use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\Quiz;
use App\Models\QuizzesResult;
use App\Models\Reward;
use App\Models\RewardAccounting;
use App\Models\Sale;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class CertificateController extends Controller
{

    public function lists(Request $request)
    {
        //dd($request);
        $user = auth()->user();

        if (!$user->isUser()) {
            $query = Quiz::where('creator_id', $user->id)
                ->where('status', Quiz::ACTIVE);

            $activeQuizzes = $query->count();

            $quizzesIds = $query->pluck('id')->toArray();

            $achievementsCount = Certificate::whereIn('quiz_id', $quizzesIds)->count();

            $quizResultQuery = QuizzesResult::whereIn('quiz_id', $quizzesIds);
            $failedResults = deepClone($quizResultQuery)->where('status', QuizzesResult::$failed)->count();
            $avgGrade = deepClone($quizResultQuery)->where('status', QuizzesResult::$passed)->avg('user_grade');

            $userAllQuizzes = deepClone($query)->get();

            $query = $this->quizFilters(deepClone($query), $request);

            $quizzes = $query->with([
                'webinar',
                'certificates',
                'quizResults' => function ($query) {
                    $query->orderBy('id', 'desc');
                },
            ])->paginate(10);

            foreach ($quizzes as $quiz) {
                $quizResults = $quiz->quizResults;

                $quiz->avg_grade = $quizResults->where('status', QuizzesResult::$passed)->avg('user_grade');
            }

            $userWebinars = Webinar::select('id')
                ->where(function ($query) use ($user) {
                    $query->where('creator_id', $user->id)
                        ->orWhere('teacher_id', $user->id);
                })
                ->where('status', 'active')
                ->get();

            $data = [
                'pageTitle' => trans('quiz.certificates_lists'),
                'quizzes' => $quizzes,
                'activeQuizzes' => $activeQuizzes,
                'achievementsCount' => $achievementsCount,
                'avgGrade' => round($avgGrade, 2),
                'failedResults' => $failedResults,
                'userWebinars' => $userWebinars,
                'userAllQuizzes' => $userAllQuizzes,
            ];

            return view('web.default.panel.certificates.list', $data);
        }

        abort(404);
    }
    
    
    public function certificateLists()
    {
        $salesWithCertificate = Sale::where('buyer_id', auth()->user()->id)
        ->whereNotNull('certificate_template_id')
        ->get();
      $certificateTemplatesArray = [];
        $titlesArray = [];
        
        foreach ($salesWithCertificate as $sale) {
            $certificateTemplate = $sale->certificate_template;
            
            if ($certificateTemplate) {
                $certificateTemplatesArray[] = $certificateTemplate;
        
                if ($certificateTemplate->translations->isNotEmpty()) {
                    $titlesArray[] = $certificateTemplate->translations[0]->title;
                } else {
                    $titlesArray[] = null;
                }
            }
        }
        $salesWithCertificate=$salesWithCertificate->toArray();
        // dd($salesWithCertificate[0]['created_at']);
        
        // dd($titlesArray);
         return view(getTemplate() . '.panel.certificates.certificate_list', compact('certificateTemplatesArray','salesWithCertificate'));
    }

    public function achievements(Request $request)
    {
        $user = auth()->user();

        $results = QuizzesResult::where('user_id', $user->id);

        $failedQuizzes = deepClone($results)->where('status', QuizzesResult::$failed)->count();
        $avgGrades = deepClone($results)->where('status', QuizzesResult::$passed)->avg('user_grade');

        if (!empty($request->get('grade'))) {
            $results->where('user_grade', $request->get('grade'));
        }

        $quizzesIds = $results->where('status', QuizzesResult::$passed)
            ->pluck('quiz_id')
            ->toArray();
        $quizzesIds = array_unique($quizzesIds);

        $query = Quiz::whereIn('id', $quizzesIds)
            ->where('status', Quiz::ACTIVE);

        $certificatesCount = deepClone($query)->count();

        $userAllQuizzes = deepClone($query)->get();

        $query = $this->quizFilters(deepClone($query), $request);

        $quizzes = $query->with([
            'webinar',
            'quizResults' => function ($query) {
                $query->orderBy('id', 'desc');
            },
        ])->paginate(10);


        $canDownloadCertificate = false;
        foreach ($quizzes as $quiz) {
            $userQuizDone = $quiz->quizResults;

            if (count($userQuizDone)) {
                $quiz->result = $userQuizDone->first();

                if ($quiz->result->status == 'passed') {
                    $canDownloadCertificate = true;
                }
            }

            $quiz->can_download_certificate = $canDownloadCertificate;
        }

    
        $webinarsIds =  $user->getAllPurchasedWebinarsIds();
        $userWebinars = Webinar::select('id')
            ->whereIn('id', $webinarsIds)
            ->get();

        foreach($userWebinars as $webinar){
            $group=$webinar->groups()->whereHas('enrollments',function($query) use($user){
                $query->where('user_id', $user->id);
            })->first();
           
            if ($group && !empty($group->end_date) && $group->end_date < now()) {
                $this->makeCourseCertificate($webinar->id);
            }
           
        }


       
        $bundlesIds =$user->purchasedBundles->pluck('bundle_id');
        $userbundles = Bundle::select('id')
        ->whereIn('id', $bundlesIds)
        ->get();
        //  dd($userbundles);  

        foreach($userbundles as $bundle){
            //dd($bundle); 
            if ($bundle && !empty($bundle->end_date) && $bundle->end_date < time()){$this->makeBundleCertificate($bundle->id);}
           
        }
        $certificates = Certificate::where('student_id', $user->id)
        ->with(['webinar', 'bundle'])->get(); // Eager load webinars and bundles
        
        $courseCertificates = $certificates->whereNotNull('webinar_id');
        $bundleCertificates = $certificates->whereNotNull('bundle_id');

        $data = [
            'pageTitle' => trans('quiz.my_achievements_lists'),
            'quizzes' => $quizzes,
            'failedQuizzes' => $failedQuizzes,
            'avgGrades' => round($avgGrades, 2),
            'certificatesCount' => $certificatesCount,
            'userWebinars' => $userWebinars,
            'userAllQuizzes' => $userAllQuizzes,
            'courseCertificates' => $courseCertificates,
            'bundleCertificates' => $bundleCertificates,
        ];

        return view(getTemplate() . '.panel.certificates.achievements', $data);
    }

    private function quizFilters($query, $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $webinar_id = $request->get('webinar_id');
        $quiz_id = $request->get('quiz_id');
        $grade = $request->get('grade');


        fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($webinar_id)) {
            $query->where('webinar_id', $webinar_id);
        }

        if (!empty($quiz_id)) {
            $query->where('id', $quiz_id);
        }

        return $query;
    }

    public function makeCertificate($quizResultId)
    {
        $user = auth()->user();

        $makeCertificate = new MakeCertificate();

        $quizResult = QuizzesResult::where('id', $quizResultId)
            ->where('user_id', $user->id)
            ->where('status', QuizzesResult::$passed)
            ->with(['quiz' => function ($query) {
                $query->with(['webinar']);
            }])
            ->first();

        if (!empty($quizResult)) {
            return $makeCertificate->makeQuizCertificate($quizResult);
        }

        abort(404);
    }


    public function makeCourseCertificate($WebinarId,$format ='png')

    {

       // dd($WebinarId);
        $user = auth()->user();

        $makeCertificate = new MakeCertificate();
        $course=Webinar::where('id', $WebinarId)->first();
      //  dd($course);
        if (!empty($course)) {
            return $makeCertificate->makeCourseCertificate($course,$format);
        }


       

        abort(404);
    }


    public function makeBundleCertificate($bundleId,$format ='png')

    {

      //dd($bundleId);
        $user = auth()->user();

        $makeCertificate = new MakeCertificate();
        $bunble=Bundle::where('id', $bundleId)->first();
      //  dd($course);
        if (!empty($bunble)) {
            return $makeCertificate->makebundleCertificate($bunble,$format);
        }


       

        abort(404);
    }





}
