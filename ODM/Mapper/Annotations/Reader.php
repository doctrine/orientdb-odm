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
 * Class used in order to decouple Orient from the Doctrine dependency.
 * If you want to use a custom annotation reader library you should make your
 * reader extend this class.
 *
 * @package     Orient
 * @subpackage  ODM
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\ODM\Mapper\Annotations;

use Orient\Contract\ODM\Mapper\Annotations\Reader as ReaderInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Closure;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Annotations\Parser;

class Reader extends AnnotationReader implements ReaderInterface
{
    
}
