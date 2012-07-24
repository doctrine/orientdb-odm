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
 * @package     Congow\Orient
 * @subpackage  Exception
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Exception\Query\SQL;

use Congow\Orient\Exception;
use Congow\Orient\Contract\Binding\BindingResultInterface;

class Invalid extends Exception
{
    protected $result;

    public function __construct($message, BindingResultInterface $result)
    {
        $this->result = $result;

        parent::__construct($message);
    }

    public function getBindingResult()
    {
        return $this->result;
    }
}
