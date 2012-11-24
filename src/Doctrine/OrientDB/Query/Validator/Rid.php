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
 * @subpackage  Query
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Validator;

class Rid extends AbstractValidator
{
    protected function clean($rid)
    {
        if (!preg_match('/^\s*#?(\d+:\d+)\s*$/', $rid, $matches)) {
            throw new ValidationException($rid, __CLASS__);
        }

        return "#{$matches[1]}";
    }
}
