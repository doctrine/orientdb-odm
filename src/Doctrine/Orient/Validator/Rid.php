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
 * Class Rid
 *
 * @package     Doctrine\Orient
 * @subpackage  Validator
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\Orient\Validator;

use Doctrine\Orient\Validator;
use Doctrine\Orient\Exception\Validation as ValidationException;

class Rid extends Validator
{
    protected function clean($rid)
    {
        if (is_string($rid) && strlen($rid)) {
            if ($rid[0] === "#") {
                $rid = substr($rid, 1);
            }

            $parts  = explode(':', $rid);

            if (count($parts) === 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
                return '#' . $rid;
            }
        }

        throw new ValidationException($rid, __CLASS__);
    }
}
