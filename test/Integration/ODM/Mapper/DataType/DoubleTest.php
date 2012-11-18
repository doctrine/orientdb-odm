<?php

/**
 * DoubleTest
 *
 * @package    Congow\Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 * @version
 */

namespace test\Integration\ODM\Mapper\DataType;

use test\PHPUnit\TestCase;

class DoubleTest extends TestCase
{
    public function testHydrationOfADoubleProperty()
    {
        $manager = $this->createManager();
        //MapPoint
        $point = $manager->find("#86:0");

        $this->assertInternalType('float', $point->y);
    }
}
