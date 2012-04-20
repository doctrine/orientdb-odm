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
 * This class manages the creation of SQL statements to update map properties
 * of a record.
 *
 * @package    Congow\Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Query\Command\Update;

use Congow\Orient\Query\Command\Update;

class Put extends Update
{
    /**
     * Creates a new statement assigning the $values to update in the given
     * $class.
     * The values can be appended through $append.
     *
     * @param array   $values
     * @param string  $class
     * @param boolean $append
     */
    public function __construct(array $values, $class, $append = true)
    {
        parent::__construct($class);

        $this->setTokenValues('Updates', $values, $append);
    }

    /**
     * @inheritdoc
     */
    protected function getSchema()
    {
        return "UPDATE :Class PUT :Updates :Where";
    }

    /**
     * Returns the formatters for this query tokens
     *
     * @return array
     */
    protected function getTokenFormatters()
    {
        return array_merge(parent::getTokenFormatters(), array(
            'Updates'  => "Congow\Orient\Formatter\Query\MapUpdates",
        ));
    }
}
