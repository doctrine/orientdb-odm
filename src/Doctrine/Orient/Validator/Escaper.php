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
 * Class Escaper
 *
 * @package     Doctrine\Orient
 * @subpackage  Validator
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\Orient\Validator;

use Doctrine\Orient\Validator;
use Doctrine\Orient\Exception\Validation as ValidationException;

class Escaper extends Validator
{
    protected function clean($value)
    {
        return addslashes($value);
    }
}
