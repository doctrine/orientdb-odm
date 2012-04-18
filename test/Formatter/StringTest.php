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
use Congow\Orient\Formatter\String;


class StringTest extends TestCase
{
    public function setup()
    {

    }

    public function testConvertPathToClassName()
    {
        $file      = "./test/ODM/Document/Stub/City.php";
        $namespace = "test";

        $className = String::convertPathToClassName($file, $namespace);
        $this->assertEquals('\test\ODM\Document\Stub\City', $className);
    }

    public function testConvertPathToClassNameWhenProvidingNestedNamespaces()
    {
        $file      = "./test/ODM/Document/Stub/City.php";
        $namespace = "test\ODM";

        $className = String::convertPathToClassName($file, $namespace);
        $this->assertEquals('\test\ODM\Document\Stub\City', $className);
    }

}
