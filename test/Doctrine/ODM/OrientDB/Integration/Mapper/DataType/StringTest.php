<?php

/**
 * StringTest
 *
 * @package    Doctrine\ODM\OrientDB
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 * @version
 */

namespace test\Doctrine\ODM\OrientDB\Integration\Mapper\DataType;

use test\PHPUnit\TestCase;

/**
 * @group integration
 */
class StringTest extends TestCase
{
    public function testHydratingAStringProperty()
    {
        $manager = $this->createManager();
        //Country
        $country = $manager->find('#'.$this->getClassId('Country').':0');

        $this->assertInternalType('string', $country->name);
    }
}
