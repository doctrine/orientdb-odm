<?php

/**
 * DoubleTest
 *
 * @package    Doctrine\ODM\OrientDB
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 * @version
 */

namespace test\Doctrine\ODM\OrientDB\Integration\Mapper\DataType;

use test\PHPUnit\TestCase;

/**
 * @group integration
 */
class DoubleTest extends TestCase
{

    public function testHydrationOfADoubleProperty()
    {
        $manager = $this->createManager();
        //MapPoint
        $point = $manager->find("#".$this->getClassId('MapPoint').":0");

        $this->assertInternalType('float', $point->y);
    }
}
