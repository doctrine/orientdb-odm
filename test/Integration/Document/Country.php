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
 * Class Address
 *
 * @package
 * @subpackage
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author      David Funaro <ing.davidino@gmail.com>
 */

namespace test\Integration\Document;

use Doctrine\ODM\OrientDB\Mapper\Annotations as ODM;

/**
* @ODM\Document(class="Country,City")
*/
class Country
{
    /**
     * @ODM\Property(name="@rid", type="string")
     */
    protected $rid;

    /**
     * @ODM\Property(name="name", type="string")
     */
    public $name;

    /**
     * @return mixed
     */
    public function getRid()
    {
        return $this->rid;
    }
}
