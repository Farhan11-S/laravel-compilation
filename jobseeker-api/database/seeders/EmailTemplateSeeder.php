<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!EmailTemplate::firstWhere('identifier', 'user-applied')) {
            $template = EmailTemplate::create([
                'identifier' => 'user-applied',
                'greeting' => "Someone Applied!!",
                'lines' => [
                    '$user_name has applied at your company $company_name on one of the jobs you posted.',
                ],
                'salutation' => "Respond favorably to the candidate.",
            ]);

            $template->save();
        }

        if (!EmailTemplate::firstWhere('identifier', 'candidate-status-updated')) {
            $template = EmailTemplate::create([
                'identifier' => 'candidate-status-updated',
                'greeting' => 'This is your application result',
                'lines' => [
                    'I am $user_name, as the owner of $company_name, I apologize if there was a mistake in the previous word. We hereby announce the results of the examination of the CV/Resume that you sent to apply for the job position we posted.',
                ],
                'salutation' => "",
            ]);

            $template->save();
        }

        if (!EmailTemplate::firstWhere('identifier', 'post-register-congratulary')) {
            $template = EmailTemplate::create([
                'identifier' => 'post-register-congratulary',
                'greeting' => 'Selamat',
                'lines' => [
                    'Selamat, pendaftaran akun anda di Rheinjobs telah berhasil.',
                    'Terima kasih karena telah menggunakan aplikasi kami',
                ],
                'salutation' => "",
            ]);

            $template->save();
        }

        if (!EmailTemplate::firstWhere('identifier', 'post-apply-notification')) {
            $template = EmailTemplate::create([
                'identifier' => 'post-apply-notification',
                'greeting' => "Terima kasih",
                'lines' => [
                    'Resume anda akan dikirimkan kepada pemberi pekerjaan, kami akan langsung memberi tahu status pendaftaran anda berikutnya. Mohon ditunggu kabar baiknya.',
                ],
                'salutation' => "",
            ]);

            $template->save();
        }

        if (!EmailTemplate::firstWhere('identifier', 'job-created-notification')) {
            $template = EmailTemplate::create([
                'identifier' => 'job-created-notification',
                'greeting' => 'Halo',
                'lines' => [
                    'Kami dari pihak Rhenjobs telah membuat iklan untuk lowongan pekerjaan di perusahaan anda',
                    'Terima kasih karena telah menggunakan aplikasi kami',
                ],
                'salutation' => "",
            ]);

            $template->save();
        }

        if (!EmailTemplate::firstWhere('identifier', 'greeting-from-admin')) {
            $template = EmailTemplate::create([
                'identifier' => 'greeting-from-admin',
                'greeting' => 'Selamat',
                'lines' => [
                    'Selamat, pendaftaran akun anda di Rheinjobs telah berhasil.',
                    'Terima kasih karena telah menggunakan aplikasi kami',
                ],
                'salutation' => "",
            ]);

            $template->save();
        }
    }
}
