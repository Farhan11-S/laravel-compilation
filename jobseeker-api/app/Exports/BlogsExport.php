<?php

namespace App\Exports;

use App\Models\Blog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BlogsExport implements FromCollection, WithHeadings
{

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = [
            'title' => '7 Pertanyaan Interview Programmer dan Cara Menjawabnya',
            'sub_title' => 'Dalam dunia yang semakin bergantung pada teknologi, peran programmer menjadi sangat krusial',
            'content' => 'Content Sample',
            'categories' => 'For Jobseeker|For Employer',
            'tags' => 'programmer|job interview'
        ];
        return collect([$data]);
    }

    public function headings(): array
    {
        $heading = [
            'Title',
            'Sub Title',
            'Content',
            'Categories',
            'Tags'
        ];

        return $heading;
    }
}
