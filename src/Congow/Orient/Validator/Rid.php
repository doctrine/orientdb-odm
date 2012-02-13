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
 * Class Rid
 *
 * @package     
 * @subpackage  
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Validator;

use Congow\Orient\Validator;
use Congow\Orient\Exception\Validation as ValidationException;

class Rid extends Validator
{
    protected function clean($rid)
    {   
        if (is_string($rid)) {
          
            if (!strlen($rid)) {
                throw new ValidationException($rid, __CLASS__);
            }
            
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

