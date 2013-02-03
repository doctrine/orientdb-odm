<?php

namespace test\Doctrine\ODM\OrientDB\Document\Stub;

use Doctrine\ODM\OrientDB\Mapper\Annotations as ODM;

/**
* @ODM\Document(class="OCity", repositoryClass="test\Doctrine\ODM\OrientDB\Document\Stub\CityRepository")
*/
class City
{
    private $name;
}
