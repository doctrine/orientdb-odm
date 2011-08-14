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
 * Class Validator
 *
 * @package     Congow\Orient
 * @subpackage  Validator
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient;

use Congow\Orient\Contract\Validator as ValidatorInterface;
use Congow\Orient\Exception\Validation as ValidationException;

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
    public function check($value, $silent = false)
    {
        try {
            return $this->clean($value);
        }
        catch (ValidationException $e) { 
            return $this->fail($e, $value, $silent);
        }
    }
    
    /**
     * Internallly cleans and return the $value.
     * 
     * @param   mixed $value
     */
    abstract protected function clean($value);
    
    /**
     * Makes the validator fail: if silent, null is returned, otherwise an
     * exception is raised.
     *
     * @param   mixed $value
     * @param   boolean $silent
     * @return  null
     * @throws  Congow\Orient\Exception\Validation
     */
    protected function fail(ValidationException $e, $value, $silent = false)
    {
        if ($silent) {
            return null;
        }
        
        throw $e;
    }
}

