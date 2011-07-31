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
 * Class Validator
 *
 * @package     Orient
 * @subpackage  Validator
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient;

use Orient\Contract\Validator as ValidatorInterface;
use Orient\Exception\Validation as ValidationException;

abstract class Validator implements ValidatorInterface
{
    /**
     * Cleans and returns the $value.
     * If it is invalid, the validator fails.
     *
     * @param   mixed $value
     * @param   boolean $silent
     * @return  mixed
     */
    public function clean($value, $silent = false)
    {
        if ($value = $this->doClean($value)) {
            return $value;
        }
        
        return $this->fail($value, $silent);
    }
    
    /**
     * Internallly cleans and return the $value.
     * 
     * @param   mixed $value
     */
    abstract protected function doClean($value);
    
    /**
     * Makes the validator fail: if silent, null is returned, otherwise an
     * exception is raised.
     *
     * @param   mixed $value
     * @param   boolean $silent
     * @return  null
     * @throws  Orient\Exception\Validation
     */
    protected function fail($value, $silent = false)
    {
        if ($silent) {
            return null;
        }
        
        throw new ValidationException($value);
    }
}

