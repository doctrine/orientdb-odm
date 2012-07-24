<?php

/**
 * OClassTest
 *
 * @package    Congow\Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace test\Query\Command\Truncate;

use test\PHPUnit\TestCase;
use Congow\Orient\Query\Command\Truncate\OClass as TruncateClass;

class OClassTest extends TestCase
{
    public function testYouGenerateAValidSQLToTruncateAClass()
    {
        $truncate = new TruncateClass('myClass');

        $this->assertCommandGives("TRUNCATE CLASS myClass", $truncate->getRaw());
    }

    public function testTheNameArgumentIsFiltered()
    {
        $truncate = new TruncateClass('myClass 54..');

        $this->assertCommandGives("TRUNCATE CLASS myClass54", $truncate->getRaw());
    }
}
