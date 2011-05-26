<?php

/**
 * QueryTest
 *
 * @package    Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace Orient\Test\Query\Command\OClass;

use Orient\Test\PHPUnit\TestCase;
use Orient\Query\Command\OClass\Create;

class CreateTest extends TestCase
{
    public function setup()
    {
        $this->create = new Create('p');
    }

    public function testTheSchemaIsValid()
    {
        $tokens = array(
            ':Class' => array(),
        );

        $this->assertTokens($tokens, $this->create->getTokens());
    }

    public function testConstructionOfAnObject()
    {
        $query = 'CREATE CLASS p';

        $this->assertCommandGives($query, $this->create->getRaw());
    }
}
