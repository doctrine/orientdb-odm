<?php

/*
 * This file is part of the Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class Invalid
 *
 * @package     Orient
 * @subpackage  Foundation
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Exception\Query\SQL;

use Congow\Orient\Exception;
use Congow\Orient\Http\Response;

class Invalid extends Exception
{
    public function __construct(Response $response)
    {
        $this->message = $response->getBody();
    }
}
