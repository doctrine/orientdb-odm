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
      $parts = explode(':', $arr);

      if (count($parts) === 2 && is_numeric($parts[0]) && is_numeric($parts[1]))
      {
        return true;
      }
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
   * @param   array $where
   * @return  string
   */
  public function formatWhere(array $where)
  {
    $where = $this->implode($where);
    $where = str_replace(", AND", " AND", $where);
    $where = str_replace(", OR", " OR", $where);

    return $where;
  }

  /**
   * Formats the ORDER BY clause.
   *
   * @param   array $orderBy
   * @return  string
   */
  public function formatOrderBy(array $orderBy)
  {
    return count($orderBy) ? "ORDER BY " . $this->implode($orderBy) : NULL;
  }

  /**
   * Formats the LIMIT clause.
   *
   * @param   array $limit
   * @return  string
   */
  public function formatLimit(array $limit)
  {
    return count($limit) ? "LIMIT {$limit[0]}" : NULL;
  }

  /**
   * Formats the RANGE clause.
   *
   * @param   array $range
   * @return  string
   */
  public function formatRange(array $range)
  {
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
    return count($fields) ? $this->implode($fields) : NULL;
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
          $values[$key] = "[" . $this->implode($value) . "]";
        }
        else
        {
          $values[$key] = array_shift($value);
        }
      }
      else
      {
        $values[$key] = '"' . $value . '"';
      }
    }

    return count($values) ? $this->implode($values) : NULL;
  }

  public function formatUpdates(array $updates)
  {
    $string = "";

    foreach ($updates as $key => $update)
    {
      $string .= ' ' . $key . ' = "' . $update . '",';
    }

    return substr($string, 0, strlen($string) - 1);
  }

  public function formatMapUpdates(array $updates)
  {
    $string = "";

    foreach ($updates as $key => $update)
    {
      $finalChar = '"';

      if (is_array($update) && count($update))
      {
        foreach ($update as $k => $value)
        {
          $update = $k . '", ' . $value;
        }

        $finalChar = NULL;
      }

      $string .= ' ' . $key . ' = "' . $update . $finalChar . ',';
    }

    return substr($string, 0, strlen($string) - 1);
  }

  public function formatRidUpdates(array $updates)
  {
    foreach ($updates as $key => $value)
    {
      if (is_array($value))
      {
        if (count($value) > 1)
        {
          $updates[$key] = "$key = [" . $this->implode($value) . "]";
        }
        else
        {
          $updates[$key] = array_shift($value);
        }
      }
      else
      {
        $updates[$key] = "$key = " . $value;
      }
    }

    return count($updates) ? $this->implode($updates) : NULL;
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
      $pattern = "/[^a-z|A-Z|0-9|:|$nonFilter]/";
      
      return preg_replace($pattern, "", $arr);
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
    return $this->implode($this->filterRegularChars($values, $nonFilter));
  }
}

