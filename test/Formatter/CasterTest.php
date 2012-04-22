<?php

/**
 * CasterTest
 *
 * @package    Congow\Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 * @version
 */

namespace test;

use test\PHPUnit\TestCase;
use Congow\Orient\ODM\Mapper;
use Congow\Orient\ODM\Manager;
use Congow\Orient\Formatter\Caster;


class CasterTest extends TestCase
{
    public function setup()
    {
        $this->mapper = new Mapper('/');
        $this->caster = new Caster($this->mapper);
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
        $this->mapper->enableMismatchesTolerance(true);
        $this->assertEquals($expected, $this->caster->setValue($input)->castBoolean());
    }
    
    /**
     * @dataProvider getForcedBooleans
     * @expectedException Congow\Orient\Exception\Casting\Mismatch
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
        $this->mapper->enableMismatchesTolerance(true);
        $this->assertEquals($expected, $this->caster->setValue($byte)->castByte());
    }
    
    /**
     * @dataProvider getForcedBytes
     * @expectedException Congow\Orient\Exception\Casting\Mismatch
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
        $this->mapper->enableMismatchesTolerance(true);
        $this->assertEquals($expected, $this->caster->setValue($long)->castLong());
    }
    
    /**
     * @dataProvider getForcedLongs
     * @expectedException Congow\Orient\Exception\Casting\Mismatch
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
        $this->mapper->enableMismatchesTolerance(true);
        $this->assertEquals($expected, $this->caster->setValue($integer)->castInteger());
    }
    
    /**
     * @dataProvider getForcedIntegers
     * @expectedException Congow\Orient\Exception\Casting\Mismatch
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
        $this->mapper->enableMismatchesTolerance(true);
        $this->assertEquals($expected, $this->caster->setValue($double)->castDouble());
    }
    
    /**
     * @dataProvider getForcedDoubles
     * @expectedException Congow\Orient\Exception\Casting\Mismatch
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
        $this->mapper->enableMismatchesTolerance(true);
        $this->assertEquals($expected, $this->caster->setValue($float)->castFloat());
    }
    
    /**
     * @dataProvider getForcedDoubles
     * @expectedException Congow\Orient\Exception\Casting\Mismatch
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
        $this->mapper->enableMismatchesTolerance(true);
        $this->assertEquals($expected, $this->caster->setValue($string)->castString());
    }
    
    /**
     * @dataProvider getForcedStrings
     * @expectedException Congow\Orient\Exception\Casting\Mismatch
     */
    public function testForcedStringsCastingRaisesAnException($expected, $integer)
    {
        $this->assertEquals($expected, $this->caster->setValue($integer)->castString());
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
        $this->caster = new Caster(new Mapper('/'),'v');
        $this->assertEquals('v', $this->caster->castString());
    }
}