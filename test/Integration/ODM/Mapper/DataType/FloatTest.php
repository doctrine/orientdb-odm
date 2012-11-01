<?php

/**
 * FloatTest
 *
 * @package    Doctrine\Orient
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
        $point = $manager->find("#27:1");

        $this->assertInternalType('float', $point->y);
    }
}
