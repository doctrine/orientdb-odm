<?php

/**
 * CasterTest
 *
 * @package    Doctrine\ODM\OrientDB
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 * @version
 */

namespace test\Doctrine\ODM\OrientDB\Caster;

use Doctrine\ODM\OrientDB\Collections\ArrayCollection;
use test\PHPUnit\TestCase;
use Doctrine\ODM\OrientDB\Mapper;
use Doctrine\ODM\OrientDB\Caster\Caster;
use Doctrine\ODM\OrientDB\Types\Rid;

class CasterTest extends TestCase
{
    private $hydrator;
    private $inflector;
    private $caster;

    public function setup()
    {
        $manager = $this->createManager();
        $this->inflector = $manager->getInflector();
        $this->hydrator = $manager->getUnitOfWork()->getHydrator();
        $this->caster = new Caster($this->hydrator, $this->inflector);
    }

    /**
     * @dataProvider getBooleans
     */
    public function testBooleanCasting($expected, $input)
    {
        $this->assertEquals($expected, $this->caster->setValue($input)->castBoolean());
    }

    public function getBooleans()
    {
        return array(
            array(true, true),
            array(true, 1),
            array(true, 'true'),
            array(true, '1'),
            array(false, '0'),
            array(false, 'false'),
            array(false, false),
            array(false, 0),
        );
    }

    /**
     * @dataProvider getForcedBooleans
     */
    public function testForcedBooleanCasting($expected, $input)
    {
        $this->hydrator->enableMismatchesTolerance(true);
        $this->assertEquals($expected, $this->caster->setValue($input)->castBoolean());
    }

    /**
     * @dataProvider getForcedBooleans
     * @expectedException \Doctrine\ODM\OrientDB\Caster\CastingMismatchException
     */
    public function testForcedBooleanCastingRaisesAnException($expected, $input)
    {
        $this->caster->setValue($input)->castBoolean();
    }

    public function getForcedBooleans()
    {
        return array(
            array(true, 'ciao'),
            array(true, 1111),
            array(false, ''),
            array(false, null),
            array(true, ' '),
            array(true, 'off'),
            array(true, 12.12),
            array(true, (float) 12.12),
            array(true, '12,12'),
            array(true, -50),
        );
    }

    /**
     * @dataProvider getBytes
     */
    public function testBytesCasting($byte)
    {
        $this->assertEquals($byte, $this->caster->setValue($byte)->castByte());
    }

    public function getBytes()
    {
        return array(
            array(0),
            array(1),
            array(100),
            array(127),
            array(-127),
            array(-128),
            array(-1),
        );
    }

    /**
     * @dataProvider getForcedBytes
     */
    public function testForcedBytesCasting($expected, $byte)
    {
        $this->hydrator->enableMismatchesTolerance(true);
        $this->assertEquals($expected, $this->caster->setValue($byte)->castByte());
    }

    /**
     * @dataProvider getForcedBytes
     * @expectedException \Doctrine\ODM\OrientDB\Caster\CastingMismatchException
     */
    public function testForcedBytesCastingRaisesAnException($expected, $byte)
    {
        $this->assertEquals($expected, $this->caster->setValue($byte)->castByte());
    }

    public function getForcedBytes()
    {
        return array(
            array(Caster::BYTE_MAX_VALUE, '129'),
            array(Caster::BYTE_MIN_VALUE, '-129'),
            array(Caster::BYTE_MAX_VALUE, '2000'),
            array(Caster::BYTE_MIN_VALUE, '-2000'),
            array(Caster::BYTE_MIN_VALUE, (float) -500.12),
            array(Caster::BYTE_MAX_VALUE, (float) 500.12),
            array(Caster::BYTE_MIN_VALUE, '-1500/3'),
            array(Caster::BYTE_MAX_VALUE, '1500/3'),
            array(127, 'ciao'),
        );
    }

    /**
     * @dataProvider getLongs
     */
    public function testLongsCasting($long)
    {
        $this->assertEquals($long, $this->caster->setValue($long)->castLong());
    }

    public function getLongs()
    {
        return array(
            array(0),
            array(1),
            array(100),
            array(127),
            array(1273825789),
            array(-127),
            array(-12735355),
            array(-128),
            array(-1),
        );
    }

    /**
     * @dataProvider getForcedLongs
     */
    public function testForcedLongsCasting($expected, $long)
    {
        $this->hydrator->enableMismatchesTolerance(true);
        $this->assertEquals($expected, $this->caster->setValue($long)->castLong());
    }

    /**
     * @dataProvider getForcedLongs
     * @expectedException \Doctrine\ODM\OrientDB\Caster\CastingMismatchException
     */
    public function testForcedLongsCastingRaisesAnException($expected, $long)
    {
        $this->assertEquals($expected, $this->caster->setValue($long)->castLong());
    }

    public function getForcedLongs()
    {
        return array(
            array(Caster::LONG_LIMIT, Caster::LONG_LIMIT + '129'),
            array(Caster::LONG_LIMIT, - Caster::LONG_LIMIT - '129'),
            array(Caster::LONG_LIMIT, Caster::LONG_LIMIT + '2000'),
            array(Caster::LONG_LIMIT, - Caster::LONG_LIMIT -'2000'),
            array(Caster::LONG_LIMIT, - Caster::LONG_LIMIT -(float) 500.12),
            array(Caster::LONG_LIMIT, Caster::LONG_LIMIT + (float) 500.12),
            array(Caster::LONG_LIMIT, - Caster::LONG_LIMIT - '1500/3'),
            array(Caster::LONG_LIMIT, Caster::LONG_LIMIT + '1500/3'),
        );
    }

    /**
     * @dataProvider getIntegers
     */
    public function testIntegersCasting($expected, $integer)
    {
        $this->assertEquals($expected, $this->caster->setValue($integer)->castInteger());
    }

    public function getIntegers()
    {
        return array(
            array(0, '0'),
            array(1, 1),
            array(100, '100'),
            array(-4, '-4'),
        );
    }

    /**
     * @dataProvider getForcedIntegers
     */
    public function testForcedIntegerCasting($expected, $integer)
    {
        $this->hydrator->enableMismatchesTolerance(true);
        $this->assertEquals($expected, $this->caster->setValue($integer)->castInteger());
    }

    /**
     * @dataProvider getForcedIntegers
     * @expectedException \Doctrine\ODM\OrientDB\Caster\CastingMismatchException
     */
    public function testForcedIntegersCastingRaisesAnException($expected, $integer)
    {
        $this->assertEquals($expected, $this->caster->setValue($integer)->castInteger());
    }

    public function getForcedIntegers()
    {
        return array(
            array(0, 'ciao'),
            array(0, null),
            array(1, new \stdClass()),
        );
    }

    /**
     * @dataProvider getDecimals
     */
    public function testDecimalCasting($expected, $double)
    {
        $this->assertEquals($expected, $this->caster->setValue($double)->castDecimal());
    }

    /**
     * @dataProvider getForcedDecimals
     */
    public function testForcedDecimalCasting($expected, $decimal)
    {
        $this->hydrator->enableMismatchesTolerance(true);
        $this->assertEquals($expected, $this->caster->setValue($decimal)->castDecimal());
    }

    /**
     * @dataProvider getForcedDecimals
     * @expectedException \Doctrine\ODM\OrientDB\Caster\CastingMismatchException
     */
    public function testForcedDecimalCastingRaisesAnException($expected, $decimal)
    {
        $this->assertEquals($expected, $this->caster->setValue($decimal)->castDecimal());
    }

    public function getDecimals()
    {
        return array(
            array(1E-8, "1E-8"), //0.00000001
            array(4.9E-324, 4.8E-324),
            array(4.9E100, 4.9E100),
            array(1.7976931348623157E+308, 1.7976931348623157E+308),

        );
    }

    public function getForcedDecimals()
    {
        return array(
            array(4.9E-324, 'ciao'),
            array(4.9E-324, null),
        );
    }

    /**
     * @dataProvider getDoubles
     */
    public function testDoublesCasting($expected, $double)
    {
        $this->assertEquals($expected, $this->caster->setValue($double)->castDouble());
    }

    public function getDoubles()
    {
        return array(
            array(0.2, '0.2'),
            array(11, 11),
            array(0, '00.00000000000000'),
            array(-4, -4),
            array(-4, '-4'),
        );
    }

    /**
     * @dataProvider getForcedDoubles
     */
    public function testForcedDoublesCasting($expected, $double)
    {
        $this->hydrator->enableMismatchesTolerance(true);
        $this->assertEquals($expected, $this->caster->setValue($double)->castDouble());
    }

    /**
     * @dataProvider getForcedDoubles
     * @expectedException \Doctrine\ODM\OrientDB\Caster\CastingMismatchException
     */
    public function testForcedDoublesCastingRaisesAnException($expected, $ddouble)
    {
        $this->assertEquals($expected, $this->caster->setValue($ddouble)->castDouble());
    }

    public function getForcedDoubles()
    {
        return array(
            array(0, ''),
            array(0, null),
            array(0, 'one'),
            array('15', '15/3'),
            array(15.2, '15.2.2'),
        );
    }

    /**
     * @dataProvider getDoubles
     */
    public function testFloatsCasting($expected, $float)
    {
        $this->assertEquals($expected, $this->caster->setValue($float)->castFloat());
    }

    /**
     * @dataProvider getForcedDoubles
     */
    public function testForcedFloatsCasting($expected, $float)
    {
        $this->hydrator->enableMismatchesTolerance(true);
        $this->assertEquals($expected, $this->caster->setValue($float)->castFloat());
    }

    /**
     * @dataProvider getForcedDoubles
     * @expectedException \Doctrine\ODM\OrientDB\Caster\CastingMismatchException
     */
    public function testForcedFloatsCastingRaisesAnException($expected, $float)
    {
        $this->assertEquals($expected, $this->caster->setValue($float)->castFloat());
    }

    /**
     * @dataProvider getStrings
     */
    public function testStringCasting($expected, $string)
    {
        $this->assertEquals($expected, $this->caster->setValue($string)->castString());
    }

    public function getStrings()
    {
        return array(
            array('0', '0'),
            array('hello', 'hello'),
            array('', ''),
        );
    }

    /**
     * @dataProvider getForcedStrings
     */
    public function testForcedStringsCasting($expected, $string)
    {
        $this->hydrator->enableMismatchesTolerance(true);
        $this->assertEquals($expected, $this->caster->setValue($string)->castString());
    }

    /**
     * @dataProvider getForcedStrings
     * @expectedException \Doctrine\ODM\OrientDB\Caster\CastingMismatchException
     */
    public function testForcedStringsCastingRaisesAnException($expected, $string)
    {
        $this->assertEquals($expected, $this->caster->setValue($string)->castString());
    }

    public function getForcedStrings()
    {
        return array(
            array('12', 12),
            array('-12', -12),
            array('', null),
            array('Array', array(1,2,3)),
        );
    }

    public function testInjectingTheValueInTheConstructor()
    {
        $this->caster = new Caster($this->hydrator, $this->inflector, 'v');
        $this->assertEquals('v', $this->caster->castString());
    }

    /**
     * @dataProvider getShorts
     */
    public function testShortsCasting($short)
    {
        $this->assertEquals($short, $this->caster->setValue($short)->castShort());
    }

    public function getShorts()
    {
        return array(
            array(0),
            array(1),
            array(100),
            array(127),
            array(32766),
            array(-127),
            array(-32766),
            array(-128),
            array(-1),
        );
    }

    /**
     * @dataProvider getForcedShorts
     */
    public function testForcedShortsCasting($expected, $short)
    {
        $this->hydrator->enableMismatchesTolerance(true);
        $this->assertEquals($expected, $this->caster->setValue($short)->castShort());
    }

    /**
     * @dataProvider getForcedShorts
     * @expectedException \Doctrine\ODM\OrientDB\Caster\CastingMismatchException
     */
    public function testForcedShortsCastingRaisesAnException($expected, $short)
    {
        $this->assertEquals($expected, $this->caster->setValue($short)->castShort());
    }

    public function getForcedShorts()
    {
        return array(
            array(32767, 32767),
            array(32767, -32767),
            array('bella', 'bella'),
            array(true, true),
            array(array(),array()),
        );
    }

    /**
     * @dataProvider getDateTimes
     */
    public function testDateTimesCasting($expected, $datetimes)
    {
        $this->assertEquals($expected, $this->caster->setValue($datetimes)->castDateTime());
    }

    public function getDateTimes()
    {
        return array(
            array(new \DateTime('2011-01-01 11:11:11'), '2011-01-01 11:11:11'),
        );
    }

    /**
     * @dataProvider getDates
     */
    public function testDatesCasting($expected,$date)
    {
        $this->hydrator->enableMismatchesTolerance(true);
        $this->assertEquals($expected, $this->caster->setValue($date)->castDate());
    }

    public function getDates()
    {
        return array(
            array(new \DateTime('2012-12-30'),'2012-12-30'),
        );
    }

    /**
     * @dataProvider getBinaries
     */
    public function testBinaryCasting($binary)
    {
        $this->assertEquals('data:;base64,' . $binary, $this->caster->setValue($binary)->castBinary());
    }

    public function getBinaries()
    {
        return array(
            array('2011-01-01 11:11:11'),
            array(12),
            array(-12),
        );
    }

    /**
     * @dataProvider getForcedBinaries
     */
    public function testForcedBinaryCasting($binary)
    {
        $this->assertEquals('data:;base64,' . $binary, $this->caster->setValue($binary)->castBinary());
    }

    public function getForcedBinaries()
    {
        return array(
            array(new \Doctrine\OrientDB\Binding\Client\Http\CurlClientResponse("1\r\n\r\n2")),
        );
    }

    /**
     * @dataProvider getLinks
     */
    public function testLinksCasting($expected,$link)
    {
        $this->assertEquals($expected, $this->caster->setValue($link)->castLink());
    }

    public function getLinks()
    {
        $addressId = '#'.$this->getClassId('Address').':0';
        $orientDocument = new \stdClass();
        $orientDocument->{"@class"} = 'Address';
        $orientDocument->{"@rid"} = $addressId;

        $address = $this->ensureProxy($orientDocument);

        return array(
            array($address, $orientDocument),
            array($address, $addressId),
            array(null, 'pete')
        );
    }

    /**
     * @dataProvider getLinkCollections
     */
    public function testLinkListCasting($expected,$linkCollection)
    {
        $this->assertEquals($expected, $this->caster->setValue($linkCollection)->castLinkList());
    }

    /**
     * @dataProvider getLinkCollections
     */
    public function testLinkSetCasting($expected,$linkCollection)
    {
        $this->assertEquals($expected, $this->caster->setValue($linkCollection)->castLinkSet());
    }

    /**
     * @dataProvider getLinkCollections
     */
    public function testLinkMapCasting($expected,$linkCollection)
    {

        $this->assertEquals($expected, $this->caster->setValue($linkCollection)->castLinkMap());
    }

    public function getLinkCollections()
    {
        $orientDocument = new \stdClass();
        $orientDocument->{"@class"} = 'Address';
        $orientDocument->{"@rid"} = '#'.$this->getClassId('Address').':0';;


        $address = $this->ensureProxy($orientDocument);

        $countryRid = '#'.$this->getClassId('Country').':0';;

        return array(
            array(new ArrayCollection(array('hello' => $this->createManager()->getReference($countryRid))), array('hello' => $countryRid)),
            array(new ArrayCollection(array('hello' => $address)), array('hello' => $orientDocument)),
        );
    }

    /**
     * @dataProvider getEmbedded
     */
    public function testEmbeddedCasting($expected,$embedded)
    {
        $this->assertEquals($expected, $this->caster->setValue($embedded)->castEmbedded());
    }

    public function getEmbedded()
    {
        $orientDocument = new \stdClass();
        $orientDocument->{"@class"} = 'Address';
        $orientDocument->{"@rid"} = '#'.$this->getClassId('Address').':0';

        $address = $this->ensureProxy($orientDocument);

        return array(
            array($address, $orientDocument),
        );
    }

    /**
     * @dataProvider getEmbeddedSet
     */
    public function testEmbeddedSetCasting($expected,$embeddedSet)
    {
        $property = $this->getMock('Doctrine\ODM\OrientDB\Mapper\Annotations\Property', null, array(array('cast' => 'embedded')));

        $this->caster->setProperty('annotation', $property);

        $this->assertEquals($expected, $this->caster->setValue($embeddedSet)->castEmbeddedSet());
    }

    /**
     * @dataProvider getEmbeddedSet
     */
    public function testEmbeddedMapCasting($expected,$embeddedSet)
    {
        $property = $this->getMock('Doctrine\ODM\OrientDB\Mapper\Annotations\Property', null, array(array('cast' => 'embedded')));

        $this->caster->setProperty('annotation', $property);

        $this->assertEquals($expected, $this->caster->setValue($embeddedSet)->castEmbeddedMap());
    }

    /**
     * @dataProvider getEmbeddedSet
     */
    public function testEmbeddedListCasting($expected,$embeddedSet)
    {
        $property = $this->getMock('Doctrine\ODM\OrientDB\Mapper\Annotations\Property', null, array(array('cast' => 'embedded')));

        $this->caster->setProperty('annotation', $property);

        $this->assertEquals($expected, $this->caster->setValue($embeddedSet)->castEmbeddedList());
    }

    public function getEmbeddedSet()
    {
        $orientDocument = new \stdClass();
        $orientDocument->{"@class"} = 'Address';
        $orientDocument->{"@rid"} = '#'.$this->getClassId('Address').':0';

        $address = $this->ensureProxy($orientDocument);

        return array(
            array(array('hello' => $address), array('hello' => $orientDocument)),
        );
    }
}
