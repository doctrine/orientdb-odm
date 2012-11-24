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
 * Class Escaper
 *
 * @package     Doctrine\OrientDB
 * @subpackage  Validator
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Validator;

use Doctrine\OrientDB\Validator;
use Doctrine\OrientDB\ValidationException;

class Escaper extends Validator
{
    protected function clean($value)
    {
        return addslashes($value);
    }
}
