<?php

namespace App\Exports;

use App\Models\Resume;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SearchResumeExport implements FromQuery, WithMapping, WithHeadings
{
    use Exportable;
    private $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function map($resume): array
    {
        return [
            $resume->user_detail->first_name . ' ' . $resume->user_detail->last_name,
            $resume->user->email,
            $resume->user_detail->street_address,
            $resume->user_detail->postal_code,
            $resume->user_detail->city,
            $resume->user_detail->province,
            $resume->user_detail->country,
            $resume->user_detail->date_of_birth,
            $resume->user_detail->place_of_birth,
        ];
    }

    public function headings(): array
    {
        return [
            'Full Name',
            'Email',
            'Street Address',
            'Postal Code',
            'City',
            'Province',
            'Country',
            'Date of Birth',
            'Place of Birth',
        ];
    }
}
