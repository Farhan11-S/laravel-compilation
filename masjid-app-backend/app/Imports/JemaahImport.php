<?php

namespace App\Imports;

use App\Models\Jemaah;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Str;

class JemaahImport implements WithHeadingRow, WithProgressBar, OnEachRow
{
    use Importable;

    /**
     * @param Row $row
     *
     * @return void
     */
    public function onRow(Row $row)
    {
        if (empty($row['nama']) || empty($row['nik'])) {
            return;
        }

        if (Jemaah::where('nik', $row['nik'])->exists()) {
            return;
        }

        $jemaah = Jemaah::create([
            'nama' => $row['nama'],
            'nik' => $row['nik'],
            'no_telp' => $row['no_telp'] ?? '',
            'jenis_kelamin' => $row['jenis_kelamin'] ?? '',
            'agama' => $row['agama'] ?? '',
            'alamat' => $row['alamat'] ?? '',
            'pekerjaan' => $row['pekerjaan'] ?? '',
            'clusters' => $row['clusters'] ?? '',
            'blok' => $row['blok'] ?? '',
            'no_rumah' => $row['no_rumah'] ?? '',
            'pic' => $row['pic'] ?? '',
            'keterangan' => $row['keterangan'] ?? '',
            'submitted_by' => null,
        ]);

        $jemaah->mustahik()->create([
            'uuid' => Str::uuid(),
            'verified_by' => null,
            'is_disabled' => false,
        ]);
    }
}
