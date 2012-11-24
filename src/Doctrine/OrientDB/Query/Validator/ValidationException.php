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
 * Class ValidationException
 *
 * @package     Doctrine\OrientDB
 * @subpackage  Query
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Validator;

use Doctrine\OrientDB\Exception;

class ValidationException extends Exception
{
    public function __construct($value, $class)
    {
        if (is_array($value)) {
            $value = implode(', ', $value);
        }

        $this->message = sprintf('Validation of "%s" as %s failed', $value, $class);
    }
}
