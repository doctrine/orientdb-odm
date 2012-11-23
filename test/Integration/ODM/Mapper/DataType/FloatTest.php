<?php

/**
 * FloatTest
 *
 * @package    Doctrine\OrientDB
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 * @version
 */

namespace test\Integration\ODM\Mapper\DataType;

use test\PHPUnit\TestCase;

class FloatTest extends TestCase
{
    public function testHydrationOfAFloatProperty()
    {
        $manager = $this->createManager();
        //MapPoint
        $point = $manager->find("#86:1");

        $this->assertInternalType('float', $point->y);
    }
}
