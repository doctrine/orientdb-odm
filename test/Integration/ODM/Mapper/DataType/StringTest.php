<?php

/**
 * StringTest
 *
 * @package    Congow\Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 * @version
 */

namespace test\Integration\ODM\Mapper\DataType;

use test\PHPUnit\TestCase;

class StringTest extends TestCase
{
    public function testHydratingAStringProperty()
    {
        $manager = $this->createManager();
        $country = $manager->find('#15:1');

        $this->assertInternalType('string', $country->name);
    }
}
