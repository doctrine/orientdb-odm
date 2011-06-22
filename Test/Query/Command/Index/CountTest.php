<?php

/**
 * CountTest
 *
 * @package    Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace Orient\Test\Query\Command\Index;

use Orient\Test\PHPUnit\TestCase;
use Orient\Query\Command\Index\Count;

class CountTest extends TestCase
{
    public function setup()
    {
        $this->count = new Count('indexName');
    }

    public function testTheSchemaIsValid()
    {
        $tokens = array(
            ':Name' => array(),
        );

        $this->assertTokens($tokens, $this->count->getTokens());
    }

    public function testConstructionOfAnObject()
    {
        $query = 'SELECT count(*) AS size from index:indexName';

        $this->assertCommandGives($query, $this->count->getRaw());
    }
}
