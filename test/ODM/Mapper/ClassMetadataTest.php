<?php

/**
 * ClassMetadataTest
 *
 * @package    Congow\Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 * @version
 */

namespace test\ODM\Mapper;

use test\PHPUnit\TestCase;
use Congow\Orient\ODM\Mapper;
use Congow\Orient\ODM\Mapper\ClassMetadata;
use Congow\Orient\ODM\Mapper\Annotations as ODM;

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

class TestMapper extends \Congow\Orient\ODM\Mapper
{
    public function __construct()
    {
        $this->annotationReader = new \Congow\Orient\ODM\Mapper\Annotations\Reader;
    }
}


class ClassMetadataTest extends TestCase
{
    public function setup()
    {
        $this->metadata = new ClassMetadata('test\ODM\Mapper\Mapped', new TestMapper);
    }
    
    function testGetName()
    {
        $this->assertEquals('test\ODM\Mapper\Mapped', $this->metadata->getName());
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