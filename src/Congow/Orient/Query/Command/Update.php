<?php

/*
 * This file is part of the Congow\Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This is a central point to manipulate SQL statements dealing with updates.
 *
 * @package    Congow\Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Query\Command;

use Congow\Orient\Query\Command;
use Congow\Orient\Contract\Query\Command\Update as UpdateInterface;

class Update extends Command implements UpdateInterface
{
    /**
     * Builds a new statement, setting the $class.
     *
     * @param string $class
     */
    public function __construct($class)
    {
        parent::__construct();

        $this->setToken('Class', $class);
    }

    /**
     * @inheritdoc
     */
    protected function getSchema()
    {
        return "UPDATE :Class SET :Updates :Where";
    }

    /**
     * Set the $values of the updates to be done.
     * You can $appnd the values.
     *
     * @param   array   $values
     * @param   boolean $append
     * @return  Update
     */
    public function set(array $values, $append = true)
    {
        $this->setTokenValues('Updates', $values, $append);

        return $this;
    }

    /**
     * Returns the formatters for this query's tokens.
     *
     * @return Array
     */
    protected function getTokenFormatters()
    {
        return array_merge(parent::getTokenFormatters(), array(
            'Updates'  => "Congow\Orient\Formatter\Query\Updates",
        ));
    }
}
