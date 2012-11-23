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
 * Class used to manipulate and identity properties in an annotation.
 *
 * @package    Doctrine\OrientDB
 * @subpackage ODM
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\ODM\OrientDB\Mapper\Annotations;

/**
 * @Annotation
 */
class Property extends \Doctrine\Common\Annotations\Annotation
{
    public $name;
    public $type;
    public $cast;

    public function getCast()
    {
        return $this->cast;
    }
}
