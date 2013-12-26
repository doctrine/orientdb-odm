<?php

/**
 * IntegerTest
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
class IntegerTest extends TestCase
{
    public $postId;

    public function setup()
    {
        $this->postId = $this->getClassId('Post');
    }

    public function testHydrationOfAnIntegerProperty()
    {
        $manager = $this->createManager(array(
            'mismatches_tolerance' => true,
        ));

        $post = $manager->find("#".$this->postId.":0");
        $this->assertInternalType('integer', $post->id);
    }

    /**
     * @expectedException Doctrine\ODM\OrientDB\Caster\CastingMismatchException
     */
    public function testAnExceptionIsRaisedWhenAnIntegerPropertyIsNotAnInteger()
    {
        $manager = $this->createManager();

        //attention 6-th record can change
        $post = $manager->find("#".$this->postId.":6");
    }

    public function testMismatchedAttributesAreConvertedIfTheMapperToleratesMismatches()
    {
        $manager = $this->createManager(array(
            'mismatches_tolerance' => true,
        ));

        $post = $manager->find("#".$this->postId.":0");

        $this->assertInternalType('integer', $post->title);
    }
}
