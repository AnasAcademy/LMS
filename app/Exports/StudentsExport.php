<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromCollection, WithHeadings, WithMapping
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
            'diploma',
            'created at',
            'Status',
            'Mobile',
            'Email',

        ];
    }

    /**
     * @inheritDoc
     */
    public function map($user): array
    {
        if ($user->student) {
            $diploma = '';
            $purchasedBundles = $user->purchasedFormBundle();

            if ($purchasedBundles) {
                foreach ($purchasedBundles as $purchasedBundle) {
                    $diploma .= ($purchasedBundle->bundle->title.' , ') ;
                }
            }


            return [
                $user->user_code,
                $user->student->ar_name,
                $user->student->en_name,
                $diploma,
                dateTimeFormat($user->created_at, 'j M Y - H:i'),
                $user->status,
                $user->mobile,
                $user->email,
            ];
        } else {
            return [
                $user->user_code,
                ($user->student ? $user->student->ar_name : $user->full_name),
                ($user->student ? $user->student->en_name : $user->full_name),
                'غير مسجل بعد',
                dateTimeFormat($user->created_at, 'j M Y - H:i'),
                $user->status,
                $user->mobile,
                $user->email,
            ];
        }

    }
}
