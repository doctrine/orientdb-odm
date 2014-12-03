<?php

namespace test\Doctrine\ODM\OrientDB\Document\Stub\Contact;

use Doctrine\ODM\OrientDB\Mapper\Annotations as ODM;

/**
* @ODM\Document(class="EmptyAddress")
*/
class EmptyAddress
{
    /**
     * @ODM\Property(name="@rid", type="string")
     */
    public $rid;
    
    /**
     * @ODM\Property(type="string", notnull="false")
     */
    public $string;
    
    /**
     * @ODM\Property(type="integer", notnull="false")
     */
    public $integer;
}
