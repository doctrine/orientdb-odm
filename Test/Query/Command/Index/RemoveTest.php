<?php

/**
 * RemoveTest
 *
 * @package    Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace Orient\Test\Query\Command\Index;

use Orient\Test\PHPUnit\TestCase;
use Orient\Query\Command\Index\Remove;

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
}
