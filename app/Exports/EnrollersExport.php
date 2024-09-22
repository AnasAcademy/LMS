<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EnrollersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $users;
    protected $batchId;
    protected $currency;

    public function __construct($users, $batchId = null)
    {
        $this->users = $users;
        $this->batchId = $batchId;
        $this->currency = currencySign();
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->users;
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {

        return [

            'Student code',
            'Arabic Name',
            'English Name',
            'Email',
            'diploma',
            'created at',
            'Status',
            'Mobile',
            'about_us'


        ];
    }

    /**
     * @inheritDoc
     */
    public function map($user): array
    {
        if ($user->student) {
            $diploma = '';
            $created_at='';
            $purchasedBundles = $user->purchasedBundles($this->batchId)->get();

            if ($purchasedBundles) {
                foreach ($purchasedBundles as $purchasedBundle) {
                        $diploma .= ($purchasedBundle->bundle->title . " , ") ;
                        $created_at .=(dateTimeFormat($purchasedBundle->created_at, 'j M Y | H:i') . " , ");;

                }
                $diploma = preg_replace('/,(?!.*,)/u', '', $diploma);
                $created_at = preg_replace('/,(?!.*,)/u', '', $created_at);
            }



            return [
                $user->user_code,
                $user->student->ar_name,
                $user->student->en_name,
                $user->email,
                $diploma,
                $created_at,
                $user->status,
                $user->mobile,
                $user->student->about_us
            ];
        } else {
            return [
                '',
                '',
                '',
                '',
                'غير مسجل بعد',
                '',
                '',
                '',
                ''
            ];
        }

    }
}
