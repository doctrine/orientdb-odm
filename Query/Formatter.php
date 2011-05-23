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
 * The aim of a Formatter class is to manipulate token values and format them
 * in order to build valid OrientDB's SQL expressions.
 *
 * @package    
 * @subpackage 
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query;

use Orient\Contract\Query\Formatter as FormatterInterface;
use Orient\Formatter\String;

class Formatter implements FormatterInterface
{
  /**
   * Tokenizes a string.
   *
   * @param   string $token
   * @return  string
   */
  public function tokenize($token)
  {
    return ":{$token}";
  }

  /**
   * Untokenizes a string.
   *
   * @param   string $token
   * @return  string
   */
  public function untokenize($token)
  {
    return substr($token, 1);
  }

  /**
   * Formats the projections.
   *
   * @param   array $values
   * @return  string
   */
  public function formatProjections(array $values)
  {
    return $this->implodeRegular($values);
  }

  /**
   * Formats the properties.
   *
   * @param   array   $values
   * @return  string
   */
  public function formatProperty(array $values)
  { 
    return $this->implodeRegular($values);
  }

  /**
   * Formats the classes.
   *
   * @param   array   $values
   * @return  string
   */
  public function formatClass(array $values)
  {
    return $this->implodeRegular($values);
  }

  /**
   * Formats the permissions.
   *
   * @param   array   $values
   * @return  string
   */
  public function formatPermission(array $values)
  {
    return $this->implodeRegular($values);
  }

  /**
   * Formats the resources.
   *
   * @param   array   $values
   * @return  string
   */
  public function formatResource(array $values)
  {
    return $this->implodeRegular($values);
  }

  /**
   * Formats the rids, iterating through them returning the first valid one.
   *
   * @param   array   $values
   * @return  string
   */
  public function formatRid(array $values)
  {
    $values = array_filter($values, function ($arr) {
      return String::filterRid($arr);
    });

    return (count($values)) ? array_shift($values) : NULL;
  }

  /**
   * If there are classes, it returns them in squared braces.
   *
   * @param array $values
   * @return string
   */
  public function formatClassList(array $values)
  {
    if (count($values))
    {
      return "[" . $this->implodeRegular($values) . "]";
    }

    return NULL;
  }

  /**
   * Formats the roles.
   *
   * @param   array   $values
   * @return  string
   */
  public function formatRole(array $values)
  {
    return $this->implodeRegular($values);
  }

  /**
   * Formats the type.
   *
   * @param   array   $values
   * @return  string
   */
  public function formatType(array $values)
  {
    return $this->implodeRegular($values);
  }

  /**
   * Formats the linked.
   *
   * @param   array   $values
   * @return  string
   */
  public function formatLinked(array $values)
  {
    return $this->implodeRegular($values);
  }

  /**
   * Formats the inverse.
   *
   * @param   array   $values
   * @return  string
   */
  public function formatInverse(array $values)
  {
    return $this->implodeRegular($values);
  }

  /**
   * Formats the source class.
   *
   * @param   array   $values
   * @return  string
   */
  public function formatSourceClass(array $values)
  {
    return $this->implodeRegular($values);
  }

  /**
   * Formats the source property.
   *
   * @param   array   $values
   * @return  string
   */
  public function formatSourceProperty(array $values)
  {
    return $this->implodeRegular($values);
  }

  /**
   * Formats the destination class.
   *
   * @param   array   $values
   * @return  string
   */
  public function formatDestinationClass(array $values)
  {
    return $this->implodeRegular($values);
  }

  /**
   * Formats the destination property.
   *
   * @param   array   $values
   * @return  string
   */
  public function formatDestinationProperty(array $values)
  {
    return $this->implodeRegular($values);
  }

  /**
   * Formats the name.
   *
   * @param   array   $values
   * @return  string
   */
  public function formatName(array $values)
  {
    return $this->implodeRegular($values);
  }

  /**
   * Formats the target.
   *
   * @param   array $values
   * @return  string
   */
  public function formatTarget(array $values)
  {
    $values = $this->filterRegularChars($values);
    $count = count($values);

    if ($count)
    {
      if ($count > 1)
      { 
        return "[" .$this->implode($values) . "]";
      }

      return array_shift($values);
    }

    return NULL;
  }

  /**
   * Formats the where conditions.
   *
   * @param   array $values
   * @return  string
   */
  public function formatWhere(array $values)
  {
    $values = $this->implode($values);
    $values = str_replace(", AND", " AND", $values);
    $values = str_replace(", OR", " OR", $values);

    return $values;
  }

  /**
   * Formats the ORDER BY clause.
   *
   * @param   array $values
   * @return  string
   */
  public function formatOrderBy(array $values)
  {
    return count($values) ? "ORDER BY " . $this->implodeRegular($values, " ") : NULL;
  }

  /**
   * Formats the LIMIT clause.
   *
   * @param   array $values
   * @return  string
   */
  public function formatLimit(array $values)
  {
    $values = function () use ($values) {
      foreach ($values as $limit)
      {
        if (is_numeric($limit))
        {
          return $limit;
        }
      }

      return false;
    };

    return $values() ? "LIMIT " . $values() : NULL;
  }

  /**
   * Formats the RANGE clause.
   *
   * @param   array $values
   * @return  string
   */
  public function formatRange(array $values)
  {
    $range = array();

    foreach ($values as $rid)
    {
      $value = $this->formatRid(array($rid));

      if ($value)
      {
        $range[] = $value;
      }
    }

    $range = array_slice($range, 0, 2);

    return count($range) ? "RANGE " . $this->implode($range) : NULL;
  }

  /**
   * Formats the FIELDS clause.
   *
   * @param   array   $fields
   * @return  string
   */
  public function formatFields(array $fields)
  {
    return count($fields) ? $this->implodeRegular($fields) : NULL;
  }

  /**
   * Formats the VALUES clause.
   *
   * @param   array   $values
   * @return  string
   */
  public function formatValues(array $values)
  {
    foreach ($values as $key => $value)
    {
      if (is_array($value))
      {
        if (count($value) > 1)
        {
          $values[$key] = "[" . addslashes($this->implode($value)) . "]";
        }
        else
        {
          $values[$key] = addslashes(array_shift($value));
        }
      }
      else
      {
        $values[$key] = '"' . addslashes($value) . '"';
      }
    }

    return count($values) ? $this->implode($values) : NULL;
  }

  /**
   * Formats the updates.
   *
   * @param   array   $values
   * @return  string
   */
  public function formatUpdates(array $values)
  {
    $string = "";

    foreach ($values as $key => $update)
    {
      $key = String::filterRegularChars($key);

      if ($key)
      {
        $string .= ' ' . $key . ' = "' . addslashes($update) . '",';
      }
    }

    return substr($string, 0, strlen($string) - 1);
  }

  /**
   * Format the rid updates.
   *
   * @param   array   $values
   * @return  string
   */
  public function formatRidUpdates(array $values)
  {
    $rids = array();

    foreach ($values as $key => $value)
    {
      $key = String::filterRegularChars($key);
      $rid = String::filterRid($value);

      if ($key && $rid)
      {
        $rids[$key] = "$key = " . $rid;
      }
    }

    return count($rids) ? $this->implode($rids) : NULL;
  }

  /**
   * Removes whitespaces from the beginning and the end of the $text.
   *
   * @param   string $text
   * @return  string
   */
  public function btrim($text)
  {
    return rtrim(ltrim($text));
  }

  /**
   * Filters the array values leaving intact regular characters a-z and
   * integers.
   *
   * @param   array $values
   * @return  array
   */
  protected function filterRegularChars(array $values, $nonFilter = NULL)
  {
    return array_map(function ($arr) use ($nonFilter) {
      return String::filterRegularChars($arr, $nonFilter);
    }, $values);
  }

  /**
   * Implodes and array using a comma.
   *
   * @param   array $array
   * @return  string
   */
  protected function implode(array $array)
  {
    return implode(', ', $array);
  }

  /**
   * Implodes the $values in a string regularly formatted.
   *
   * @param   array   $values
   * @return  string
   */
  protected function implodeRegular(array $values, $nonFilter = NULL)
  {
    $values         = $this->filterRegularChars($values, $nonFilter);
    $nonEmptyValues = array();

    foreach ($values as $value)
    {
      if ($value !== '')
      {
        $nonEmptyValues[] = $value;
      }
    }

    return $this->implode($nonEmptyValues);
  }
}

