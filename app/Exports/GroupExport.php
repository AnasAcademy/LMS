<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GroupExport implements FromCollection, WithHeadings, WithMapping
{
    protected $enrollments;

    public function __construct($enrollments)
    {
        $this->enrollments = $enrollments;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->enrollments;
    }
    /**
     * @inheritDoc
     */
    public function headings(): array
    {

        return [
            'code',
            'Name',
            'Email',
            'created at',
            'Status',
            'Mobile',


        ];
    }

    /**
     * @inheritDoc
     */
    public function map($enrollment): array
    {


        return [

            $enrollment->user->user_code,
            $enrollment->user->student ? $enrollment->user->student->ar_name : null,
            $enrollment->user->email,
            Carbon::parse($enrollment->created_at)->format('Y-m-d | H:i'),
            $enrollment->user->status,
            $enrollment->user->mobile,
        ];


    }
}
