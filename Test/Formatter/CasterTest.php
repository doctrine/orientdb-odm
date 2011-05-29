<?php

/**
 * CasterTest
 *
 * @package    Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 * @version
 */

namespace Orient\Test;

use Orient\Test\PHPUnit\TestCase;
use Orient\Formatter\Caster;

class StubObject
{
    public function __toString(){
        return 'a';
    }
}


class CasterTest extends TestCase
{
    
    public function testConversionToString()
    {
        $this->assertTrue(is_string(Caster::castString('john')));
        $this->assertTrue(is_string(Caster::castString(true)));
        $this->assertTrue(is_string(Caster::castString(new StubObject())));
        $emtpyString = Caster::castString(new \StdClass());
        $this->assertTrue(empty($emtpyString));

    }
    
    public function testConversionToBoolean()
    {
        $this->assertTrue(is_bool(Caster::castBoolean(true)));
        $this->assertTrue(is_bool(Caster::castBoolean('john')));
        $this->assertTrue(is_bool(Caster::castBoolean(new StubObject())));
        
        $this->assertEquals(true, Caster::castBoolean(true));
        $this->assertEquals(true, Caster::castBoolean('john'));
        $this->assertEquals(false, Caster::castBoolean('0'));
        $this->assertEquals(true, Caster::castBoolean(new StubObject()));
    }
    
    public function testConversionToDate()
    {
        //$this->assertEquals(true, Caster::castDate('john')); //string
    }

}