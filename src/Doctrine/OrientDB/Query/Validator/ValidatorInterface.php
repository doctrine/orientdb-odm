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
 * Interface Validator
 *
 * @package     Doctrine\OrientDB
 * @subpackage  Query
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Validator;

interface ValidatorInterface
{
    /**
     * Cleans ad returns the polished $value.
     *
     * @param   mixed $value
     * @return  mixed
     * @throws  Doctrine\OrientDB\Query\Validator\ValidationException
     */
    public function check($value);
}
