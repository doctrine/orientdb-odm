<?php

/**
 * LongTest
 *
 * @package    Doctrine\OrientDB
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 * @version
 */

namespace test\Integration\ODM\Mapper\DataType;

use test\PHPUnit\TestCase;
use Doctrine\OrientDB\Query\Query;

class LongTest extends TestCase
{
    public function testHydrationOfALongProperty()
    {
        $manager = $this->createManager();

        $query = new Query();
        $query->update('Profile')
            ->set(array('hash' => '2937480'))
            ->where('@rid = ?', '#13:0');

        $manager->execute($query);

        $neoProfile = $manager->find("#13:0");
        $this->assertInternalType('integer', $neoProfile->hash);
    }
}
