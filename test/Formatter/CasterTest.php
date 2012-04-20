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

    public function testInjectingTheValueInTheConstructor()
    {
        $this->caster = new Caster(new Mapper('/'),'v');
        $this->assertEquals('v', $this->caster->castString());
    }
}