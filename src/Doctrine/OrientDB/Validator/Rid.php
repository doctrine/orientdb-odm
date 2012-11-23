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
 * Class Rid
 *
 * @package     Doctrine\OrientDB
 * @subpackage  Validator
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Validator;

use Doctrine\OrientDB\Validator;
use Doctrine\OrientDB\Exception\Validation as ValidationException;

class Rid extends Validator
{
    protected function clean($rid)
    {
        if (!preg_match('/^\s*#?(\d+:\d+)\s*$/', $rid, $matches)) {
            throw new ValidationException($rid, __CLASS__);
        }

        return "#{$matches[1]}";
    }
}
