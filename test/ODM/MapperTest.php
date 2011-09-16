<?php

/**
 * QueryTest
 *
 * @package    Congow\Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 * @version
 */

namespace test;

use test\PHPUnit\TestCase;
use Congow\Orient\ODM\Manager;
use Congow\Orient\ODM\Mapper;
use Congow\Orient\ODM\Mapper\Annotations\Reader as AnnotationReader;

class Adapter implements \Congow\Orient\Contract\Protocol\Adapter
{
    public function __construct()
    {

    }
    
    public function getResult()
    {

    }
    
    public function execute($sql, $return)
    {
        
    }

    public function find($rid)
    {
        return '{
            "@type": "d", "@rid": "#19:0", "@version": 2, "@class": "Address", 
            "name": "Luca", 
            "surname": "Garulli", 
            "out": ["#20:1"]
          }';
    }
    
    public function findRecords(array $rids)
    {
        return array(json_decode($this->find("#20:1")),json_decode($this->find("#20:2")));
        
    }
}



class MapperTest extends TestCase
{
    const BINARY_64_ENCODED = "data:;base64,/9j/4AAQSkZJRgABAgAAZABkAAD/7AARRHVja3kAAQAEAAAAWAAA/+4ADkFkb2JlAGTAAAAAAf/bAEMAAQEBAQEBAQEBAQIBAQECAgIBAQICAwICAgICAwQDAwMDAwMEBAQEBQQEBAYGBgYGBggICAgICQkJCQkJCQkJCf/bAEMBAgICAwMDBQQEBQgGBQYICQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCf/CABEIADAAMAMBEQACEQEDEQH/xAAZAAEBAQEBAQAAAAAAAAAAAAAABwkIBAb/xAAUAQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIQAxAAAAHYQAAAnB9We4nBVQYsnfpVDFk37AAABxYQAFVO/QAAD//EAB0QAAMBAAIDAQAAAAAAAAAAAAUGBwQAFwgQIDD/2gAIAQEAAQUC+y7gor+kaTGmcWnTmxZhr0kGdvryNktBfHeJrZpRmL0N2mUiJxOnKNO/CjTFudDfQVJ50FSeJMhd1dn+/wD/xAAUEQEAAAAAAAAAAAAAAAAAAABQ/9oACAEDAQE/ARP/xAAUEQEAAAAAAAAAAAAAAAAAAABQ/9oACAECAQE/ARP/xAArEAABBAAFAgQHAQAAAAAAAAABAgMEBQAGERIUE0EHFSIxECAhMDiR1DL/2gAIAQEABj8C+dEO+zTXUkxxAdbiy5rMZxTZJAUEuKB01B+uGbKosGLWukbuPPjOpfZXsJSdq0Eg6EaYkTJkhESHEQp2VKdUENttoGqlKUfoAB7nDNbUZxqrWxkbuPAjWDD7y9gKjtQhZJ0A1+NXb5UoPNa6PVMxnpHKjMaPIffWU6POIPssYyzl7MMPy+4r+Zy4nUQ7t6sp1xPqbKknVKh3xnGorWeRY2tVYRoEfcEb3n2FoQnVRAGpPfGWcw5hyz5fT1/M5cvmRXdvViutp9LbqlHVSh2+zFtKHxWscjQ2IqGHKmIHum44la1F09OQ0NSFAe3bH5E3n6lf3Y/Im8/Ur+7FZe2/jPa5sroPW5FBJD/Rf6jSmxu3ynB6Srd/nt9j/8QAHhABAAEEAwEBAAAAAAAAAAAAAREAITFRECBBkTD/2gAIAQEAAT8h7uu2eWVZiIRI6raDENrRJQ2RKdNu18/g5UYCtoMQ2tElBYF59B5RNKsNkIvnNfQCO/8ApLLO61DIhaWSJQHrXwAD/wD6Syzr8Xbb+Xvptb2Dfw46dLIICdfT4SrjGTv/AP/aAAwDAQACAAMAAAAQAAAEAgAEgAAAAkAAAA//xAAUEQEAAAAAAAAAAAAAAAAAAABQ/9oACAEDAQE/EBP/xAAUEQEAAAAAAAAAAAAAAAAAAABQ/9oACAECAQE/EBP/xAAeEAEAAQQCAwAAAAAAAAAAAAABEQAQIWEwwSBBcf/aAAgBAQABPxDzFuqfDtNkkEMqNkhWCkv6SiESjvgt1rWNICqBWwQrBSX9JRALfchqwCRIrwEgOhPfKDCzBgIfQDRqEn5VIAtdCa/UCFmTAU4HVKT0rz5DIDIuVq3sQj0atSu5KOA//9k=";
    
    public function setup()
    {
        $annotationReader = new AnnotationReader;
        $this->mapper = new Mapper(new Adapter, $annotationReader);
        $this->mapper->setDocumentDirectories(array('./test/ODM/Document/Stub' => '\\'));
        
        $this->jsonRecord = json_decode('{
            "@type":          "d",
            "@rid":           "#12:0",
            "@version":        0,
            "is_true":         1,
            "is_false":        0,
            "@class":         "Address",
            "date":           "2011-01-01",
            "datetime":           "2011-01-01 21:00:00",
            "street":         "Piazza Navona, 1",
            "type":           "Residence",
            "city":           "#13:0",
            "sample":         "ok",
            "capital":        "122.231",
            "positive_short":  "32000",
            "negative_short":  "-32000",
            "invalid_short":  "-38000",
            "number":         "12", 
            "positive_long":  "32",
            "negative_long":  "-32",
            "invalid_long":   "3200000000000000000000",
            "positive_byte":  "32",
            "negative_byte":  "-32",
            "invalid_byte":   "128",
            "floating":       "10.5",
            "image":          "' . base64_encode(fread(fopen(__DIR__ . '/bin/image.jpg', "r"), filesize(__DIR__ . '/bin/image.jpg'))) . '",
              "embedded":      {
                                "@type": "d", "@version": 99, "@class": "OCity", 
                                "name": "Rome"
                               }
         }');        
        
        $this->jsonLinkedRecord = json_decode('{
            "@type":          "d",
            "@rid":           "#12:0",
            "@version":        0,
            "is_true":         1,
            "is_false":        0,
            "@class":         "Address",
            "link":           {
                              "@type": "d", "@rid": "#14:0", "@version": 99, "@class": "Address", 
                              "name": "Rome", 
                              "link":{
                                "@type": "d", "@rid": "#15:0", "@version": 99, "@class": "Address", 
                                "name": "Italy"
                              }},
            "linkset":        [
                                {"@type": "d", "@rid": "#20:102", "@version": 1, "@class": "Address"}, 
                                {"@type": "d", "@rid": "#20:103", "@version": 1, "@class": "Address"}
                              ],
            "linklist":       [
                                {"@type": "d", "@rid": "#20:102", "@version": 1, "@class": "Address"}, 
                                {"@type": "d", "@rid": "#20:103", "@version": 1, "@class": "Address"}
                              ],
              "linkmap":       {
                                  "first_key" : {"@type": "d", "@rid": "#20:102", "@version": 1, "@class": "Address"}, 
                                  "second_key": {"@type": "d", "@rid": "#20:103", "@version": 1, "@class": "Address"}
                                },
            "lazy_link":       "#1:1",
            "lazy_linklist":  [ "#20:102", "#20:103" ],
            "lazy_linkset":   [ "#20:102", "#20:103" ],
            "lazy_linkmap":   { "first_key" : "#20:102", "second_key": "#20:103" }
         }');
         
         $this->jsonEmbeddedMapRecord = json_decode('{
             "@type":          "d",
             "@rid":           "#12:0",
             "@version":        0,
             "is_true":         1,
             "is_false":        0,
             "@class":         "Address",
             "embedded_map":    {
                                   "first_key" : {"@type": "d", "@version": 1, "@class": "Address"},
                                   "second_key": {"@type": "d", "@version": 1, "@class": "Address"}
                                }
          }');                   
        
        $this->jsonEmbeddedRecord = json_decode('{
            "@type":    "d",
            "@rid":     "#12:0",
            "@version":  0,
            "@class":   "Address",
            "embeddedlist": [
                         {"@type": "d", "@version": 1, "@class": "Address"}, 
                         {"@type": "d", "@version": 1, "@class": "Address"}
                        ],
            "embeddedintegers": [10, 20],
            "embeddedstrings": ["hola", "halo"],
            "embeddedbooleans": ["0", "1"]
         }');
    
     $this->jsonEmbeddedSetRecord = json_decode('{
         "@type":    "d",
         "@rid":     "#12:0",
         "@version":  0,
         "@class":   "Address",
         "embeddedset": [
                      {"@type": "d", "@version": 1, "@class": "Address"}, 
                      {"@type": "d", "@version": 1, "@class": "Address"}
                     ],
         "embeddedsetintegers": [10, 20],
         "embeddedsetstrings": ["hola", "halo"],
         "embeddedsetbooleans": ["0", "1"]
      }');
        
        $this->jsonLongRecord = json_decode('{
            "@type":    "d",
            "@rid":     "#12:0",
            "@version":  0,
            "@class":   "Address",
            "invalid_long":     "3200000000000000000000"
         }');

        $this->jsonRecordWrongClass = json_decode('{
            "@type":    "d",
            "@rid":     "#12:0",
            "@version":  0,
            "@class":   "MNOMNOMONMONM",
            "street":   "Piazza Navona, 1",
            "type":     "Residence",
            "city":     "#13:0"
         }');

        $this->jsonRecordNoClass = json_decode('{
            "@type":    "d",
            "@rid":     "#12:0",
            "@version":  0,
            "street":   "Piazza Navona, 1",
            "type":     "Residence",
            "city":     "#13:0"
         }');
         
         $this->jsonCollection = array(
            json_decode('{
                "@type":    "d", 
                "@rid":     "#12:0", 
                "@version":  0, 
                "@class":   "Address",
                "street":   "Piazza Navona, 1",
                "type":     "Residence",
                "city":     "#13:0"
              }'),
            json_decode(  '{
                  "@type":    "d", 
                  "@rid":     "#13:0", 
                  "@version":  0, 
                  "@class":   "OCity",
                  "name":     "roma"
                }'),
            json_decode('{
                  "@type":    "d", 
                  "@rid":     "#12:0", 
                  "@version":  0, 
                  "@class":   "ForeignAddress",
                  "street":   "Piazza Navona, 1",
                  "type":     "Residence",
                  "city":     "#13:0"
              }'),
         );
         
    }
    
    public function testYouCanDecideWheterInjectACustomAnnotationReaderOrNotToTheMapper()
    {
        $annotationReader = new AnnotationReader;
        $this->mapper = new Mapper(new Adapter, $annotationReader);
        
        $this->assertInstanceOf('Congow\Orient\ODM\Mapper\Annotations\Reader', $this->mapper->getAnnotationReader());
        
        $this->mapper = new Mapper(new Adapter);
        
        $this->assertInstanceOf('Doctrine\Common\Annotations\AnnotationReader', $this->mapper->getAnnotationReader());
    }
    
    public function testAJsonGetsConvertedToAnObject()
    {   
        $object = $this->mapper->hydrate($this->jsonRecord);
        
        $this->assertInstanceOf('Test\ODM\Document\Stub\Contact\Address', $object);
    }

    /**
     * @expectedException Congow\Orient\Exception\Document\NotFound
     */
    public function testAnExceptionIsRaisedWhenAnObjectGetsPersistedWithoutAClass()
    {
        $object = $this->mapper->hydrate($this->jsonRecordNoClass);
    }

    /**
     * @expectedException Congow\Orient\Exception\ODM\OClass\NotFound
     */
    public function testAnExceptionIsRaisedWhenAnObjectGetsPersistedWithAWrongClass()
    {
        $object = $this->mapper->hydrate($this->jsonRecordWrongClass);
    }

    public function testStringPropertiesGetsMappedInTheObject()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals('Residence', $object->getType());
    }

    public function testDoublePropertiesGetsMappedInTheObject()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(122.231, $object->getCapital());
    }
    
    public function testIntegersGetsMappedInTheObject()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(12, $object->getNumber());
    }
    
    public function testFloatGetsMappedInTheObject()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);

        $this->assertInternalType('float', $object->getFloating());
    }

    public function testShortPropertiesGetsMappedInTheObject()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(-32000, $object->getNegativeShort());
        $this->assertEquals(32000, $object->getPositiveShort());
    }

    public function testShortPropertiesDontThrowAnExceptionIfOverflowsAreTolerated()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(null, $object->getInvalidShort());
    }

    /**
     * @expectedException Congow\Orient\Exception\Overflow
     */
    public function testShortPropertiesThrowAnExceptionIfOverflowsAreNotTolerated()
    {
        $this->mapper->enableOverflows();
        $object = $this->mapper->hydrate($this->jsonRecord);
    }

    public function testLongPropertiesGetsMappedInTheObject()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(-32, $object->getNegativeLong());
        $this->assertEquals(32, $object->getPositiveLong());
    }

    public function testLongPropertiesDontThrowAnExceptionIfOverflowsAreTolerated()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(null, $object->getInvalidLong());
    }

    /**
     * @expectedException Congow\Orient\Exception\Overflow
     */
    public function testLongPropertiesThrowAnExceptionIfOverflowsAreNotTolerated()
    {
        $this->mapper->enableOverflows();
        $object = $this->mapper->hydrate($this->jsonLongRecord);
    }

    public function testBytePropertiesGetsMappedInTheObject()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(-32, $object->getNegativeByte());
        $this->assertEquals(32, $object->getPositiveByte());
    }

    public function testBytePropertiesDontThrowAnExceptionIfOverflowsAreTolerated()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(null, $object->getInvalidByte());
    }

    /**
     * @expectedException Congow\Orient\Exception\Overflow
     */
    public function testBytePropertiesThrowAnExceptionIfOverflowsAreNotTolerated()
    {
        $this->mapper->enableOverflows();
        $object = $this->mapper->hydrate($this->jsonLongRecord);
    }
    
    public function testPropertiesCanHaveDifferentNamesInCongowOrientAndPopo()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals('ok', $object->getExampleProperty());
    }
    
    public function testBooleanPropertiesGetsMappedInTheObject()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(true, $object->getIsTrue());
        $this->assertEquals(false, $object->getIsFalse());
    }
    
    public function testBinaryPropertiesGetsMappedInTheObject()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(self::BINARY_64_ENCODED, $object->getImage());
    }
    
    public function testLinkedRecordsGetsMappedInTheObject()
    {
        $object = $this->mapper->hydrate($this->jsonLinkedRecord);

        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $object->getLink());
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $object->getLink()->getLink());
    }
    
    public function testLazyLinkedRecordsGetsMappedInTheObject()
    {
        $object = $this->mapper->hydrate($this->jsonLinkedRecord);
        
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $object->getLazyLink());
    }
    
    public function testLazyLinkListGetsMappedInTheObject()
    {
        $object = $this->mapper->hydrate($this->jsonLinkedRecord);
        $linklist = $object->getLazyLinkList();
        
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linklist[0]);
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linklist[1]);
    }

    public function testLazyLinkSetGetsMappedInTheObject()
    {
        $object = $this->mapper->hydrate($this->jsonLinkedRecord);
        $linkset = $object->lazy_linkset;
        
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linkset[0]);
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linkset[1]);
    }
    
    public function testLinkSetGetsMappedInTheObject()
    {
        $object = $this->mapper->hydrate($this->jsonLinkedRecord);
        $linkset = $object->getLinkset();
        
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linkset[0]);
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linkset[1]);
    }
    
    public function testLazyLinkMapGetsMappedInTheObject()
    {
        $object = $this->mapper->hydrate($this->jsonLinkedRecord);
        $linkmap = $object->lazy_linkmap;
        
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linkmap[0]);
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linkmap[1]);
    }
    
    public function testLinkListGetsMappedInTheObject()
    {
        $object = $this->mapper->hydrate($this->jsonLinkedRecord);
        $linklist = $object->getLinkList();
        
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linklist[0]);
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linklist[1]);
    }
    
    public function testLinkMapGetsMappedInTheObject()
    {
        $object = $this->mapper->hydrate($this->jsonLinkedRecord);
        $linkmap = $object->getLinkMap();
        
        $keys = array_keys($linkmap);
        
        $this->assertEquals('first_key', $keys[0]);
        $this->assertEquals('second_key', $keys[1]);
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linkmap['first_key']);
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linkmap['second_key']);
    }
     
    public function testEmbeddedMapGetsMappedInTheObject()
    {
        $object = $this->mapper->hydrate($this->jsonEmbeddedMapRecord);
        $linkmap = $object->getEmbeddedMap();
        
        $keys = array_keys($linkmap);
        
        $this->assertEquals('first_key', $keys[0]);
        $this->assertEquals('second_key', $keys[1]);
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linkmap['first_key']);
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linkmap['second_key']);
    }
    
    public function testDatePropertiesGetsMappedInTheObject()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);

        $this->assertInstanceOf('\DateTime', $object->getDate());
        $this->assertEquals('2011-01-01', $object->getDate()->format('Y-d-m'));
    }

    public function testDatetimePropertiesGetsMappedInTheObject()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);

        $this->assertInstanceOf('\DateTime', $object->getDateTime());
        $this->assertEquals('2011-01-01 21:00:00', $object->getDateTime()->format('Y-d-m H:i:s'));
    }

    public function testAnAnnotatedPropertyNotPassedWithTheJSONIsNullByDefault()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(null, $object->getAnnotatedButNotInJson());
    }

    public function testPropertiesGetsMappedInTheObjectOnlyIfAnnotated()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(null, $object->getStreet());
    }

    public function testGettingTheDirectoriesInWhichTheMapperLooksForPOPOs()
    {
        $annotationReader = new AnnotationReader;
        $this->mapper = new Mapper(new Adapter, $annotationReader);
        $dirs = array(
            'dir'   => 'namespace',
            'dir2'  => 'namespace2',
        );
        $object = $this->mapper->setDocumentDirectories($dirs);

        $this->assertEquals($dirs, $this->mapper->getDocumentDirectories());
    }
    
    public function testNoRecordsIsLostWhenHydratingACollection()
    {
        $collection = $this->mapper->hydrateCollection($this->jsonCollection);
        $this->assertEquals(3, count($collection)); 
    }
    
    public function testHidratedCollectionsContainPopo()
    {
        $collection = $this->mapper->hydrateCollection($this->jsonCollection);
        $this->assertInstanceOf('Test\ODM\Document\Stub\Contact\Address', $collection[0] );
        $this->assertInstanceOf('Test\ODM\Document\Stub\City', $collection[1] );
    }
    
    public function testCongowOrientObjectsOfDifferentClassesCanBeMappedByASinglePopo()
    {
        $collection = $this->mapper->hydrateCollection($this->jsonCollection);
        $this->assertInstanceOf('Test\ODM\Document\Stub\Contact\Address', $collection[2] );
    }
    
    public function testEmbeddedRecordsGetsMappedInTheObject()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);
        
        $this->assertInstanceOf("Test\ODM\Document\Stub\City", $object->getEmbedded());
    }
    
    public function testEmbeddedListedRecordsGetsMappedInTheObject()
    {
        $object     = $this->mapper->hydrate($this->jsonEmbeddedRecord);
        $embedded   = $object->getEmbeddedList();
        
        $this->assertEquals(2, count($embedded));
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $embedded[0]);
    }
    
    public function testEmbeddedListedDataGetsMappedInTheObject()
    {
        $object             = $this->mapper->hydrate($this->jsonEmbeddedRecord);
        $embeddedIntegers   = $object->getEmbeddedIntegers();
        $embeddedStrings    = $object->getEmbeddedStrings();
        $embeddedBooleans   = $object->getEmbeddedBooleans();
        
        $this->assertEquals(2, count($embeddedIntegers));
        $this->assertEquals(10, $embeddedIntegers[0]);        
        $this->assertEquals(2, count($embeddedStrings));
        $this->assertEquals('hola', $embeddedStrings[0]);
        $this->assertEquals(2, count($embeddedBooleans));
        $this->assertEquals(false, $embeddedBooleans[0]);
    }

    /* embedded set */
    public function testEmbeddedSetRecordsGetsMappedInTheObject()
    {
        $object     = $this->mapper->hydrate($this->jsonEmbeddedSetRecord);
        $embedded   = $object->getEmbeddedSet();
        
        $this->assertEquals(2, count($embedded));
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $embedded[0]);
    }
    
    public function testEmbeddedSetDataGetsMappedInTheObject()
    {
        $object             = $this->mapper->hydrate($this->jsonEmbeddedSetRecord);
        $embeddedIntegers   = $object->getEmbeddedSetIntegers();
        $embeddedStrings    = $object->getEmbeddedSetStrings();
        $embeddedBooleans   = $object->getEmbeddedSetBooleans();
        
        $this->assertEquals(2, count($embeddedIntegers));
        $this->assertEquals(10, $embeddedIntegers[0]);        
        $this->assertEquals(2, count($embeddedStrings));
        $this->assertEquals('hola', $embeddedStrings[0]);
        $this->assertEquals(2, count($embeddedBooleans));
        $this->assertEquals(false, $embeddedBooleans[0]);
    }
}
