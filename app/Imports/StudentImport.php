<?php

namespace App\Imports;

use Exception;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Mail\SendNotifications;
use App\Models\Notification;
use App\User;
use App\Student;
use App\BundleStudent;
use App\Models\Bundle;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Sale;
use App\Models\Code;
use App\Models\Accounting;
use App\Models\StudyClass;
use App\Models\TicketUser;




class StudentImport implements ToModel
{
    private $skipFirstRow = true;
    private $currentRow = 1; // Initialize row counter
    private $errors = [];
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            if ($this->skipFirstRow) {
                $this->skipFirstRow = false;
                return null;
            }
            // Increment row counter
            $this->currentRow++;


            $diplomaCode = $row[8];

            $bundle = bundle::find($diplomaCode);

            if (!$bundle) {
                $this->errors[] = "في الصف رقم {$this->currentRow}: كود الدبلومة غير صحيح";
                return null;
            }
            $rules = [
                'ar_name' => 'required|string|regex:/^[\p{Arabic} ]+$/u|max:255|min:5',
                'en_name' => 'required|string|regex:/^[a-zA-Z\s]+$/|max:255|min:5',
                'email' => 'required|email|max:255|regex:/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/',
                'deaf' => 'required|in:نعم,لا',
                'gender' => 'required|in:male,female',
                'birthdate' => 'required|date_format:Y-m-d'
            ];

            $fileData = [
                'ar_name' => $row[0],
                'en_name' => $row[1],
                'email' => $row[2],
                'deaf' => $row[22],
                'gender' => $row[6],
                'birthdate' => $row[5],
            ];
            // validate imported data
            $validator = Validator::make($fileData, $rules);

            if ($validator->fails()) {
                $this->errors[] = "في الصف رقم {$this->currentRow}: "  . implode(', ', $validator->errors()->all());
                return null;
            }

            // find or create user if doesn't exist
            $user = User::firstOrCreate(['email' => $row[2]], [
                'role_name' => 'registered_user',
                'role_id' => 13,
                'full_name' => $row[0],
                'status' => User::$active,
                'verified' => 1,
                'access_content' => 1,
                'password' => Hash::make('anasAcademy123'),
                'affiliate' => 0,
                'timezone' => getGeneralSettings('default_time_zone'),
                'created_at'=>time()
            ]);

            // if the user was created newly send an email to him with email and password
            if ($user->wasRecentlyCreated) {
                $data['title'] = "انشاء حساب جديد";
                $data['body'] = " تهانينا تم انشاء حساب لكم في اكاديمية انس للفنون
                            <br>
                            <br>
                            يمكن تسجيل الدخول من خلال هذا الرابط
                            <a href='https://lms.anasacademy.uk/login' class='btn btn-danger'>اضغط هنا للدخول</a>
                            <br>
                            بإستخدام هذا البريد الإلكتروني وكلمة المرور
                            <br>
                            <span style='font-weight:bold;'>البريد الالكتروني: </span> $user->email
                            <br>
                             <span style='font-weight:bold;'>كلمة المرور: </span> anasAcademy123
                            <br>
                ";
                $this->sendEmail($user, $data);
            }

            // update user code
            if (empty($user->user_code)) {
                $code = $this->generateStudentCode();
                $user->update(['user_code' => $code]);

                // update code
                Code::latest()->first()->update(['lst_sd_code' => $code]);
            }

            // create student if doesn't exist
            $student = $user->student ?? Student::create([
                'user_id' => $user->id,
                'ar_name' => $row[0],
                'en_name' => $row[1],
                'email' => $row[2],
                'phone' => $row[3] ?? '000000',
                'mobile' => $row[4] ?? $row[3] ?? '0000',
                'birthdate' => $row[5] ?? '1999-01-01',
                'gender' => $row[6],
                'identifier_num' => $row[7] ?? '000000',
                'nationality' => $row[9] ?? 'سعودي/ة',
                'country' => $row[10] ?? 'السعودية',
                'town' => $row[11] ?? 'الرياض',
                'educational_qualification_country' => $row[12],
                'educational_area' => $row[13] ?? 'الرياض',
                'university' => $row[14],
                'faculty' => $row[15],
                'education_specialization' => $row[16],
                'graduation_year' => $row[17],
                'gpa' => $row[18],
                'school' => $row[19],
                'secondary_school_gpa' => $row[20],
                'secondary_graduation_year' => $row[21],
                'deaf' => ($row[22] == 'نعم') ? 1 : 0,
                'disabled_type' => $row[23] ?? null,
                'healthy_problem' => $row[24],
                'job' => $row[25] ?? null,
                'job_type' => $row[26] ?? null,
                'referral_person' => $row[27] ?? 'صديق',
                'relation' => $row[28] ?? 'صديق',
                'referral_email' => $row[29] ?? 'email@example.com',
                'referral_phone' => $row[30] ?? '0000000',
                'about_us' => $row[31] ?? 'facebook',
                'created_at' => date('Y-m-d H:i:s')


            ]);

            // check the user apply to this bundle before or not
            $bundleStudent = BundleStudent::where(['student_id' => $student->id, 'bundle_id' => $bundle->id])->first();
            if ($bundleStudent) {
                return null;
            }

            $class =  StudyClass::get()->last();
            if (!$class) {
                $class = StudyClass::create(['title' => "الدفعة الأولي"]);
            }
            // apply bundle for student
            $bundleStudent = BundleStudent::create([
                'student_id' => $student->id,
                'bundle_id' => $bundle->id,
                'class_id' => $class->id,
            ]);

            // create order
            $order = Order::create([
                'user_id' => $user->id,
                'status' => Order::$paid,
                'amount' => 230,
                'payment_method' => 'payment_channel',
                'tax' => 0,
                'total_discount' => 0,
                'total_amount' => 230,
                'product_delivery_fee' => null,
                'created_at' => time(),
            ]);

            // create order item
            $orderItem = OrderItem::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'webinar_id' => null,
                'bundle_id' => $bundle->id ?? null,
                'certificate_template_id' =>  null,
                'certificate_bundle_id' => null,
                'form_fee' => 1,
                'product_id' =>  null,
                'product_order_id' => null,
                'reserve_meeting_id' => null,
                'subscribe_id' => null,
                'promotion_id' => null,
                'gift_id' => null,
                'installment_payment_id' => null,
                'ticket_id' => null,
                'discount_id' => null,
                'amount' => 230,
                'total_amount' => 230,
                'tax' => null,
                'tax_price' => 0,
                'commission' => 0,
                'commission_price' => 0,
                'product_delivery_fee' => 0,
                'discount' => 0,
                'created_at' => time(),
            ]);

            // create sale
            $sale = Sale::createSales($orderItem, $order->payment_method);
            Accounting::createAccounting($orderItem);
            TicketUser::useTicket($orderItem);

            // create sale
            // $sale = Sale::create([
            //     'buyer_id' => $user->id,
            //     'seller_id' => $bundle->creator_id,
            //     'order_id' => $order->id,
            //     'bundle_id' => $bundle->id,
            //     'type' => 'form_fee',
            //     'form_fee' => 1,
            //     'manual_added' => true,
            //     'payment_method' => Sale::$credit,
            //     'amount' => 230,
            //     'total_amount' => 230,
            //     'created_at' => time(),
            // ]);


            // $data['title'] = 'رسوم حجز مقعد دراسي';
            // $data['body'] = " تهانينا تم سدادكم رسوم حجز مقعد دراسي بالأكاديمية بقيمة 230 ر.س";


            // $this->sendEmail($user, $data);

            // $this->sendNotification($user, $data);

            return null;

        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            return null; // Skip invalid row
        }
    }

    public function sendEmail($user, $data)
    {
        if (!empty($user) and !empty($user->email)) {
            Mail::to($user->email)->send(new SendNotifications(['title' => $data['title'] ?? '', 'message' => $data['body'] ?? '']));
        }
    }

    public function sendNotification($user, $data)
    {
        Notification::create([
            'user_id' => $user->id ?? 0,
            'sender_id' => auth()->id(),
            'title' => $data['title'] ?? '',
            'message' => $data['body'] ?? '',
            'sender' => Notification::$AdminSender,
            'type' => "single",
            'created_at' => time()
        ]);
    }

    public function generateStudentCode()
    {
        // USER CODE
        $lastCode = Code::latest()->first();
        if (!empty($lastCode)) {
            if (empty($lastCode->lst_sd_code)) {
                $lastCode->lst_sd_code = $lastCode->student_code;
            }
            $lastCodeAsInt = intval(substr($lastCode->lst_sd_code, 2));
            do {
                $nextCodeAsInt = $lastCodeAsInt + 1;
                $nextCode = 'SD' . str_pad($nextCodeAsInt, 5, '0', STR_PAD_LEFT);

                $codeExists = User::where('user_code', $nextCode)->exists();

                if ($codeExists) {
                    $lastCodeAsInt = $nextCodeAsInt;
                } else {
                    break;
                }
            } while (true);

            return $nextCode;
        }
        return 'SD00001';
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
