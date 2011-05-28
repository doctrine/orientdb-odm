<?php

/**
 * QueryTest
 *
 * @package    Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace Orient\Test;

use Orient\Test\PHPUnit\TestCase;
use Orient\ODM\Manager;
use Orient\ODM\Mapper;


class ManagerTest extends TestCase
{
    public function setup()
    {
        $this->manager = new Manager(new Mapper());
        $this->manager->setDocumentDirectories(array('./Test/ODM/Document/Stub' => 'Orient\\'));
        
        $this->jsonRecord = '{
            "@type":    "d",
            "@rid":     "#12:0",
            "@version":  0,
            "is_true":   1,
            "is_false":  0,
            "@class":   "Address",
            "date":     "2011-01-01",
            "datetime":     "2011-01-01 21:00:00",
            "street":   "Piazza Navona, 1",
            "type":     "Residence",
            "city":     "#13:0"
         }';

        $this->jsonRecordWrongClass = '{
            "@type":    "d",
            "@rid":     "#12:0",
            "@version":  0,
            "@class":   "MNOMNOMONMONM",
            "street":   "Piazza Navona, 1",
            "type":     "Residence",
            "city":     "#13:0"
         }';

        $this->jsonRecordNoClass = '{
            "@type":    "d",
            "@rid":     "#12:0",
            "@version":  0,
            "street":   "Piazza Navona, 1",
            "type":     "Residence",
            "city":     "#13:0"
         }';
         
         $this->jsonCollection = '{ 
             "schema": {
                 "id":   6,
                 "name": "Address"
               },
             "result": [{
                "@type":    "d", 
                "@rid":     "#12:0", 
                "@version":  0, 
                "@class":   "Address",
                "street":   "Piazza Navona, 1",
                "type":     "Residence",
                "city":     "#13:0"
              }]
          }';
         
    }
    
    public function testAJsonGetsConvertedToAnObject()
    {   
        $object = $this->manager->hydrate($this->jsonRecord);
        
        $this->assertInstanceOf('Orient\Test\ODM\Document\Stub\Contact\Address', $object);
    }

    /**
     * @expectedException Orient\Exception\Document\NotFound
     */
    public function testAnExceptionIsRaisedWhenAnObjectGetsPersistedWithoutAClass()
    {
        $object = $this->manager->hydrate($this->jsonRecordNoClass);
    }

    /**
     * @expectedException Orient\Exception\Document\NotFound
     */
    public function testAnExceptionIsRaisedWhenAnObjectGetsPersistedWithAWrongClass()
    {
        $object = $this->manager->hydrate($this->jsonRecordWrongClass);
    }

    public function testStringPropertiesGetsMappedInTheObject()
    {
        $object = $this->manager->hydrate($this->jsonRecord);

        $this->assertEquals('Residence', $object->getType());
    }

    public function testBooleanPropertiesGetsMappedInTheObject()
    {
        $object = $this->manager->hydrate($this->jsonRecord);

        $this->assertEquals(true, $object->getIsTrue());
        $this->assertEquals(false, $object->getIsFalse());
    }

    public function testDatePropertiesGetsMappedInTheObject()
    {
        $object = $this->manager->hydrate($this->jsonRecord);

        $this->assertInstanceOf('\DateTime', $object->getDate());
        $this->assertEquals('2011-01-01', $object->getDate()->format('Y-d-m'));
    }

    public function testDatetimePropertiesGetsMappedInTheObject()
    {
        $object = $this->manager->hydrate($this->jsonRecord);

        $this->assertInstanceOf('\DateTime', $object->getDateTime());
        $this->assertEquals('2011-01-01 21:00:00', $object->getDateTime()->format('Y-d-m H:i:s'));
    }

    public function testAnAnnotatedPropertyNotPassedWithTheJSONIsNullByDefault()
    {
        $object = $this->manager->hydrate($this->jsonRecord);

        $this->assertEquals(NULL, $object->getAnnotatedButNotInJson());
    }

    public function testPropertiesGetsMappedInTheObjectOnlyIfAnnotated()
    {
        $object = $this->manager->hydrate($this->jsonRecord);

        $this->assertEquals(NULL, $object->getStreet());
    }

    public function testGettingTheDirectoriesInWhichTheMapperLooksForPOPOs()
    {
        $this->manager = new Manager(new Mapper());
        $dirs = array(
            'dir'   => 'namespace',
            'dir2'  => 'namespace2',
        );
        $object = $this->manager->setDocumentDirectories($dirs);

        $this->assertEquals($dirs, $this->manager->getDocumentDirectories());
    }
}
