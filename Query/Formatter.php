<?php

/**
 * The aim of a Formatter class is to manipulate token values and format them
 * in order to build valid OrientDB's SQL expressions.
 *
 * @package    
 * @subpackage 
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query;

class Formatter
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
   * @param   array $projections
   * @return  string
   */
  public function formatProjections(array $projections)
  {
    return $this->implode($projections);
  }

  /**
   * Formats the target.
   *
   * @param   array $target
   * @return  string
   */
  public function formatTarget(array $target)
  {
    $count = count($target);

    if ($count)
    {
      if ($count > 1)
      {
        return "[" .$this->implode($target) . "]";
      }

      return array_shift($target);
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
   * Removes whitespaces from the beginng and the end of the $text.
   *
   * @param   string $text
   * @return  string
   */
  public function btrim($text)
  {
    return rtrim(ltrim($text));
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
}

