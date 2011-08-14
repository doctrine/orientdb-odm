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
 * Class Validation
 *
 * @package     Congow\Orient
 * @subpackage  Exception
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Exception;

use Congow\Orient\Exception;

class Validation extends Exception
{
    public function __construct($value, $class)
    {
        if (is_array($value)) {
            $value = implode(', ', $value);
        }
        
        $this->message = sprintf('Validation of "%s" as %s failed', $value, $class);
    }
}

