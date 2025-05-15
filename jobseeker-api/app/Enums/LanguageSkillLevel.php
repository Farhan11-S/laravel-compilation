<?php

namespace App\Enums;

enum LanguageSkillLevel: string
{
    case BEGINNER = 'beginner';
    case INTERMEDIATE = 'intermediate';
    case ADVANCED = 'advanced';
    case FLUENT = 'fluent';
    case NATIVE = 'native';
}
