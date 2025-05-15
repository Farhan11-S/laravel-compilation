<?php

namespace App\Enums;

enum SubscriptionTransactionStatus: string
{
    case BELUM_SELESAI = 'BELUM SELESAI';
    case MENUNGGU_KONFIRMASI = 'MENUNGGU KONFIRMASI';
    case SELESAI = 'SELESAI';
    case GAGAL = 'GAGAL';
    case TIDAK_AKTIF = 'TIDAK AKTIF';
}
