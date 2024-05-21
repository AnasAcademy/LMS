<?php

namespace App\Imports;

use Exception;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

use App\User;
use App\Student;
use App\BundleStudent;
use App\Models\Bundle;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Sale;
use App\Models\Code;

class StudentImport implements ToModel
{
    private $skipFirstRow = true;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if ($this->skipFirstRow) {
            $this->skipFirstRow = false;
            return null;
        }

        $diplomaCode = $row[8];

        $bundle = bundle::where('id', $diplomaCode)->first();

        if ($bundle) {
            $existUser = User::where('email', $row['2'])->first();


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
            }


            if ($existUser) {
                $user = $existUser;
            } else {
                // create user
                $user = User::create([
                    'role_name' => 'registered_user',
                    'role_id' => 13,
                    'mobile' => $row[3] ?? null,
                    'email' => $row[2] ?? null,
                    'full_name' => $row[0],
                    'status' => User::$active,
                    'verified' => 1,
                    'access_content' => 1,
                    'password' => Hash::make('anasAcademy@123'),
                    'affiliate' => 0,
                    'timezone' => getGeneralSettings('default_time_zone') ?? null,
                    'created_at' => time()
                ]);
            }

            if (empty($user->user_code)) {
                // update user code
                $user->update(['user_code' => $nextCode]);
                // update code
                $lastCode->update(['lst_sd_code' => $nextCode]);
            }

            if ($user->student) {
                $student = $user->student;
            } else {
                // create student
                $student = Student::create([
                    'user_id' => $user->id,
                    'ar_name' => $row[0],
                    'en_name' => $row[1],
                    'email' => $row[2],
                    'phone' => $row[3],
                    'mobile' => $row[4],
                    'birthdate' => $row[5],
                    'gender' => $row[6],
                    'identifier_num' => $row[7],
                    'nationality' => $row[9],
                    'country' => $row[10],
                    // 'area' => $row['area'],
                    // 'city' => $row['city'],
                    'town' => $row[11],
                    'educational_qualification_country' => $row[12],
                    'educational_area' => $row[13],
                    'university' => $row[14],
                    'faculty' => $row[15],
                    'education_specialization' => $row[16],
                    'graduation_year' => $row[17],
                    'gpa' => $row[18],
                    'school' => $row[19],
                    'secondary_school_gpa' => $row[20],
                    'secondary_graduation_year' => $row[21],
                    'deaf' => ($row[22]=='نعم') ? 1: 0 ,
                    'disabled_type' => $row[23] ?? null,
                    'healthy_problem' => $row[24],
                    'job' => $row[25] ?? null,
                    'job_type' => $row[26] ?? null,
                    'referral_person' => $row[27],
                    'relation' => $row[28],
                    'referral_email' => $row[29],
                    'referral_phone' => $row[30],
                    'about_us' => $row[31],
                    'created_at' => date('Y-m-d H:i:s')


                ]);
            }

            $bundleStudent = BundleStudent::where(['student_id' => $student->id, 'bundle_id' => $bundle->id])->first();
            if ($bundleStudent) {
                return null;
            }
            // apply bundle
            $bundleStudent = BundleStudent::create([
                'student_id' => $student->id,
                'bundle_id' => $bundle->id,
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
            $sale = Sale::create([
                'buyer_id' => $user->id,
                'seller_id' => $bundle->creator_id,
                'order_id' => $order->id,
                'bundle_id' => $bundle->id,
                'type' => 'form_fee',
                'form_fee' => 1,
                'manual_added' => true,
                'payment_method' => Sale::$credit,
                'amount' => 230,
                'total_amount' => 0,
                'created_at' => time(),
            ]);


            Session::flash('success', 'تم اضافه الطلبة بنجاح.');
        } else {
            throw new Exception( 'يوجد مشكلة في كود الدبلومة');
        }


        return null;
    }
}
