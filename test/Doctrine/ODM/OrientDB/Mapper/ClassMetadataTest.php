<?php

/**
 * ClassMetadataTest
 *
 * @package    Doctrine\ODM\OrientDB
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 * @version
 */

namespace test\Doctrine\ODM\OrientDB\Mapper;

use test\PHPUnit\TestCase;
use Doctrine\ODM\OrientDB\Mapper;
use Doctrine\ODM\OrientDB\Mapper\ClassMetadata;
use Doctrine\ODM\OrientDB\Mapper\Annotations as ODM;

/**
* @ODM\Document(class="Mapped")
*/
class Mapped
{
    /**
     * @ODM\Property(name="field",type="string")
     */
    protected $field;

    /**
     * @ODM\Property(name="assoc",type="link")
     */
    protected $assoc;

    /**
     * @ODM\Property(name="multiassoc",type="linkset")
     */
    protected $multiassoc;
}

class ClassMetadataTest extends TestCase
{
    public function setup()
    {
        $mapper = $this->getMock('Doctrine\ODM\OrientDB\Mapper', null, array(null));

        $this->metadata = new ClassMetadata('test\Doctrine\ODM\OrientDB\Mapper\Mapped', $mapper);
    }

    function testGetName()
    {
        $this->assertEquals('test\Doctrine\ODM\OrientDB\Mapper\Mapped', $this->metadata->getName());
    }

    function testGetIdentifier()
    {
        $this->assertEquals(array('@rid'), $this->metadata->getIdentifier());
    }

    function testGetReflectionClass()
    {
        $this->assertInstanceOf('\ReflectionClass', $this->metadata->getReflectionClass());
    }

    function testIsIdentifier()
    {
        $this->assertEquals(true, $this->metadata->isIdentifier('@rid'));
        $this->assertEquals(false, $this->metadata->isIdentifier('id'));
    }

    function testHasField()
    {
        $this->assertEquals(true, $this->metadata->hasField('field'));
        $this->assertEquals(false, $this->metadata->hasField('OMNOMNOMNOMN'));
    }

    function testHasAssociation()
    {
        $this->assertEquals(true, $this->metadata->hasAssociation('assoc'));
        $this->assertEquals(false, $this->metadata->hasAssociation('OMNOMNOMNOMN'));
    }

    function testIsSingleValuedAssociation()
    {
        $this->assertEquals(true, $this->metadata->isSingleValuedAssociation('assoc'));
        $this->assertEquals(false, $this->metadata->isSingleValuedAssociation('multiassoc'));
    }

    function testIsCollectionValuedAssociation()
    {
        $this->assertEquals(false, $this->metadata->isCollectionValuedAssociation('assoc'));
        $this->assertEquals(true, $this->metadata->isCollectionValuedAssociation('multiassoc'));
    }

    function testGetFieldNames()
    {
        $this->assertEquals(array('field'), $this->metadata->getFieldNames());
    }

    function testgetAssociationNames()
    {
        $this->assertEquals(array('assoc', 'multiassoc'), $this->metadata->getAssociationNames());
    }

    function testgetTypeOfField()
    {
        $this->assertEquals('string', $this->metadata->getTypeOfField('field'));
    }

    function testgetAssociationTargetClass()
    {
        $this->assertEquals(null, $this->metadata->getAssociationTargetClass('OMNOMNOMNOMN'));
    }
}
