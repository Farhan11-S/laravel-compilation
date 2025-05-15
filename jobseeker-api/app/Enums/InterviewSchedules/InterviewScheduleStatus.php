<?php

namespace App\Enums\InterviewSchedules;

enum InterviewScheduleStatus: string
{
    case RESCHEDULED = 'rescheduled';
    case CANCELLED = 'cancelled';
    case APPROVED = 'approved';
}
