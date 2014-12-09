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
 * Class Post
 *
 * @package
 * @subpackage
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author      David Funaro <ing.davidino@gmail.com>
 */

namespace test\Integration\Document;

use Doctrine\ODM\OrientDB\Mapper\Annotations as ODM;

/**
* @ODM\Document(class="Post")
*/
class Post
{
    /**
     * @ODM\Property(name="@rid", type="string")
     */
    public $rid;

    /**
     * @ODM\Property(type="link_list")
     */
    public $comments;

    /**
     * @ODM\Property(name="id", type="integer")
     */
    public $id;

    /**
     * @ODM\Property(type="integer")
     */
    public $title;

    public function getRid()
    {
        return $this->rid;
    }

    public function setRid($rid)
    {
        $this->rid = $rid;
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function setComments($city)
    {
        $this->comments = $city;
    }
}
