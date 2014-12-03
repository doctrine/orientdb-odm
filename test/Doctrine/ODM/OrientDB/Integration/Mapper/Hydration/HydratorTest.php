<?php

namespace test\Doctrine\ODM\OrientDB\Integration\Mapper\Hydration;


use test\PHPUnit\TestCase;

class HydratorTest extends TestCase
{
    const BINARY_64_ENCODED = "data:;base64,/9j/4AAQSkZJRgABAgAAZABkAAD/7AARRHVja3kAAQAEAAAAWAAA/+4ADkFkb2JlAGTAAAAAAf/bAEMAAQEBAQEBAQEBAQIBAQECAgIBAQICAwICAgICAwQDAwMDAwMEBAQEBQQEBAYGBgYGBggICAgICQkJCQkJCQkJCf/bAEMBAgICAwMDBQQEBQgGBQYICQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCf/CABEIADAAMAMBEQACEQEDEQH/xAAZAAEBAQEBAQAAAAAAAAAAAAAABwkIBAb/xAAUAQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIQAxAAAAHYQAAAnB9We4nBVQYsnfpVDFk37AAABxYQAFVO/QAAD//EAB0QAAMBAAIDAQAAAAAAAAAAAAUGBwQAFwgQIDD/2gAIAQEAAQUC+y7gor+kaTGmcWnTmxZhr0kGdvryNktBfHeJrZpRmL0N2mUiJxOnKNO/CjTFudDfQVJ50FSeJMhd1dn+/wD/xAAUEQEAAAAAAAAAAAAAAAAAAABQ/9oACAEDAQE/ARP/xAAUEQEAAAAAAAAAAAAAAAAAAABQ/9oACAECAQE/ARP/xAArEAABBAAFAgQHAQAAAAAAAAABAgMEBQAGERIUE0EHFSIxECAhMDiR1DL/2gAIAQEABj8C+dEO+zTXUkxxAdbiy5rMZxTZJAUEuKB01B+uGbKosGLWukbuPPjOpfZXsJSdq0Eg6EaYkTJkhESHEQp2VKdUENttoGqlKUfoAB7nDNbUZxqrWxkbuPAjWDD7y9gKjtQhZJ0A1+NXb5UoPNa6PVMxnpHKjMaPIffWU6POIPssYyzl7MMPy+4r+Zy4nUQ7t6sp1xPqbKknVKh3xnGorWeRY2tVYRoEfcEb3n2FoQnVRAGpPfGWcw5hyz5fT1/M5cvmRXdvViutp9LbqlHVSh2+zFtKHxWscjQ2IqGHKmIHum44la1F09OQ0NSFAe3bH5E3n6lf3Y/Im8/Ur+7FZe2/jPa5sroPW5FBJD/Rf6jSmxu3ynB6Srd/nt9j/8QAHhABAAEEAwEBAAAAAAAAAAAAAREAITFRECBBkTD/2gAIAQEAAT8h7uu2eWVZiIRI6raDENrRJQ2RKdNu18/g5UYCtoMQ2tElBYF59B5RNKsNkIvnNfQCO/8ApLLO61DIhaWSJQHrXwAD/wD6Syzr8Xbb+Xvptb2Dfw46dLIICdfT4SrjGTv/AP/aAAwDAQACAAMAAAAQAAAEAgAEgAAAAkAAAA//xAAUEQEAAAAAAAAAAAAAAAAAAABQ/9oACAEDAQE/EBP/xAAUEQEAAAAAAAAAAAAAAAAAAABQ/9oACAECAQE/EBP/xAAeEAEAAQQCAwAAAAAAAAAAAAABEQAQIWEwwSBBcf/aAAgBAQABPxDzFuqfDtNkkEMqNkhWCkv6SiESjvgt1rWNICqBWwQrBSX9JRALfchqwCRIrwEgOhPfKDCzBgIfQDRqEn5VIAtdCa/UCFmTAU4HVKT0rz5DIDIuVq3sQj0atSu5KOA//9k=";

    protected $hydrator;

    public function setup()
    {
        $manager = $this->createManager(array('document_dirs' => array('test/Doctrine/ODM/OrientDB/Document/Stub' => 'test')));
        $this->hydrator = $manager->getUnitOfWork()->getHydrator();

        $this->emptyJsonRecord = json_decode('{
            "@type":          "d",
            "@rid":           "#13:0",
            "@version":        0,
            "is_true":         1,
            "is_false":        0,
            "@class":         "EmptyAddress",
            "string":          null,
            "integer":         null,
            "image":          "' . base64_encode(fread(fopen(__DIR__ . '/../../../bin/image.jpg', "r"), filesize(__DIR__ . '/../../../bin/image.jpg'))) . '",
              "embedded":      {
                                "@type": "d", "@version": 99, "@class": "OCity",
                                "name": "Rome"
                               }
         }');

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
            "number":         "12",
            "positive_long":  "32",
            "negative_long":  "-32",
            "positive_byte":  "32",
            "negative_byte":  "-32",
            "floating":       "10.5",
            "image":          "' . base64_encode(fread(fopen(__DIR__ . '/../../../bin/image.jpg', "r"), filesize(__DIR__ . '/../../../bin/image.jpg'))) . '",
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

    public function testHydratingNullableAttributes()
    {
        $result = $this->hydrator->hydrate($this->emptyJsonRecord);

        $this->assertInstanceOf('test\Doctrine\ODM\OrientDB\Document\Stub\Contact\EmptyAddress', $result);
    }

    public function testAJsonGetsConvertedToAnObject()
    {
        $result = $this->hydrator->hydrate($this->jsonRecord);

        $this->assertInstanceOf('test\Doctrine\ODM\OrientDB\Document\Stub\Contact\Address', $result);
    }

    /**
     * @expectedException \Doctrine\ODM\OrientDB\DocumentNotFoundException
     */
    public function testAnExceptionIsRaisedWhenAnObjectGetsPersistedWithoutAClass()
    {
        $object = $this->hydrator->hydrate($this->jsonRecordNoClass);
    }

    /**
     * @expectedException \Doctrine\ODM\OrientDB\OClassNotFoundException
     */
    public function testAnExceptionIsRaisedWhenAnObjectGetsPersistedWithAWrongClass()
    {
        $object = $this->hydrator->hydrate($this->jsonRecordWrongClass);
    }

    public function testPropertiesCanHaveDifferentNamesInCongowOrientAndPopo()
    {
        $result = $this->hydrator->hydrate($this->jsonRecord);

        $this->assertEquals('ok', $result->getExampleProperty());
    }

    public function testAnAnnotatedPropertyNotPassedWithTheJSONIsNullByDefault()
    {
        $object = $this->hydrator->hydrate($this->jsonRecord);

        $this->assertEquals(null, $object->getAnnotatedButNotInJson());
    }

    public function testPropertiesGetsMappedInTheObjectOnlyIfAnnotated()
    {
        $object = $this->hydrator->hydrate($this->jsonRecord);

        $this->assertEquals(null, $object->getStreet());
    }

    public function testNoRecordsIsLostWhenHydratingACollection()
    {
        $collection = $this->hydrator->hydrateCollection($this->jsonCollection);
        $this->assertEquals(3, count($collection));
    }

    public function testHidratedCollectionsContainPopo()
    {
        $collection = $this->hydrator->hydrateCollection($this->jsonCollection);
        $this->assertInstanceOf('test\Doctrine\ODM\OrientDB\Document\Stub\Contact\Address', $collection[0] );
        $this->assertInstanceOf('test\Doctrine\ODM\OrientDB\Document\Stub\City', $collection[1] );
    }

    public function testCongowOrientObjectsOfDifferentClassesCanBeMappedByASinglePopo()
    {
        $collection = $this->hydrator->hydrateCollection($this->jsonCollection);
        $this->assertInstanceOf('test\Doctrine\ODM\OrientDB\Document\Stub\Contact\Address', $collection[2] );
    }
}