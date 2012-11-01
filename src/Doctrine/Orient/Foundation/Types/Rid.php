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
 * Rid class encapsulates a rid.
 *
 * @package    Doctrine\Orient
 * @subpackage Foundation
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\Orient\Foundation\Types;

use Doctrine\Orient\Validator\Rid as RidValidator;
use Doctrine\Orient\Exception\Validation as ValidationException;

class Rid
{
    protected $rid;

    /**
     * Instantiates a new object, injecting the $rid;
     *
     * @param string $rid
     */
    public function __construct($rid)
    {
        $validator = new RidValidator();
        $validator->check($rid);
        $this->rid = $rid;
    }

    /**
     * Returns the rid associated with the current object.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->rid;
    }
}
