<?php

/*
 * This file is part of the Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Utility class
 *
 * @package    
 * @subpackage 
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Formatter;

class String
{
  public function filterRegularChars($string, $nonFilter = NULL)
  {
    $pattern = "/[^a-z|A-Z|0-9|:|@|#|$nonFilter]/";

    return preg_replace($pattern, "", $string);
  }

  public function filterRid($rid)
  {
    $parts = explode(':', $rid);

    if (count($parts) === 2 && is_numeric($parts[0]) && is_numeric($parts[1]))
    {
      return $rid;
    }

    return false;
  }
}

