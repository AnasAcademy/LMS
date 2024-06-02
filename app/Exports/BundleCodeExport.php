<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BundleCodeExport implements FromCollection, WithHeadings, WithMapping
{
    protected $bundles;

    public function __construct($bundles)
    {
        $this->bundles = $bundles;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->bundles;
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            'اسم البرنامج',
            'اسم الدبلومة',
            'كود الدبلومة'
        ];
    }

    /**
     * @inheritDoc
     */
    public function map($bundle): array
    {
        return [
            $bundle->category->title,
            $bundle->title,
            $bundle->id
        ];
    }
}
