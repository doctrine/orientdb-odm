<?php

/*
 * This file is part of the Doctrine\Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class Truncate
 *
 * @package    Doctrine\Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\Orient\Query\Command;

use Doctrine\Orient\Query\Command;

abstract class Truncate extends Command
{
    public function __construct($name)
    {
        parent::__construct();

        $this->setToken('Name', $name);
    }

    protected function getTokenFormatters()
    {
        return array_merge(parent::getTokenFormatters(), array(
            'Name'     => "Doctrine\Orient\Formatter\Query\Regular",
        ));
    }
}

