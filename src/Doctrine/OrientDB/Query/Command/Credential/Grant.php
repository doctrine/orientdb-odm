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
 * The Grant class it's used to build GRANT SQL statements.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Command\Credential;

use Doctrine\OrientDB\Contract\Query\Command\Credential as CredentialInterface;
use Doctrine\OrientDB\Query\Command\Credential;

class Grant extends Credential implements CredentialInterface
{
    /**
     * @inheritdoc
     */
    protected function getSchema()
    {
        return "GRANT :Permission ON :Resource TO :Role";
    }
}
