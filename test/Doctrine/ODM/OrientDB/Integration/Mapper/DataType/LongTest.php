<?php

/**
 * LongTest
 *
 * @package    Doctrine\ODM\OrientDB
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 * @version
 */

namespace test\Doctrine\ODM\OrientDB\Integration\Mapper\DataType;

use test\PHPUnit\TestCase;
use Doctrine\OrientDB\Query\Query;

/**
 * @group integration
 */
class LongTest extends TestCase
{
    public function testHydrationOfALongProperty()
    {

        $manager = $this->createManager();

        $query = new Query();
        $query->update('Profile')
            ->set(array('hash' => 2937480 ))
            ->where('@rid = ?', '#'.$this->getClassId('Profile').':0');

        $manager->execute($query);

        $neoProfile = $manager->find("#".$this->getClassId('Profile').":0");

        $this->assertInternalType('integer', $neoProfile->hash);
    }
}
