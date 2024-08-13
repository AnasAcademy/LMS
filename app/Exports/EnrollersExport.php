<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EnrollersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $users;
    protected $currency;

    public function __construct($users)
    {
        $this->users = $users;
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
            $purchasedBundles = $user->purchasedBundles;

            if ($purchasedBundles) {
                foreach ($purchasedBundles as $purchasedBundle) {
                        $diploma = $purchasedBundle->bundle->title;
                        $created_at=$purchasedBundle->created_at;

                }
            }


            return [
                $user->user_code,
                $user->student->ar_name,
                $user->student->en_name,
                $user->email,
                $diploma,
                dateTimeFormat($created_at, 'j M Y | H:i'),
                $user->status,
                $user->mobile,
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
                ''
            ];
        }

    }
}
