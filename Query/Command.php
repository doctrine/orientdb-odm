<?php

/**
 * Command class is a base class shared among all the command executable with
 * OrientDB's SQL synthax.
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query;

use Orient\Exception\Query\Command as CommandException;

abstract class Command implements \Orient\Contract\Query\Command
{
  protected   $tokens     = array();
  protected   $statement  = NULL;

  /**
   * Sets the token for the from clause. You can $append your values.
   *
   * @param array   $target
   * @param boolean $append
   */
  public function from(array $target, $append = true)
  {
    $this->setToken('Target', $target, $append);
  }

  /**
   * Returns the raw SQL query incapsulated by the current object.
   *
   * @return string
   */
  public function getRaw()
  {
    return $this->getValidStatement();
  }

  /**
   * Analyzing the command's SCHEMA, this method returns all the tokens
   * allocable in the command.
   *
   * @return array
   */
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

  public function getTokenValue($token)
  {   
    return $this->checkToken($this->tokenize($token));
  }

  /**
   * Deletes all the WHERE conditions in the current command.
   *
   * @return true
   */
  public function resetWhere()
  {
    $token                = 'Where';
    $token                = $this->tokenize($token);
    $this->checkToken($token);
    $this->tokens[$token] = array();

    return true;
  }

  /**
   * Adds a WHERE conditions into the current query.
   *
   * @param string  $condition
   * @param mixed   $value
   * @param boolean $append
   * @param string  $clause
   */
  public function where($condition, $value = NULL, $append = false, $clause = "WHERE")
  {
    $condition = str_replace("?", '"' .$value . '"', $condition);

    $this->setToken('Where', array("{$clause} " . $condition), $append);
  }

  protected function appendToken($token, $values)
  {
    foreach($values as $key => $value)
    {
      $this->tokens[$token][] = $value;
    }

    $this->tokens[$token] = array_unique($this->tokens[$token]);
  }

  /**
   * Checks if a token is set, returning it if it is.
   *
   * @param   string $token
   * @return  mixed
   * @throws  Exception\Query\Command\TokenNotFound
   */
  protected function checkToken($token)
  {
    if (array_key_exists($token, $this->tokens))
    {
      return $this->tokens[$token];
    }

    throw new CommandException\TokenNotFound($token, get_called_class());
  }

  /**
   * Returns the values to replace command's schema tokens.
   *
   * @return array
   */
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

  /**
   * Build the command replacing schema tokens with actual values and cleaning
   * the command synthax.
   *
   * @return string
   */
  protected function getValidStatement()
  {
    $statement = $this->replaceTokens($this->statement);
    $statement = str_replace("  ", " ", $statement);
    $statement = str_replace(", AND", " AND", $statement);
    $statement = str_replace(", OR", " OR", $statement);

    return rtrim(ltrim($statement));
  }

  /**
   * Replaces the tokens in the command's schema with their actual values in
   * the current object.
   *
   * @param   string  $statement
   * @return  string
   */
  protected function replaceTokens($statement)
  {
    $replaces = $this->getTokenReplaces();

    return str_replace(array_keys($replaces), $replaces, $statement);
  }

  /**
   * Sets a token, and can be appended with the given $append.
   *
   * @param   string                                  $token
   * @param   mixed                                   $tokenValue
   * @param   boolean                                 $append
   * @return  true
   * @todo    Nesting of IF kills kittahs
   */
  protected function setToken($token, $tokenValue, $append = true)
  {
    $token = $this->tokenize($token);
    $this->checkToken($token);

    if (is_array($this->tokens[$token]) && is_array($tokenValue))
    {
      if ($append)
      {
        $this->appendToken($token, $tokenValue);
      }
      else
      {
        $this->unsetToken($token);
        $this->tokens[$token] = $tokenValue;
      }
    }

    return true;
  }

  protected function unsetToken($token)
  {
    unset($this->tokens[$token]);
  }

  /**
   * Tokenizes a string.
   *
   * @param   string $token
   * @return  string
   */
  protected function tokenize($token)
  {
    return ":$token";
  }
}

