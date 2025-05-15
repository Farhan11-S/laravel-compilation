<?php

namespace App\Http\Requests\Jemaah;

use Illuminate\Foundation\Http\FormRequest;

class StoreJemaahRequest extends FormRequest
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
        return [
            'nama' => 'required|string',
            'nik' => 'required|string|unique:jemaah,nik',
            'no_telp' => 'required|string:unique:jemaah,no_telp',
            'jenis_kelamin' => 'required|string|in:LAKI-LAKI,PEREMPUAN',
            'agama' => 'nullable|string|in:ISLAM,KATHOLIK,CHRISTIAN,HINDU,KRISTEN,BUDHA,KONGHUCU',
            'alamat' => 'required|string',
            'pekerjaan' => 'required|string',
            // 'clusters' => 'required|string',
            'blok' => 'required|string',
            'no_rumah' => 'required|string',
            'keterangan' => 'nullable|string',
            'ktp_path' => 'nullable|string',
            'nama_majikan' => 'nullable|string',
        ];
    }
}
