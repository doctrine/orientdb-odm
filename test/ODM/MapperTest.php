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
use Congow\Orient\Query;

class Adapter implements \Congow\Orient\Contract\Protocol\Adapter
{
    public function __construct()
    {

    }
    
    public function getResult()
    {
        return json_decode('{
            "@type": "d", "@rid": "#19:0", "@version": 2, "@class": "Address", 
            "name": "Luca", 
            "surname": "Garulli", 
            "out": ["#20:1"]
          }');
    }
    
    public function execute(Query $query)
    {
        return true;
    }
    
    public function findRecords(array $rids)
    {
        return array($this->getResult("#20:1"),$this->getResult("#20:2"));
        
    }
}



class MapperTest extends TestCase
{
    const BINARY_64_ENCODED = "data:;base64,/9j/4AAQSkZJRgABAgAAZABkAAD/7AARRHVja3kAAQAEAAAAWAAA/+4ADkFkb2JlAGTAAAAAAf/bAEMAAQEBAQEBAQEBAQIBAQECAgIBAQICAwICAgICAwQDAwMDAwMEBAQEBQQEBAYGBgYGBggICAgICQkJCQkJCQkJCf/bAEMBAgICAwMDBQQEBQgGBQYICQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCf/CABEIADAAMAMBEQACEQEDEQH/xAAZAAEBAQEBAQAAAAAAAAAAAAAABwkIBAb/xAAUAQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIQAxAAAAHYQAAAnB9We4nBVQYsnfpVDFk37AAABxYQAFVO/QAAD//EAB0QAAMBAAIDAQAAAAAAAAAAAAUGBwQAFwgQIDD/2gAIAQEAAQUC+y7gor+kaTGmcWnTmxZhr0kGdvryNktBfHeJrZpRmL0N2mUiJxOnKNO/CjTFudDfQVJ50FSeJMhd1dn+/wD/xAAUEQEAAAAAAAAAAAAAAAAAAABQ/9oACAEDAQE/ARP/xAAUEQEAAAAAAAAAAAAAAAAAAABQ/9oACAECAQE/ARP/xAArEAABBAAFAgQHAQAAAAAAAAABAgMEBQAGERIUE0EHFSIxECAhMDiR1DL/2gAIAQEABj8C+dEO+zTXUkxxAdbiy5rMZxTZJAUEuKB01B+uGbKosGLWukbuPPjOpfZXsJSdq0Eg6EaYkTJkhESHEQp2VKdUENttoGqlKUfoAB7nDNbUZxqrWxkbuPAjWDD7y9gKjtQhZJ0A1+NXb5UoPNa6PVMxnpHKjMaPIffWU6POIPssYyzl7MMPy+4r+Zy4nUQ7t6sp1xPqbKknVKh3xnGorWeRY2tVYRoEfcEb3n2FoQnVRAGpPfGWcw5hyz5fT1/M5cvmRXdvViutp9LbqlHVSh2+zFtKHxWscjQ2IqGHKmIHum44la1F09OQ0NSFAe3bH5E3n6lf3Y/Im8/Ur+7FZe2/jPa5sroPW5FBJD/Rf6jSmxu3ynB6Srd/nt9j/8QAHhABAAEEAwEBAAAAAAAAAAAAAREAITFRECBBkTD/2gAIAQEAAT8h7uu2eWVZiIRI6raDENrRJQ2RKdNu18/g5UYCtoMQ2tElBYF59B5RNKsNkIvnNfQCO/8ApLLO61DIhaWSJQHrXwAD/wD6Syzr8Xbb+Xvptb2Dfw46dLIICdfT4SrjGTv/AP/aAAwDAQACAAMAAAAQAAAEAgAEgAAAAkAAAA//xAAUEQEAAAAAAAAAAAAAAAAAAABQ/9oACAEDAQE/EBP/xAAUEQEAAAAAAAAAAAAAAAAAAABQ/9oACAECAQE/EBP/xAAeEAEAAQQCAwAAAAAAAAAAAAABEQAQIWEwwSBBcf/aAAgBAQABPxDzFuqfDtNkkEMqNkhWCkv6SiESjvgt1rWNICqBWwQrBSX9JRALfchqwCRIrwEgOhPfKDCzBgIfQDRqEn5VIAtdCa/UCFmTAU4HVKT0rz5DIDIuVq3sQj0atSu5KOA//9k=";
    
    public function setup()
    {
        $this->mapper = new Mapper("proxies");
        $this->mapper->setDocumentDirectories(array('./test/ODM/Document/Stub' => 'test'));
        
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
        
        $this->jsonNotLazyLinkedRecord = json_decode('{
            "@type":          "d",
            "@rid":           "#12:0",
            "@version":        0,
            "is_true":         1,
            "is_false":        0,
            "@class":         "Address",
            "lazy_link":           {
                              "@type": "d", "@rid": "#14:0", "@version": 99, "@class": "Address", 
                              "name": "Rome", 
                              "link":{
                                "@type": "d", "@rid": "#15:0", "@version": 99, "@class": "Address", 
                                "name": "Italy"
                              }},
            "lazy_linklist":  [
                                {"@type": "d", "@rid": "#20:102", "@version": 1, "@class": "Address"}, 
                                {"@type": "d", "@rid": "#20:103", "@version": 1, "@class": "Address"}
                              ]
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
    
    public function testAJsonGetsConvertedToAnObject()
    {   
        $result = $this->mapper->hydrate($this->jsonRecord);
        
        $this->assertInstanceOf('Test\ODM\Document\Stub\Contact\Address', $result->getDocument());
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
        $result = $this->mapper->hydrate($this->jsonRecord);
        
        $this->assertEquals('Residence', $result->getDocument()->getType());
    }

    public function testDoublePropertiesGetsMappedInTheObject()
    {
        $result = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(122.231, $result->getDocument()->getCapital());
    }
    
    public function testIntegersGetsMappedInTheObject()
    {
        $result = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(12, $result->getDocument()->getNumber());
    }
    
    public function testFloatGetsMappedInTheObject()
    {
        $result = $this->mapper->hydrate($this->jsonRecord);

        $this->assertInternalType('float', $result->getDocument()->getFloating());
    }

    public function testShortPropertiesGetsMappedInTheObject()
    {
        $result = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(-32000, $result->getDocument()->getNegativeShort());
        $this->assertEquals(32000, $result->getDocument()->getPositiveShort());
    }

    public function testShortPropertiesDontThrowAnExceptionIfOverflowsAreTolerated()
    {
        $result = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(null, $result->getDocument()->getInvalidShort());
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
        $result = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(-32, $result->getDocument()->getNegativeLong());
        $this->assertEquals(32, $result->getDocument()->getPositiveLong());
    }

    public function testLongPropertiesDontThrowAnExceptionIfOverflowsAreTolerated()
    {
        $result = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(null, $result->getDocument()->getInvalidLong());
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
        $result = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(-32, $result->getDocument()->getNegativeByte());
        $this->assertEquals(32, $result->getDocument()->getPositiveByte());
    }

    public function testBytePropertiesDontThrowAnExceptionIfOverflowsAreTolerated()
    {
        $result = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(null, $result->getDocument()->getInvalidByte());
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
        $result = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals('ok', $result->getDocument()->getExampleProperty());
    }
    
    public function testBooleanPropertiesGetsMappedInTheObject()
    {
        $result = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(true, $result->getDocument()->getIsTrue());
        $this->assertEquals(false, $result->getDocument()->getIsFalse());
    }
    
    public function testBinaryPropertiesGetsMappedInTheObject()
    {
        $result = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(self::BINARY_64_ENCODED, $result->getDocument()->getImage());
    }
    
    public function testLinkedRecordsGetsMappedInTheObject()
    {
        $result = $this->mapper->hydrate($this->jsonLinkedRecord);
        $link = $result->getDocument()->getLink();
        $sonLink = $link->getDocument()->getLink();
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $link->getDocument());
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $sonLink->getDocument());
    }
    
    // public function testLazyLinkedRecordsGetsMappedInTheObject()
    // {
    //     $object = $this->mapper->hydrate($this->jsonLinkedRecord);
    //     
    //     $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $object->getDocument()->getLazyLink());
    // 
    //     $object = $this->mapper->hydrate($this->jsonNotLazyLinkedRecord);
    // 
    //     $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $object->getDocument()->getLazyLink());
    // }
    
    // public function testLazyLinkListGetsMappedInTheObject()
    // {
    //     $object = $this->mapper->hydrate($this->jsonLinkedRecord);
    //     $linklist = $object->getDocument()->getLazyLinkList();
    //     
    //     $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linklist[0]);
    //     $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linklist[1]);
    //     
    //     $object = $this->mapper->hydrate($this->jsonNotLazyLinkedRecord);
    //     $linklist = $object->getDocument()->getLazyLinkList();
    // 
    //     $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linklist[0]);
    //     $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linklist[1]);
    // }

    // public function testLazyLinkSetGetsMappedInTheObject()
    // {
    //     $object = $this->mapper->hydrate($this->jsonLinkedRecord);
    //     $linkset = call_user_func($object->getDocument()->lazy_linkset);
    //     
    //     $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linkset[0]);
    //     $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linkset[1]);
    // }
    
    public function testLinkSetGetsMappedInTheObject()
    {
        $result = $this->mapper->hydrate($this->jsonLinkedRecord);
        $linkset = $result->getDocument()->getLinkset();
        
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linkset[0]->getDocument());
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linkset[1]->getDocument());
    }
    
    // public function testLazyLinkMapGetsMappedInTheObject()
    // {
    //     $object = $this->mapper->hydrate($this->jsonLinkedRecord);
    //     $linkmap = call_user_func($object->getDocument()->lazy_linkmap);
    //     
    //     $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linkmap[0]);
    //     $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linkmap[1]);
    // }
    
    public function testLinkListGetsMappedInTheObject()
    {
        $result = $this->mapper->hydrate($this->jsonLinkedRecord);
        $linklist = $result->getDocument()->getLinkList();
        
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linklist[0]->getDocument());
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linklist[1]->getDocument());
    }
    
    public function testLinkMapGetsMappedInTheObject()
    {
        $result = $this->mapper->hydrate($this->jsonLinkedRecord);
        $linkmap = $result->getDocument()->getLinkMap();
        
        $keys = array_keys($linkmap);
        
        $this->assertEquals('first_key', $keys[0]);
        $this->assertEquals('second_key', $keys[1]);
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linkmap['first_key']->getDocument());
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linkmap['second_key']->getDocument());
    }
     
    public function testEmbeddedMapGetsMappedInTheObject()
    {
        $result = $this->mapper->hydrate($this->jsonEmbeddedMapRecord);
        $linkmap = $result->getDocument()->getEmbeddedMap();
        
        $keys = array_keys($linkmap);
        
        $this->assertEquals('first_key', $keys[0]);
        $this->assertEquals('second_key', $keys[1]);
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linkmap['first_key']->getDocument());
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $linkmap['second_key']->getDocument());
    }
    
    public function testDatePropertiesGetsMappedInTheObject()
    {
        $result = $this->mapper->hydrate($this->jsonRecord);

        $this->assertInstanceOf('\DateTime', $result->getDocument()->getDate());
        $this->assertEquals('2011-01-01', $result->getDocument()->getDate()->format('Y-d-m'));
    }

    public function testDatetimePropertiesGetsMappedInTheObject()
    {
        $result = $this->mapper->hydrate($this->jsonRecord);

        $this->assertInstanceOf('\DateTime', $result->getDocument()->getDateTime());
        $this->assertEquals('2011-01-01 21:00:00', $result->getDocument()->getDateTime()->format('Y-d-m H:i:s'));
    }

    public function testAnAnnotatedPropertyNotPassedWithTheJSONIsNullByDefault()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(null, $object->getDocument()->getAnnotatedButNotInJson());
    }

    public function testPropertiesGetsMappedInTheObjectOnlyIfAnnotated()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);

        $this->assertEquals(null, $object->getDocument()->getStreet());
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
        $this->assertInstanceOf('Test\ODM\Document\Stub\Contact\Address', $collection[0]->getDocument() );
        $this->assertInstanceOf('Test\ODM\Document\Stub\City', $collection[1]->getDocument() );
    }
    
    public function testCongowOrientObjectsOfDifferentClassesCanBeMappedByASinglePopo()
    {
        $collection = $this->mapper->hydrateCollection($this->jsonCollection);
        $this->assertInstanceOf('Test\ODM\Document\Stub\Contact\Address', $collection[2]->getDocument() );
    }
    
    public function testEmbeddedRecordsGetsMappedInTheObject()
    {
        $object = $this->mapper->hydrate($this->jsonRecord);
        $embedded = $object->getDocument()->getEmbedded();
        
        $this->assertInstanceOf("Test\ODM\Document\Stub\City", $embedded->getDocument());
    }
    
    public function testEmbeddedListedRecordsGetsMappedInTheObject()
    {
        $object     = $this->mapper->hydrate($this->jsonEmbeddedRecord);
        $embedded   = $object->getDocument()->getEmbeddedList();
        
        $this->assertEquals(2, count($embedded));
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $embedded[0]->getDocument());
    }
    
    public function testEmbeddedListedDataGetsMappedInTheObject()
    {
        $object             = $this->mapper->hydrate($this->jsonEmbeddedRecord);
        $embeddedIntegers   = $object->getDocument()->getEmbeddedIntegers();
        $embeddedStrings    = $object->getDocument()->getEmbeddedStrings();
        $embeddedBooleans   = $object->getDocument()->getEmbeddedBooleans();
        
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
        $result     = $this->mapper->hydrate($this->jsonEmbeddedSetRecord);
        $embedded   = $result->getDocument()->getEmbeddedSet();
        
        $this->assertEquals(2, count($embedded));
        $this->assertInstanceOf("Test\ODM\Document\Stub\Contact\Address", $embedded[0]->getDocument());
    }
    
    public function testEmbeddedSetDataGetsMappedInTheObject()
    {
        $object             = $this->mapper->hydrate($this->jsonEmbeddedSetRecord);
        $embeddedIntegers   = $object->getDocument()->getEmbeddedSetIntegers();
        $embeddedStrings    = $object->getDocument()->getEmbeddedSetStrings();
        $embeddedBooleans   = $object->getDocument()->getEmbeddedSetBooleans();
        
        $this->assertEquals(2, count($embeddedIntegers));
        $this->assertEquals(10, $embeddedIntegers[0]);        
        $this->assertEquals(2, count($embeddedStrings));
        $this->assertEquals('hola', $embeddedStrings[0]);
        $this->assertEquals(2, count($embeddedBooleans));
        $this->assertEquals(false, $embeddedBooleans[0]);
    }
}
