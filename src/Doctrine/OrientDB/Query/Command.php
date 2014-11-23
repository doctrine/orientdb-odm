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
 * Command class is a base class shared among all the command executable with
 * Doctrine\OrientDB's SQL synthax.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query;

use Doctrine\OrientDB\Exception;
use Doctrine\OrientDB\LogicException;
use Doctrine\OrientDB\Query\Formatter\QueryInterface as QueryFormatterInterface;
use Doctrine\OrientDB\Query\Formatter\Query as Formatter;
use Doctrine\OrientDB\Query\Validator\Rid as RidValidator;
use Doctrine\OrientDB\Query\Validator\Escaper as EscapeValidator;

abstract class Command implements CommandInterface
{
    protected $ridValidator;
    protected $escapeValidator;
    protected $formatter;
    protected $formatters = array();
    protected $tokens = array();

    /**
     * These are the valid return types for commands
     */
    const RETURN_COUNT  = 'COUNT';
    const RETURN_BEFORE = 'BEFORE';
    const RETURN_AFTER  = 'AFTER';

    /**
     * Builds a new object, creating the SQL statement from the class SCHEMA
     * and initializing the tokens.
     */
    public function __construct()
    {
        $this->tokens = $this->getTokens();
        $this->ridValidator = new RidValidator();
        $this->escapeValidator = new EscapeValidator();
    }

    /**
     * Returns the schema template for the command.
     */
    protected function getSchema()
    {
        return null;
    }

    /**
     * Sets a where token using the AND operator.
     * If the $condition contains a "?", it will be replaced by the $value.
     *
     * @param  string $condition
     * @param  string $value
     * @return Command
     */
    public function andWhere($condition, $value = null)
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
    public function getTokens()
    {
        preg_match_all("/(\:\w+)/", $this->getSchema(), $matches);
        $tokens = array();

        foreach ($matches[0] as $match) {
            $tokens[$match] = array();
        }

        return $tokens;
    }

    /**
     * Returns the value of a token.
     *
     * @param  string $token
     * @return mixed
     */
    public function getTokenValue($token)
    {
        return $this->checkToken($this->tokenize($token));
    }

    /**
     * Sets a where token using the OR operator.
     * If the $condition contains a "?", it will be replaced by the $value.
     *
     * @param  string $condition
     * @param  string $value
     * @return Command
     */
    public function orWhere($condition, $value = null)
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
     * @param QueryFormatterInterface $formatter
     */
    public function setFormatter(QueryFormatterInterface $formatter)
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
     */
    public function where($condition, $value = null, $append = false, $clause = "WHERE")
    {
        if (is_array($value)) {
            $condition = $this->formatWhereConditionWithMultipleTokens($condition, $value, $this->escapeValidator);
        } else {
            if ($value === null) {
                $condition = preg_replace("/=\s*\?/", "IS ?", $condition, 1);
                $value = 'NULL';
            } else if (is_bool($value)) {
                $value = $value ? 'TRUE' : 'FALSE';
            } else if (is_int($value) || is_float($value)) {
                // Preserve $value as is
            } else {
                $rid = $this->ridValidator->check($value, true);
                $value = $rid ? $rid : '"' . $this->escapeValidator->check($value, true) . '"';
            }

            $condition = str_replace("?", $value, $condition);
        }

        if (!$this->getTokenValue('Where')) {
            $clause = 'WHERE';
        }

        $this->setTokenValues('Where', array("{$clause} $condition"), $append, false, false);

        return $this;
    }

    /**
     * Returns whether this query, when executed, should have the collection hydrated.
     * The default is true
     *
     * @return boolean
     */
    public function canHydrate()
    {
        return true;
    }

    /**
     * Sets the Returns token
     *
     * @param string $return
     */
    public function returns($returns)
    {
        //check if the Return clause is even supported
        $returnTypes = $this->getValidReturnTypes();
        if (count($returnTypes) <= 0) {
            throw new LogicException("Return clause not supported for this statement");
        }

        $returns = strtoupper($returns);
        if (!in_array($returns, $returnTypes)) {
            throw new LogicException(sprintf("Unknown return type %s", $returns));
        }
        $this->setToken('Returns', $returns);
    }

    /**
     * Returns the array of valid params for the Return clause.
     * Use this function to support the Return clause by overriding and returing a list in the subclass
     *
     * @return array()
     */
    public function getValidReturnTypes()
    {
        return array();
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
    }

    /**
     * Appends $value to the query $token, using $key to identify the $value in
     * the token array.
     * With this method you set a token value and can retrieve it by its key.
     *
     * @param string $token
     * @param string $key
     * @param mixed  $value
     */
    protected function appendTokenAsString($token, $key, $value)
    {
        $this->tokens[$token][$key] = $value;
    }

    /**
     * Appends $value to the query $token.
     *
     * @param string $token
     * @param string $key
     * @param mixed  $value
     */
    protected function appendTokenAsInteger($token, $key, $value)
    {
        $this->tokens[$token][] = $value;
    }

    /**
     * Checks if a token is set, returning it if it is.
     *
     * @param  string $token
     * @return mixed
     * @throws TokenNotFoundException
     */
    protected function checkToken($token)
    {
        if (!array_key_exists($token, $this->tokens)) {
            throw new TokenNotFoundException($token, get_called_class());
        }

        return $this->tokens[$token];
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
     * @return QueryFormatterInterface
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
            'Target'   => "Doctrine\OrientDB\Query\Formatter\Query\Target",
            'Where'    => "Doctrine\OrientDB\Query\Formatter\Query\Where",
            'Class'    => "Doctrine\OrientDB\Query\Formatter\Query\Regular",
            'Property' => "Doctrine\OrientDB\Query\Formatter\Query\Regular",
            'Type'     => "Doctrine\OrientDB\Query\Formatter\Query\Regular",
            'Rid'      => "Doctrine\OrientDB\Query\Formatter\Query\Rid"
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

        if (!array_key_exists($token, $formatters)) {
            $message = "The class %s does not know how to format the %s token\n".
                       "Have you added it in the getTokenFormatters() method?";

            throw new Exception(sprintf($message, get_called_class(), $token));
        }

        return $formatters[$token];
    }

    /**
     * Returns the values to replace command's schema tokens.
     *
     * @return array
     */
    protected function getTokenReplaces()
    {
        $replaces = array();

        foreach ($this->tokens as $token => $value) {
            $key              = $this->getFormatter()->untokenize($token);
            $formatter        = $this->getTokenFormatter($key);
            $replaces[$token] = $formatter::format($value);
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
        $schema = $this->getSchema();
        $statement = $this->replaceTokens($schema);
        $statement = preg_replace('/( ){2,}/', ' ', $statement);

        return trim($statement);
    }

    /**
     * Substitutes multiple tokens ($values) in the WHERE $condition.
     *
     * @param  string $condition
     * @param  array $values
     * @return string
     * @throws LogicException
     */
    protected function formatWhereConditionWithMultipleTokens(
        $condition,
        Array $values,
        EscapeValidator $validator
    ) {
        if (count($values) !== substr_count($condition, '?')) {
            throw new LogicException("Number of given parameters does not match number of tokens");
        }

        foreach ($values as $replacement) {
            $condition = preg_replace("/\?/", '"' . $validator->check($replacement, 1) . '"', $condition, 1);
        }

        return $condition;
    }

    /**
     * Replaces the tokens in the command's schema with their actual values in
     * the current object.
     *
     * @param  string $statement
     * @return string
     */
    protected function replaceTokens($statement)
    {
        $replaces = $this->getTokenReplaces();

        return str_replace(array_keys($replaces), $replaces, $statement);
    }

    /**
     * Sets a single value for a token,
     *
     * @param  string  $token
     * @param  string  $tokenValue
     * @param  boolean $append
     * @param  boolean $first
     * @return true
     */
    public function setToken($token, $tokenValue, $append = false, $first = false)
    {
        return $this->setTokenValues($token, array($tokenValue), $append, $first);
    }

    /**
     * Sets the values of a token, and can be appended with the given $append.
     *
     * @param  string  $token
     * @param  array   $tokenValues
     * @param  boolean $append
     * @param  boolean $first
     * @param  boolean $filter
     * @return true
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
     * @param  string $token
     * @return string
     */
    protected function tokenize($token)
    {
        return $this->getFormatter()->tokenize($token);
    }
}
