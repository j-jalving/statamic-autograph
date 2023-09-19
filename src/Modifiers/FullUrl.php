<?php

namespace JJalving\Autograph\Modifiers;

use Statamic\Modifiers\Modifier;

class FullUrl extends Modifier
{
  /**
   * Create an absolute url from a relative url by adding the current domain name.
   *
   * @param mixed  $value    The value to be modified
   * @param array  $params   Any parameters used in the modifier
   * @param array  $context  Contextual values
   * @return mixed
   */
  public function index(mixed $value, array $params, array $context): mixed
  {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domainName = $_SERVER['HTTP_HOST'];
    return $protocol . $domainName . $value;
  }
}
