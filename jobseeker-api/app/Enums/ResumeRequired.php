<?php

namespace App\Enums;

enum ResumeRequired: string
{
    case YES = 'yes';
    case NO = 'no';
    case OPTIONAL = 'optional';
}
