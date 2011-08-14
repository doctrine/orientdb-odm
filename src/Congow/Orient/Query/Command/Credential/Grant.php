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
 * The Grant class it's used to build GRANT SQL statements.
 *
 * @package    Congow\Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Query\Command\Credential;

use Congow\Orient\Contract\Query\Command\Credential as CredentialInterface;
use Congow\Orient\Query\Command\Credential;

class Grant extends Credential implements CredentialInterface
{
    const SCHEMA =
        "GRANT :Permission ON :Resource TO :Role"
    ;
}
