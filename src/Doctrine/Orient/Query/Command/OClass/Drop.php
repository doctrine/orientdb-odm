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
 * This class lets you build SQL statements to drop a class from Doctrine\OrientDB.
 *
 * @package    Doctrine\Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\Orient\Query\Command\OClass;

use Doctrine\Orient\Contract\Query\Command\OClass as OClassInterface;
use Doctrine\Orient\Query\Command\OClass;

class Drop extends OClass implements OClassInterface
{
    /**
     * @inheritdoc
     */
    protected function getSchema()
    {
        return "DROP CLASS :Class";
    }
}
