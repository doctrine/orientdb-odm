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
        $this->create = new Count('indexName');
    }

    public function testTheSchemaIsValid()
    {
        $tokens = array(
            ':Name' => array(),
        );

        $this->assertTokens($tokens, $this->create->getTokens());
    }

    public function testConstructionOfAnObject()
    {
        $query = 'SELECT count(*) AS size from index:indexName';

        $this->assertCommandGives($query, $this->create->getRaw());
    }
}
