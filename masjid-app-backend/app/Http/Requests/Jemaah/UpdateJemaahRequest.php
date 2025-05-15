<?php

namespace App\Http\Requests\Jemaah;

use App\Models\Jemaah;
use Illuminate\Foundation\Http\FormRequest;

class UpdateJemaahRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $jemaahId = $this->route('jemaah');
        $jemaah = Jemaah::find($jemaahId);
        return [
            'nama' => 'nullable|string',
            'nik' => 'nullable|string|unique:jemaah,nik,' . $jemaah->id,
            'no_telp' => 'nullable|string:unique:jemaah,no_telp',
            'jenis_kelamin' => 'nullable|string|in:LAKI-LAKI,PEREMPUAN',
            'agama' => 'nullable|string|in:ISLAM,KATHOLIK,CHRISTIAN,HINDU,KRISTEN,BUDHA,KONGHUCU',
            'alamat' => 'nullable|string',
            'pekerjaan' => 'nullable|string',
            'clusters' => 'nullable|string',
            'blok' => 'nullable|string',
            'no_rumah' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'ktp_path' => 'nullable|string',
            'nama_majikan' => 'nullable|string',
        ];
    }
}
