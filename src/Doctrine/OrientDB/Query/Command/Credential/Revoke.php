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
 * The Revoke class is used to build REVOKE sql statements.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Command\Credential;

use Doctrine\OrientDB\Query\Command\Credential;

class Revoke extends Credential
{
    /**
     * @inheritdoc
     */
    protected function getSchema()
    {
        return "REVOKE :Permission ON :Resource FROM :Role";
    }
}
