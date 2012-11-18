<?php

/**
 * ByteTest
 *
 * @package    Congow\Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 * @version
 */

namespace test\Integration\ODM\Mapper\DataType;

use test\PHPUnit\TestCase;

class ByteTest extends TestCase
{
    public function testHydrationOfAByteProperty()
    {
        $manager = $this->createManager();
        $role = $manager->find("#4:0");

        $this->assertInternalType('integer', $role->mode);
    }
}
