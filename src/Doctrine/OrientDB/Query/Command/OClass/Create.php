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
 * This class lets you build SQL statements to create a class in Doctrine\OrientDB.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Command\OClass;

use Doctrine\OrientDB\Query\Command\OClass;
use Doctrine\OrientDB\Query\Command\OClassInterface;

class Create extends OClass implements OClassInterface
{
    /**
     * @inheritdoc
     */
    protected function getSchema()
    {
        return "CREATE CLASS :Class";
    }
}
