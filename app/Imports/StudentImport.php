<?php

namespace App\Imports;

use App\Models\design_appointment;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

use App\User;
use App\Student;
use App\BundleStudent;
use App\Models\Bundle;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Sale;

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

        dd($row);
        $diplomaCode = $row['diploma_code'];


        if (!empty($appointmentDate) && !empty($user_count)) {

            $bundle = bundle::where('id', $diplomaCode)->first();

            if ($bundle) {
                $existUser = User::where('email', $row['email'])->first();
                if($existUser){
                    Session::flash('error', 'الطالب موجود بالفعل');
                    return null;
                }

                $user = User::create([
                    'role_name' => 'registered_user',
                    'role_id' => 13,
                    'mobile' => $row['phone'] ?? null,
                    'email' => $row['email'] ?? null,
                    'full_name' => $row['ar_name'],
                    'status' => User::$active,
                    'verified'=>1,
                    'access_content' => 1,
                    'password' => Hash::make('anasAcademy@123'),
                    'affiliate' =>0,
                    'timezone' => getGeneralSettings('default_time_zone') ?? null,
                    'created_at' => time()
                ]);

                $student = Student::create([
                    'user_id' =>$user->id,
                    'ar_name'=> $row['ar_name'],
                    'en_name' => $row['en_name'],
                    'email' => $row['email'],
                    'birthdate' => $row['birthdate'],
                    'gender' => $row['gender'],
                    'identifier_num' => $row['identifier_num'],
                    'phone' => $row['phone'],
                    'mobile' => $row['mobile'],
                    'country' => $row['country'],
                    // 'area' => $row['area'],
                    // 'city' => $row['city'],
                    'town' => $row['town'],
                    'educational_qualification_country' => $row['educational_qualification_country'],
                    'educational_area' => $row['educational_area'],
                    'university' => $row['university'],
                    'faculty' => $row['faculty'],
                    'education_specialization' => $row['education_specialization'],
                    'graduation_year' => $row['graduation_year'],
                    'gpa' => $row['gpa'],
                    'school' => $row['school'],
                    'secondary_school_gpa' => $row['secondary_school_gpa'],
                    'secondary_graduation_year' => $row['secondary_graduation_year'],
                    'deaf' => $row['deaf'] ?? null,
                    'disabled_type' => $row['disabled_type'] ?? null,
                    'healthy_problem' => $row['healthy_problem'],
                    'nationality' => $row['nationality'],
                    'job' => $row['job'] ?? null,
                    'job_type' => $row['job_type'] ?? null,
                    'referral_person' => $row['referral_person'],
                    'relation' => $row['relation'],
                    'referral_email' => $row['referral_email'],
                    'referral_phone' => $row['referral_phone'],
                    'about_us' => $row['about_us'],
                    'created_at' => date('Y-m-d H:i:s')


                ]);

                $bundleStudent = BundleStudent::create([
                    'user_id' => $student->id,
                    'bundle_id' => $bundle->id,
                ]);

                $order = Order::create([
                    'user_id' => $user->id,
                    'status' => Order::$paid,
                    'amount' => 230,
                    'tax' => 0,
                    'total_discount' => 0,
                    'total_amount' => 230,
                    'product_delivery_fee' => null,
                    'created_at' => time(),
                ]);

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

            }else{
                Session::flash('error', 'يوجد مشكلة في كود الدبلومة');

            }

        }

        return null;
    }
}
