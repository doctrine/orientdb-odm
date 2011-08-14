<?php

/**
 * CreateTest
 *
 * @package    Congow\Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace test\Query\Command\Index;

use test\PHPUnit\TestCase;
use Congow\Orient\Query\Command\Index\Put;

class PutTest extends TestCase
{
    public function setup()
    {
        $this->put = new Put('i', 'k', '12:0');
    }

    public function testTheSchemaIsValid()
    {
        $tokens = array(
            ':Name'     => array(),
            ':Key'      => array(),
            ':Value'    => array(),
        );

        $this->assertTokens($tokens, $this->put->getTokens());
    }

    public function testConstructionOfAnObject()
    {
        $query = 'INSERT INTO index:i (key,rid) values ("k", #12:0)';

        $this->assertCommandGives($query, $this->put->getRaw());
    }
}
