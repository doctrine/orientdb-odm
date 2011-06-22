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
 * Class Reader
 *
 * @package     
 * @subpackage  
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Contract\ODM\Mapper\Annotations;

interface Reader
{
    /**
     * Sets the default namespace that the AnnotationReader should assume for annotations
     * with not fully qualified names.
     * 
     * @param string $defaultNamespace
     */
    public function setDefaultAnnotationNamespace($defaultNamespace);

    /**
     * Sets the custom function to use for creating new annotations on the
     * underlying parser.
     *
     * The function is supplied two arguments. The first argument is the name
     * of the annotation and the second argument an array of values for this
     * annotation. The function is assumed to return an object or NULL.
     * Whenever the function returns NULL for an annotation, the implementation falls
     * back to the default annotation creation process of the underlying parser.
     *
     * @param Closure $func
     */
    public function setAnnotationCreationFunction(\Closure $func);

    /**
     * Sets an alias for an annotation namespace.
     * 
     * @param string $namespace
     * @param string $alias
     */
    public function setAnnotationNamespaceAlias($namespace, $alias);

    /**
     * Sets a flag whether to try to autoload annotation classes, as well as to distinguish
     * between what is an annotation and what not by triggering autoloading.
     *
     * NOTE: Autoloading of annotation classes is inefficient and requires silently failing
     *       autoloaders. In particular, setting this option to TRUE renders this AnnotationReader
     *       incompatible with a {@link ClassLoader}.
     * @param boolean $bool Boolean flag.
     */
    public function setAutoloadAnnotations($bool);
    /**
     * Gets a flag whether to try to autoload annotation classes.
     *
     * @see setAutoloadAnnotations
     * @return boolean
     */
    public function getAutoloadAnnotations();

    /**
     * Gets the annotations applied to a class.
     * 
     * @param ReflectionClass $class The ReflectionClass of the class from which
     * the class annotations should be read.
     * @return array An array of Annotations.
     */
    public function getClassAnnotations(\ReflectionClass $class);

    /**
     * Gets a class annotation.
     * 
     * @param ReflectionClass $class The ReflectionClass of the class from which
     * the class annotations should be read.
     * @param string $annotation The name of the annotation.
     * @return The Annotation or NULL, if the requested annotation does not exist.
     */
    public function getClassAnnotation(\ReflectionClass $class, $annotation);
    
    /**
     * Gets the annotations applied to a property.
     * 
     * @param string|ReflectionProperty $property The name or ReflectionProperty of the property
     * from which the annotations should be read.
     * @return array An array of Annotations.
     */
    public function getPropertyAnnotations(\ReflectionProperty $property);
    
    /**
     * Gets a property annotation.
     * 
     * @param ReflectionProperty $property
     * @param string $annotation The name of the annotation.
     * @return The Annotation or NULL, if the requested annotation does not exist.
     */
    public function getPropertyAnnotation(\ReflectionProperty $property, $annotation);
    
    /**
     * Gets the annotations applied to a method.
     * 
     * @param ReflectionMethod $property The name or ReflectionMethod of the method from which
     * the annotations should be read.
     * @return array An array of Annotations.
     */
    public function getMethodAnnotations(\ReflectionMethod $method);
    
    /**
     * Gets a method annotation.
     * 
     * @param ReflectionMethod $method
     * @param string $annotation The name of the annotation.
     * @return The Annotation or NULL, if the requested annotation does not exist.
     */
    public function getMethodAnnotation(\ReflectionMethod $method, $annotation);
}

