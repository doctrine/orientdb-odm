<?php

/**
 * Command class
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query;

abstract class Command implements \Orient\Contract\Query\Command
{
  public function from(array $target, $append = true)
  {
    $this->setToken('Target', $target, $append);
  }

  public function getRaw()
  {
    return $this->getValidStatement();
  }

  public static function getTokens()
  {
    $class  = get_called_class();
    $tokens = array();
    preg_match_all("/(\:\w+)/", $class::SCHEMA, $matches);

    foreach($matches[0] as $match)
    {
      $tokens[$match] = array();
    }

    return $tokens;
  }

  public function resetWhere()
  {
    if (array_key_exists(':Where', $this->tokens))
    {
      $this->tokens[':Where'] = array();

      return true;
    }

    throw new CommandException\TokenNotFound($token, __CLASS__);
  }

  public function where($condition, $value = NULL, $append = false, $clause = "WHERE")
  {
    $condition = str_replace("?", '"' .$value . '"', $condition);

    $this->setToken('Where', array("{$clause} " . $condition), $append);
  }

  protected function getValidStatement()
  {
    $statement = $this->replaceTokens($this->statement);
    $statement = str_replace("  ", " ", $statement);
    $statement = str_replace(", AND", " AND", $statement);
    $statement = str_replace(", OR", " OR", $statement);

    return rtrim($statement);
  }

  protected function getTokenReplaces()
  {
    $replaces = array();

    foreach ($this->tokens as $token => $value)
    {
      if (is_array($value))
      {
        $value = implode(', ', $value);
      }

      $replaces[$token] = $value;
    }

    return $replaces;
  }

  protected function replaceTokens($statement)
  {
    $replaces = $this->getTokenReplaces();

    return str_replace(array_keys($replaces), $replaces, $statement);
  }

  protected function setToken($token, $tokenValue, $append = true)
  {
    $token = ":$token";

    if (array_key_exists($token, $this->tokens))
    {
      if (is_array($this->tokens[$token]) && is_array($tokenValue))
      {
        if ($append)
        {
          foreach($tokenValue as $key => $value)
          {
            $this->tokens[$token][] = $value;
          }

          $this->tokens[$token] = array_unique($this->tokens[$token]);
        }
        else
        {
          unset($this->tokens[$token]);

          $this->tokens[$token] = $tokenValue;
        }
      }
      else
      {
        $this->tokens[$token] = $tokenValue;
      }

      return true;
    }

    throw new CommandException\TokenNotFound($token, __CLASS__);
  }

  protected function tokenize($token)
  {
    return ":$token";
  }
}

