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
 * Class used to identify a document's annotations.
 *
 * @package    Doctrine\Orient
 * @subpackage ODM
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\Orient\ODM\Mapper\Annotations;

/**
 * @Annotation
 */
class Document extends \Doctrine\Common\Annotations\Annotation
{
    public $class;

    /**
     * Given a $Doctrine\OrientClass, checks wheter this annotation matches it.
     *
     * @param  string   $Doctrine\OrientClass
     * @return boolean
     */
    public function hasMatchingClass($orientClass)
    {
        $classes = explode(',', $this->class);

        foreach ($classes as $class) {
            if ($class === $orientClass) {
                return true;
            }
        }

        return false;
    }
}
