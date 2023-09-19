<?php

namespace JJalving\Autograph\Contracts;

use Statamic\Facades\Entry;
use Statamic\Facades\User;

interface UserFormatter
{
  /**
   * Get the display name for the given user
   *
   * @param mixed $user
   * @return string
   */
  public static function getDisplayName(User|Entry $user): mixed;
}
