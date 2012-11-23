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
 * Class OClass
 *
 * @package    Doctrine\OrientDB
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Command\Truncate;

use Doctrine\OrientDB\Query\Command\Truncate;

class OClass extends Truncate
{
    /**
     * @inheritdoc
     */
    protected function getSchema()
    {
        return "TRUNCATE CLASS :Name";
    }
}

