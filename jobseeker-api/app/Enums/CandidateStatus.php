<?php

namespace App\Enums;

enum CandidateStatus: string
{
    case SAVED = 'saved';
    case WAITING_REVIEW = 'waiting_review';
    case REVIEWED = 'reviewed';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
}
