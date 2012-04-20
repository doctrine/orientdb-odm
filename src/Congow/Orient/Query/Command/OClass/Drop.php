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
 * This class lets you build SQL statements to drop a class from Congow\OrientDB.
 *
 * @package    Congow\Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Query\Command\OClass;

use Congow\Orient\Contract\Query\Command\OClass as OClassInterface;
use Congow\Orient\Query\Command\OClass;

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
