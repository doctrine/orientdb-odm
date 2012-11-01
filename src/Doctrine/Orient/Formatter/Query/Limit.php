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
 * Class Limit
 *
 * @package     Doctrine\Orient
 * @subpackage  Formatter
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\Orient\Formatter\Query;

use Doctrine\Orient\Formatter\Query;
use Doctrine\Orient\Formatter\String;
use Doctrine\Orient\Contract\Formatter\Query\Token as TokenFormatter;

class Limit extends Query implements TokenFormatter
{
    public static function format(array $values)
    {
        foreach ($values as $limit) {
            if (is_numeric($limit)) {
                return "LIMIT $limit";
            }
        }

        return null;
    }
}
