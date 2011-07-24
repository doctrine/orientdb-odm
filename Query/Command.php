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
 * Command class is a base class shared among all the command executable with
 * OrientDB's SQL synthax.
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query;

use Orient\Exception\Query\Command as CommandException;
use Orient\Contract\Formatter\Query as QueryFormatter;
use Orient\Formatter\Query as Formatter;
use Orient\Exception;
use Orient\Contract\Query\Command as CommandContract;

abstract class Command implements CommandContract
{
    protected $tokens       = array();
    protected $formatters   = array();
    protected $statement    = NULL;
    protected $formatter    = NULL;

    /**
     * Builds a new object, creating the SQL statement from the class SCHEMA
     * and initializing the tokens.
     */
    public function __construct()
    {
        $class              = get_called_class();
        $this->statement    = $class::SCHEMA;
        $this->tokens       = $this->getTokens();
    }

    /**
     * Sets a where token using the AND operator.
     * If the $condition contains a "?", it will be replaced by the $value.
     *
     * @param   string $condition
     * @param   string $value
     * @return  Command
     */
    public function andWhere($condition, $value = NULL)
    {
        return $this->where($condition, $value, true, "AND");
    }

    /**
     * Sets the token for the from clause. You can $append your values.
     *
     * @param array   $target
     * @param boolean $append
     */
    public function from(array $target, $append = true)
    {
        $this->setTokenvalues('Target', $target, $append);

        return $this;
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
        $class = get_called_class();
        $tokens = array();
        preg_match_all("/(\:\w+)/", $class::SCHEMA, $matches);

        foreach ($matches[0] as $match) {
            $tokens[$match] = array();
        }

        return $tokens;
    }

    /**
     * Returns the value of a token.
     *
     * @param   string $token
     * @return  mixed
     */
    public function getTokenValue($token)
    {
        return $this->checkToken($this->tokenize($token));
    }

    /**
     * Sets a where token using the OR operator.
     * If the $condition contains a "?", it will be replaced by the $value.
     *
     * @param   string $condition
     * @param   string $value
     * @return  Command
     */
    public function orWhere($condition, $value = NULL)
    {
        return $this->where($condition, $value, true, "OR");
    }

    /**
     * Deletes all the WHERE conditions in the current command.
     *
     * @return true
     */
    public function resetWhere()
    {
        $this->clearToken('Where');

        return true;
    }

    /**
     * Sets the internal query formatter object.
     *
     * @param QueryFormatter $formatter
     */
    public function setFormatter(QueryFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * Adds a WHERE conditions into the current query.
     *
     * @param string  $condition
     * @param mixed   $value
     * @param boolean $append
     * @param string  $clause
     * @todo  find a way to specify multiple ?
     * @todo  data cleaning here through addslashes: this is a job for a formatter
     */
    public function where($condition, $value = NULL, $append = false, $clause = "WHERE")
    {
        $condition = str_replace("?", '"' . addslashes($value) . '"', $condition);
        $this->setTokenValues('Where', array("{$clause} " . $condition), $append, false, false);

        return $this;
    }

    /**
     * Appends a token to the query, without deleting existing values for the
     * given $token.
     *
     * @param string  $token
     * @param mixed   $values
     * @param boolean $first
     */
    protected function appendToken($token, $values, $first = false)
    {
        foreach ($values as $key => $value) {
            if ($first) {
                array_unshift($this->tokens[$token], $value);
            } else {
                $method = "appendTokenAs" . ucfirst(gettype($key));
                $this->$method($token, $key, $value);
            }
        }

        $this->tokens[$token] = array_unique($this->tokens[$token], SORT_REGULAR);
    }

    /**
     * Appends $value to the query $token, using $key to identify the $value in
     * the token array.
     * With this method you set a token value and can retrieve it by its key.
     *
     * @param string  $token
     * @param string  $key
     * @param mixed   $value
     */
    protected function appendTokenAsString($token, $key, $value)
    {
        $this->tokens[$token][$key] = $value;
    }

    /**
     * Appends $value to the query $token.
     *
     * @param string  $token
     * @param string  $key
     * @param mixed   $value
     */
    protected function appendTokenAsInteger($token, $key, $value)
    {
        $this->tokens[$token][] = $value;
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
        if (array_key_exists($token, $this->tokens)) {
            return $this->tokens[$token];
        }

        throw new CommandException\TokenNotFound($token, get_called_class());
    }

    /**
     * Clears the value of a token.
     *
     * @param string $token
     */
    protected function clearToken($token)
    {
        $token = $this->tokenize($token);
        $this->checkToken($token);
        $this->tokens[$token] = array();
    }

    /**
     * Returns a brand new instance of a Formatter in order to format query
     * tokens.
     *
     * @return  QueryFormatter
     */
    protected function getFormatter()
    {
        return $this->formatter ?: new Formatter();
    }

    /**
     * Returns the formatters for this query's tokens.
     *
     * @return Array
     */
    protected function getTokenFormatters()
    {
        return array(
            'Target'    => "Orient\Formatter\Query\Target",      
            'Where'     => "Orient\Formatter\Query\Where",  
            'Class'     => "Orient\Formatter\Query\Regular",
            'Property'  => "Orient\Formatter\Query\Regular",
            'Type'      => "Orient\Formatter\Query\Regular",
            'Rid'       => "Orient\Formatter\Query\Rid",
        );
    }
    
    /**
     * Returns the formatter for a particular token.
     *
     * @param  string $token
     * @return Array
     * @throws string
     */
    protected function getTokenFormatter($token)
    {
        $formatters = $this->getTokenFormatters();
        
        if (!array_key_exists($token, $formatters))
        {
            $message = "The class %s does not know how to format the %s token\n";
            $message .= "Have you added it in the getTokenFormatters() method?";

            throw new Exception(sprintf($message, get_called_class(), $token));
        }
        
        return $formatters[$token];
    }

    /**
     * Returns the values to replace command's schema tokens.
     *
     * @return  array
     */
    protected function getTokenReplaces()
    {
        $replaces = array();

        foreach ($this->tokens as $token => $value) {
            $token              = $this->getFormatter()->untokenize($token);
            $formatter          = $this->getTokenFormatter($token);
            $values             = array_filter($value);
            $token              = Formatter::tokenize($token);
            $replaces[$token]   = $formatter::format($values);
        }

        return $replaces;
    }

    /**
     * Build the command replacing schema tokens with actual values and cleaning
     * the command synthax.
     *
     * @return  string
     */
    protected function getValidStatement()
    {
        $statement = $this->replaceTokens($this->statement);
        $statement = preg_replace('/( ){2,}/', ' ', $statement);

        return trim($statement);
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
     * Sets a single value for a token, 
     *
     * @param   string  $token
     * @param   string  $tokenValue
     * @param   boolean $append
     * @param   boolean $first 
     * @return  true
     */
    public function setToken($token, $tokenValue, $append = false, $first = false)
    {
        return $this->setTokenValues($token, array($tokenValue), $append, $first);
    }

    /**
     * Sets the values of a token, and can be appended with the given $append.
     *
     * @param   string   $token
     * @param   array    $tokenValues
     * @param   boolean  $append
     * @param   boolean  $first
     * @param   boolean  $filter
     * @return  true
     */
    protected function setTokenValues($token, array $tokenValues, $append = true, $first = false)
    {
        $token = $this->tokenize($token);
        $this->checkToken($token);

        if (is_array($this->tokens[$token]) && is_array($tokenValues)) {
            if ($append) {
                $this->appendToken($token, $tokenValues, $first);
            } else {
                $this->unsetToken($token);
                $this->tokens[$token] = $tokenValues;
            }
        }

        return true;
    }

    /**
     * Deletes a token.
     *
     * @param string $token
     */
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
        return $this->getFormatter()->tokenize($token);
    }
}
