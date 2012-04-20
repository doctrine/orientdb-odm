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
 * Class OClass
 *
 * @package     Congow\Orient
 * @subpackage  Query
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Query\Command\Truncate;

use Congow\Orient\Query\Command\Truncate;

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

