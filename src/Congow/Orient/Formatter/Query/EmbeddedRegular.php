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
 * Class EmbeddedRegular
 *
 * @package     Congow\Orient
 * @subpackage  Formatter
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Formatter\Query;

use Congow\Orient\Formatter\Query;
use Congow\Orient\Contract\Formatter\Query\Token as TokenFormatter;

class EmbeddedRegular extends Query\Regular implements TokenFormatter
{
    public static function format(array $values)
    {
        return '"' . parent::format($values) . '"';
    }
}
