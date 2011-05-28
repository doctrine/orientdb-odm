<?php

/*
 * This file is part of the Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * (c) David Funaro <ing.davidino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    Orient
 * @subpackage Manager
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 */

namespace Orient\Document;

use Orient\Exception\Document as Exception;


/**
* @Orient\Document\Foo(class="Address")
*/
class Address
{
    public $pippo;
    public $pluto;
    public $paperino;
    public $minini;
}

class Foo extends \Doctrine\Common\Annotations\Annotation
{
    
    public $class;
    
}

class Manager 
{
    public function hydrate($json)
    {
        $jsonDecode = json_decode($json);
        $jsonClass = $jsonDecode->{'@class'};
        
        if ($jsonClass) {
            $class = $this->getORecordClass($jsonClass);
            if($class)
                return new $class();
        }
        
        throw new Exception\NotFound($jsonDecode);

    }
    
    /**
     * 
     */
    public function getORecordClass($OClass)
    {
        $dirs = array('./Test/Document/Stub' => 'Orient\\');
        
        foreach ($dirs as $dir => $namespace){
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
            $regex = new \RegexIterator($it, '/^.*\.php$/i');
            
            foreach ($regex as $file) {
                
                $class = str_replace(array('.php','/', '.\\'),array('','\\',$namespace), $file);
                
                if (class_exists($class)){
                    $reader = new \Doctrine\Common\Annotations\AnnotationReader();
                    $reflClass = new \ReflectionClass('Orient\Document\Address');
                    
                    $classAnnotations = $reader->getClassAnnotations($reflClass);
                    
                    if(isset($classAnnotations['Orient\Document\Foo'])){
                        $annotationClass = $classAnnotations['Orient\Document\Foo']->class;                        

                        if( $annotationClass == $OClass){
                            return $class;
                        }
                    }
                }
                
            }                
        }
        
        return null;
        
    }
}