<?php

/**
 * IntegerTest
 *
 * @package    Congow\Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 * @version
 */

namespace test\Integration\ODM\Mapper\DataType;

use test\PHPUnit\TestCase;

class IntegerTest extends TestCase
{
    public function testHydrationOfAnIntegerProperty()
    {
        $manager = $this->createManager(array(
            'mismatches_tolerance' => true,
        ));

        $post = $manager->find("#30:0");
        $this->assertInternalType('integer', $post->id);
    }

    /**
     * @expectedException Congow\Orient\Exception\Casting\Mismatch
     */
    public function testAnExceptionIsRaisedWhenAnIntegerPropertyIsNotAnInteger()
    {
        $manager = $this->createManager();

        $post = $manager->find("#30:0");
    }

    public function testMismatchedAttributesAreConvertedIfTheMapperToleratesMismatches()
    {
        $manager = $this->createManager(array(
            'mismatches_tolerance' => true,
        ));

        $post = $manager->find("#30:0");

        $this->assertInternalType('integer', $post->title);
    }
}
