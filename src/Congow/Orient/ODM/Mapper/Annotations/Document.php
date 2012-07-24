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
 * Class used to identify a document's annotations.
 *
 * @package    Congow\Orient
 * @subpackage ODM
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\ODM\Mapper\Annotations;

/**
 * @Annotation
 */
class Document extends \Doctrine\Common\Annotations\Annotation
{
    public $class;

    /**
     * Given a $Congow\OrientClass, checks wheter this annotation matches it.
     *
     * @param  string   $Congow\OrientClass
     * @return boolean
     */
    public function hasMatchingClass($orientClass)
    {
        $classes = explode(',', $this->class);

        foreach ($classes as $class) {
            if ($class === $orientClass ) {
                return true;
            }
        }

        return false;
    }
}
