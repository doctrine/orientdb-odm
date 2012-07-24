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
 * Class Profile
 *
 * @package
 * @subpackage
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author      David Funaro <ing.davidino@gmail.com>
 */

namespace test\Integration\Document;

use Congow\Orient\ODM\Mapper\Annotations as ODM;

/**
* @ODM\Document(class="Profile")
*/
class Profile
{
    /**
     * @ODM\Property(type="long")
     */
    public $hash;
}
