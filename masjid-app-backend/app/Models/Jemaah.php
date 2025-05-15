<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jemaah extends Model
{
    protected $table = 'jemaah';

    protected $fillable = [
        'nama',
        'nik',
        'no_telp',
        'jenis_kelamin',
        'agama',
        'alamat',
        'pekerjaan',
        'clusters',
        'blok',
        'no_rumah',
        'pic',
        'keterangan',
        'submitted_by',
        'ktp_path',
        'nama_majikan',
    ];

    public function mustahik()
    {
        return $this->hasOne(Mustahik::class, 'jemaah_id', 'id');
    }
}
