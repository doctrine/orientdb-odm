<?php

/*
 * This file is part of the Doctrine\OrientDB package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class RidUpdates
 *
 * @package     Doctrine\OrientDB
 * @subpackage  Formatter
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Formatter\Query;

use       Doctrine\OrientDB\Query\Formatter\Query;
use Doctrine\OrientDB\Query\Validator\Rid as RidValidator;

class SetUpdates extends Query implements TokenInterface
{
    public static function format(array $values)
    {
        //$rids = array();
        //$validator = new RidValidator;
      $string = "";
      foreach($values as $key=>$value) {
        if ($key = self::stripNonSQLCharacters($key)) {
          if ($value === null) {
            $value = 'NULL';
          } 
          else if (is_int($value) || is_float($value)) {
            // Preserve content of $value as is
          } else if (is_bool($value)) {
            $value = $value ? 'TRUE' : 'FALSE';
          } elseif(is_array($value)) {
            $value = '[' . implode(',', $value) . ']';
          } else {
            $value = '"' . addslashes($value) . '"';
          }

            $string .= " $key = $value,";
        }
      }
        $final = substr($string, 0, strlen($string) - 1);
        if(strlen($final) > 0) {
          return $final;
        }
        else {
          return null;
        }

/*
        foreach ($values as $key => $value) {
            $key = self::stripNonSQLCharacters($key);
           // $rid = $validator->check($value, true);

            //if ($key && $rid) {
            //    $rids[$key] = "$key = $rid";
            //}
        }
		$query = $key." = '".$value."'";
        $rids = array($query);
        if ($rids) {
            return self::implode($rids);
        }
*/
        return null;
    }
}
