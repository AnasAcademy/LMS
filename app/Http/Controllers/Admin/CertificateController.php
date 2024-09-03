<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CertificatesExport;
use App\Http\Controllers\Controller;
use App\Mixins\Certificate\MakeCertificate;
use App\Models\Certificate;
use App\Models\QuizzesResult;
use App\Models\Translation\CertificateTemplateTranslation;
use App\User;
use App\Models\Bundle;
use App\Models\Sale;
use App\Models\Quiz;
use App\Models\CertificateTemplate;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_certificate_list');

        $query = Certificate::whereNull('webinar_id');

        $query = $this->filters($query, $request);

        $certificates = $query->with(
            [
                'quiz' => function ($query) {
                    $query->with('webinar');
                },
                'student',
                'quizzesResult'
            ]
        )->orderBy('created_at', 'desc')
            ->paginate(10);


        $data = [
            'pageTitle' => trans('admin/main.certificate_list_page_title'),
            'certificates' => $certificates,
            'student' => $filters['student'] ?? null,
            'instructor' => $filters['instructor'] ?? null,
            'quiz_title' => $filters['quiz_title'] ?? null,
        ];

        $teacher_ids = $request->get('teacher_ids');
        $student_ids = $request->get('student_ids');

        if (!empty($teacher_ids)) {
            $data['teachers'] = User::select('id', 'full_name')
                ->whereIn('id', $teacher_ids)->get();
        }

        if (!empty($student_ids)) {
            $data['students'] = User::select('id', 'full_name')
                ->whereIn('id', $student_ids)->get();
        }

        return view('admin.certificates.lists', $data);
    }
    
    public function purchase(){
        $purchased_certificates=Sale::whereNotNull('certificate_template_id')
        ->get();
       
        return view('admin.certificates.purchased_certificates',compact('purchased_certificates'));
    }

    private function filters($query, $request)
    {
        $filters = $request->all();

        if (!empty($filters['student_ids'])) {
            $query->whereIn('student_id', $filters['student_ids']);
        }

        if (!empty($filters['teacher_ids'])) {
            $quizzes = Quiz::whereIn('creator_id', $filters['teacher_ids'])->pluck('id')->toArray();

            if ($quizzes and is_array($quizzes)) {
                $query->whereIn('quiz_id', $quizzes);
            }
        }

        if (!empty($filters['quiz_title'])) {
            $quizzes = Quiz::whereTranslationLike('title', '%' . $filters['quiz_title'] . '%')->pluck('id')->toArray();
            $query->whereIn('quiz_id', $quizzes);
        }

        return $query;
    }

    public function CertificatesTemplatesList(Request $request)
    {
        $this->authorize('admin_certificate_template_list');

        removeContentLocale();

        $templates = CertificateTemplate::orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('admin/main.certificate_templates_list_page_title'),
            'templates' => $templates,
        ];

        return view('admin.certificates.new_templates', $data);
    }

    public function CertificatesNewTemplate()
    {
        $this->authorize('admin_certificate_template_create');
    
        removeContentLocale();
        $bundles = Bundle::get();
        $data = [
            'pageTitle' => trans('admin/main.certificate_new_template_page_title'),
            'bundles' => $bundles,
        ];
     
      //  dd($bundles); // Debug the data array
        return view('admin.certificates.new_templates', $data);
    }
    
    public function CertificatesTemplateStore(Request $request, $template_id = null)
    {
        $this->authorize('admin_certificate_template_create');

        $rules = [
            'title' => 'required',
            'image' => 'required',
            'body' => 'required',
            'type' => 'required|in:quiz,course,bundle',
            'bundles'=>'required',
            'position_x' => 'required',
            'position_y' => 'required',
            'font_size' => 'required',
            'text_color' => 'required',
        ];
        $this->validate($request, $rules);

        $data = $request->all();

        if ($data['status'] and $data['status'] == 'publish') { // set draft for other templates
            CertificateTemplate::where('status', 'publish')
                ->where('type', $data['type'])
                ->update([
                    'status' => 'draft'
                ]);
        }

        if (!empty($template_id)) {

            $template = CertificateTemplate::findOrFail($template_id);
            $template->update([
                'image' => $data['image'],
                'status' => $data['status'],
                'type' => $data['type'],
                'price' => $data['price'],
                'position_x' => $data['position_x'],
                'position_y' => $data['position_y'],
                'font_size' => $data['font_size'],
                'text_color' => $data['text_color'],
                'updated_at' => time(),
            ]);
            $template->bundle()->sync($request->input('bundles', []));
        } else {
            $template = CertificateTemplate::create([
                'image' => $data['image'],
                'status' => $data['status'],
                'type' => $data['type'],
                'price' => $data['price'],
                'position_x' => $data['position_x'],
                'position_y' => $data['position_y'],
                'font_size' => $data['font_size'],
                'text_color' => $data['text_color'],
                'created_at' => time(),
            ]);
             $template->bundle()->sync($request->input('bundles', []));
        }

        CertificateTemplateTranslation::updateOrCreate([
            'certificate_template_id' => $template->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
            'body' => $data['body'],
            'rtl' => $data['rtl'],
        ]);

        removeContentLocale();

        return redirect(getAdminPanelUrl().'/certificates/templates');
    }

    // public function CertificatesTemplatePreview(Request $request)
    // {
        
    //     $this->authorize('admin_certificate_template_create');

    //     $data = [
    //         'pageTitle' => trans('public.certificate'),
    //         'image' => $request->get('image'),
    //         'body' => $request->get('body'),
    //         'position_x' => (int)$request->get('position_x', 120),
    //         'position_y' => (int)$request->get('position_y', 100),
    //         'font_size' => (int)$request->get('font_size', 26),
    //         'text_color' => $request->get('text_color', '#e1e1e1'),
    //     ];

    //   //  dd($request->all(), $data);
    //     $isRtl = $request->get('rtl', false);

    //     $body = str_replace('[student]', 'student name', $data['body']);
    //     $body = str_replace('[course]', 'course name', $body);
    //     $body = str_replace('[grade]', 'xx', $body);
    //     $body = str_replace('[certificate_id]', 'xx', $body);
    //     $body = str_replace('[user_certificate_additional]', 'xx', $body);
    //     $body = str_replace('[date]', 'xx', $body);
    //     $body = str_replace('[instructor_name]', 'xx', $body);
    //     $body = str_replace('[duration]', 'xx', $body);

    //     //$data['body'] = $body;//mb_convert_encoding($body, 'HTML-ENTITIES', 'UTF-8');;

    //     if ($isRtl) {
    //         $Arabic = new \I18N_Arabic('Glyphs');
    //         $body = $Arabic->utf8Glyphs($body);
    //     }

    //     $imgPath = public_path($data['image']);
    //  //   dd($imgPath, file_exists($imgPath));
    //     $img = Image::make($imgPath);
       

    //     $img->text($body, $data['position_x'], $data['position_y'], function ($font) use ($data, $isRtl) {
    //         $fontPath = $isRtl ? public_path('assets/default/fonts/vazir/Vazir-Medium.ttf') : public_path('assets/default/fonts/Montserrat-Medium.ttf');
    //        // dd($fontPath);

    //         if (!file_exists($fontPath)) {
    //             throw new \Exception('Font file does not exist: ' . $fontPath);
    //         }
    //         $font->file(public_path('assets/default/fonts/vazir/Vazir-Medium.ttf'));
           
    //         $font->size($data['font_size']);
    //         $font->color($data['text_color']);
    //         $font->align($isRtl ? 'right' : 'left');
    //     });
    //     return $img->response('png');
    // }

    // public function CertificatesTemplatePreview(Request $request)
    // {
    //     // Load the background image
    //     $imgPath = public_path($request->get('image'));
    //     $img = Image::make($imgPath);
    
    //     // Define the dynamic data
    //     $studentName = "Wejdan Hamad Alnuami";
    //     $courseName = "Graphic Design and UI/UX";
    //     $text = "HAS BEEN AWARDED AN ONLINE DIPLOMA DEGREE OF";
    //     $text2 = "/WITH ABOVE EXCELLENT FIRST CLASS HONORS GRADE AND A";
    //     $issueDate = " ON THE 26th of February 2024";
    //     $gpa = "GPA of (5/5)";
    //     $fullText = $text2 . " " . $gpa;
    
    //     // Define font path
    //     $fontPath = public_path('assets/default/fonts/vazir/Vazir-Medium.ttf'); // Make sure this font file exists
    
    //     // Add Student Name
    //     $img->text($studentName, 800, 1250, function($font) use ($fontPath) {
    //         $font->file($fontPath);
    //         $font->size(50); // Adjust as needed
    //         $font->color('#000000');
    //         $font->align('center');
    //         $font->valign('top');
    //     });
 
 
 
    //     $img->text($text, 800, 1400, function($font) use ($fontPath) {
    //         $font->file($fontPath);
    //         $font->size(30); // Adjust as needed
    //         $font->color('#000000');
    //         $font->align('center');
    //         $font->valign('top');
    //     });
    
    //     // Add Course/Diploma Name
    //     $img->text($courseName, 800, 1450, function($font) use ($fontPath) {
    //         $font->file($fontPath);
    //         $font->size(40); // Adjust as needed
    //         $font->color('#000000');
    //         $font->align('center');
    //         $font->valign('top');
    //     });
 
 
    //             // Add Date of Issue
    //             $img->text($fullText, 800, 1510, function($font) use ($fontPath) {
    //                 $font->file($fontPath);
    //                 $font->size(30); // Adjust as needed
    //                 $font->color('#000000');
    //                 $font->align('center');
    //                 $font->valign('top');
    //             });
            
    
    //     // Add Date of Issue
    //     $img->text($issueDate, 800, 1570, function($font) use ($fontPath) {
    //         $font->file($fontPath);
    //         $font->size(35); // Adjust as needed
    //         $font->color('#000000');
    //         $font->align('center');
    //         $font->valign('top');
    //     });
    
   
    //     // Save the modified image
    //     //$img->save(public_path('path_to_save_the_certificate.jpg'));
    
    //     // Optionally, return the image directly
    //     return $img->response('jpg');
    // }



public function CertificatesTemplatePreview(Request $request)
{
    // Load the background image
    $imgPath = public_path($request->get('image'));
    $img = Image::make($imgPath);
    $textColor = $request->get('text_color', '#000000');

    $studentName = $request->get('studentName');
    $position_x_student = (int)$request->get('position_x_student', 835); // Default to 800 if not provided
    $position_y_student = (int)$request->get('position_y_student', 1250);
    $font_size_student = (int)$request->get('font_size_student', 40);

    $courseName = $request->get('courseName');
    $position_x_course = (int)$request->get('position_x_course', 835); // Default to 800 if not provided
    $position_y_course = (int)$request->get('position_y_course', 1450);
    $font_size_course = (int)$request->get('font_size_course', 40);

    $text1 = $request->get('text_1');
    $position_x_text_1 = (int)$request->get('position_x_text_1', 835); // Default to 800 if not provided
    $position_y_text_1 = (int)$request->get('position_y_text_1', 1400);
    $font_size_text_1 = (int)$request->get('font_size_text_1', 40);

    $issueDate = $request->get('date');
    $position_x_date = (int)$request->get('position_x_date', 835); // Default to 800 if not provided
    $position_y_date = (int)$request->get('position_y_date', 1510);
    $font_size_date = (int)$request->get('font_size_date', 40);

    // Define font path
    $fontPath = public_path('assets/default/fonts/Trajan-Regular.ttf'); // Make sure this font file exists

    // Helper function to get ordinal suffix
    function getOrdinal($number) {
        $suffix = [' th', ' st', ' nd', ' rd'];
        $lastDigit = $number % 10;
        $lastTwoDigits = $number % 100;

        if ($lastTwoDigits >= 11 && $lastTwoDigits <= 13) {
            return $number . $suffix[0];
        }

        return $number . ($suffix[$lastDigit] ?? $suffix[0]);
    }

    // Format the issue date
    $date = new \DateTime($issueDate);
    $day = $date->format('j');
    $month = $date->format('F');
    $year = $date->format('Y');
    $formattedDate = "ON THE " . getOrdinal($day) . " of " . $month . " " . $year;

    // Add Student Name
    $img->text($studentName, $position_x_student, $position_y_student, function($font) use ($fontPath, $textColor, $font_size_student) {
        $font->file($fontPath);
        $font->size($font_size_student); // Adjust as needed
        $font->color($textColor);
        $font->align('center');
        $font->valign('top');
    });

    // Add Text
    $img->text($text1, $position_x_text_1, $position_y_text_1, function($font) use ($fontPath, $textColor, $font_size_text_1) {
        $font->file($fontPath);
        $font->size($font_size_text_1); // Adjust as needed
        $font->color($textColor);
        $font->align('center');
        $font->valign('top');
    });

    // Add Course/Diploma Name
    $img->text($courseName, $position_x_course, $position_y_course, function($font) use ($fontPath, $textColor, $font_size_course) {
        $font->file($fontPath);
        $font->size($font_size_course); // Adjust as needed
        $font->color($textColor);
        $font->align('center');
        $font->valign('top');
    });

    // Add Date of Issue
    $img->text($formattedDate, $position_x_date, $position_y_date, function($font) use ($fontPath, $textColor, $font_size_date) {
        $font->file($fontPath);
        $font->size($font_size_date); // Adjust as needed
        $font->color($textColor);
        $font->align('center');
        $font->valign('top');
    });

    // Save the modified image
    //$img->save(public_path('path_to_save_the_certificate.jpg'));

    // Optionally, return the image directly
    return $img->response('jpg');
}






    public function CertificatesTemplatesEdit(Request $request, $template_id)
    {
        $this->authorize('admin_certificate_template_edit');

        $template = CertificateTemplate::findOrFail($template_id);

        $locale = $request->get('locale', app()->getLocale());
        storeContentLocale($locale, $template->getTable(), $template->id);
        $bundles=Bundle::get();
        $data = [
            'pageTitle' => trans('admin/main.certificate_template_edit_page_title'),
            'template' => $template,
            'bundles'=>$bundles,
        ];
        return view('admin.certificates.new_templates', $data);
    }

    public function CertificatesTemplatesDelete($template_id)
    {
        $this->authorize('admin_certificate_template_delete');

        $template = CertificateTemplate::findOrFail($template_id);

        $template->delete();

        return redirect(getAdminPanelUrl().'/certificates/templates');
    }

    public function CertificatesDownload($id)
    {
        $certificate = Certificate::findOrFail($id);

        $makeCertificate = new MakeCertificate();

        if ($certificate->type == 'quiz') {
            $quizResult = QuizzesResult::where('id', $certificate->quiz_result_id)
                ->where('status', QuizzesResult::$passed)
                ->with([
                    'quiz' => function ($query) {
                        $query->with(['webinar']);
                    },
                    'user'
                ])
                ->first();

            return $makeCertificate->makeQuizCertificate($quizResult);
        } else if ($certificate->type == 'course') {

            return $makeCertificate->makeCourseCertificate($certificate);
        }

        abort(404);
    }

    public function exportExcel(Request $request)
    {
        $this->authorize('admin_certificate_export_excel');

        $query = Certificate::query();

        $query = $this->filters($query, $request);

        $certificates = $query
            ->whereHas('quiz')
            ->with(
            [
                'quiz' => function ($query) {
                    $query->with('webinar');
                },
                'student',
                'quizzesResult'
            ]
        )->orderBy('created_at', 'desc')
            ->get();

        $export = new CertificatesExport($certificates);

        return Excel::download($export, 'certificates.xlsx');
    }
}
