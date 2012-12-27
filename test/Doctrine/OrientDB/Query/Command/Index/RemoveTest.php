<?php

/**
 * RemoveTest
 *
 * @package    Doctrine\OrientDB
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace test\Doctrine\OrientDB\Query\Command\Index;

use test\PHPUnit\TestCase;
use Doctrine\OrientDB\Query\Command\Index\Remove;

class RemoveTest extends TestCase
{
    public function setup()
    {
        $this->remove = new Remove('indexName', 'k');
    }

    public function testTheSchemaIsValid()
    {
        $tokens = array(
            ':Name'     => array(),
            ':Where'    => array(),
        );

        $this->assertTokens($tokens, $this->remove->getTokens());
    }

    public function testConstructionOfAnObject()
    {
        $query = 'DELETE FROM index:indexName WHERE key = "k"';

        $this->assertCommandGives($query, $this->remove->getRaw());
    }

    public function testYouCanAlsoRemoveAnEntryByKey()
    {
        $this->remove = new Remove('indexName', 'k', '20:0');
        $query = 'DELETE FROM index:indexName WHERE key = "k" AND rid = #20:0';

        $this->assertCommandGives($query, $this->remove->getRaw());
    }

    public function testYouCanAlsoRemoveAnEntryByRid()
    {
        $this->remove = new Remove('indexName', null, '20:0');
        $query = 'DELETE FROM index:indexName WHERE rid = #20:0';

        $this->assertCommandGives($query, $this->remove->getRaw());
    }
}
