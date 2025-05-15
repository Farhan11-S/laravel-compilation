<?php

namespace App\Constants;

use ReflectionClass;

class Roles
{
  public const SUPERADMIN = 1;
  public const EMPLOYER = 2;
  public const JOB_SEEKER = 3;

  public static function getRoleName($id): mixed
  {
    $class = new ReflectionClass(__CLASS__);
    $constants = array_flip($class->getConstants());

    return $constants[$id];
  }

  public static function isImportantRole($id): bool
  {
    switch ($id) {
      case Roles::SUPERADMIN:
        return true;
      case Roles::EMPLOYER:
        return true;
      case Roles::JOB_SEEKER:
        return true;

      default:
        return false;
    }
  }
}
