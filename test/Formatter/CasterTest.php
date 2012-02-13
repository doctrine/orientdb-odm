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

class StubObject
{
    public function __toString(){
        return 'a';
    }
}

class MockAdapter implements \Congow\Orient\Contract\Protocol\Adapter
{   
 public function execute($sql, $return = false)
 {
        
 }
 public function getResult()
 {
        
 }
}

class CasterTest extends TestCase
{
    public function setup()
    {
        $this->caster = new Caster(new Mapper('/'));
    }
    
    public function testInjectingTheValueInTheConstructor()
    {
        $this->caster = new Caster(new Mapper('/'),'v');
        $this->assertEquals('v', $this->caster->castString());
    }
    
    public function testStringToStringConversion()
    {
        $this->assertTrue(is_string($this->caster->setValue('john')->castString()));
    }

    public function testStringToDateTimeConversion()
    {
        $caster = $this->caster->setValue('2012-01-01 18:30:30:1231');

        $this->assertInstanceOf('DateTime', $datetime = $caster->castDateTime());
        $this->assertInstanceOf('DateTime', $date = $caster->castDate());
        $this->assertEquals($datetime, $date);
    }
    
    public function testBooleanToStringConversion()
    {
        $this->assertTrue(is_string($this->caster->setValue(true)->castString()));
    }
    
    public function testToStringableObjectToStringConversion()
    {
        $this->assertTrue(is_string($this->caster->setValue(new StubObject)->castString()));
    }
    
    public function testNotToStringableObjectToStringConversion()
    {
        $emtpyString = $this->caster->setValue(new \stdClass())->castString();
        $this->assertTrue(empty($emtpyString));
    }
    
    public function testBooleanToBooleanConversion()
    {
        $this->assertTrue(is_bool($this->caster->setValue(true)->castBoolean()));
        $this->assertEquals(true, $this->caster->setValue(true)->castBoolean());
    }
    
    public function testStringToBooleanConversion()
    {
        $this->assertTrue(is_bool($this->caster->setValue('john')->castBoolean()));
        $this->assertEquals(true, $this->caster->setValue('john')->castBoolean());
        $this->assertEquals(false, $this->caster->setValue('0')->castBoolean());
    }
    
    public function testObjectToBooleanConversion()
    {
        $this->assertTrue(is_bool($this->caster->setValue(new StubObject())->castBoolean()));
        $this->assertEquals(true, $this->caster->setValue(new StubObject())->castBoolean());
    }
    
    public function testNullIsReturnedWhenCastingToRidAnInvalidRid()
    {
        $this->caster = new Caster(new Mapper('/'),'v');
        $this->assertEquals(null, $this->caster->castLink());
    }
}