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
 * Class Reader
 *
 * @package    Doctrine\ODM
 * @subpackage OrientDB
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\ODM\OrientDB\Mapper\Annotations;

interface ReaderInterface
{
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
     * @return The Annotation or null, if the requested annotation does not exist.
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
     * @return The Annotation or null, if the requested annotation does not exist.
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
     * @return The Annotation or null, if the requested annotation does not exist.
     */
    public function getMethodAnnotation(\ReflectionMethod $method, $annotation);
}
