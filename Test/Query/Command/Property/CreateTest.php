<?php

/**
 * CreateTest
 *
 * @package    Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace Orient\Test\Query\Command\Property;

use Orient\Test\PHPUnit\TestCase;
use Orient\Query\Command\Property\Create;

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
            ':Property' => array(),
            ':Type' => array(),
            ':Linked' => array(),
        );

        $this->assertTokens($tokens, $this->create->getTokens());
    }

    public function testConstructionOfAnObject()
    {
        $query = 'CREATE PROPERTY .p';

        $this->assertCommandGives($query, $this->create->getRaw());
    }

    public function testUsingTheFluentInterface()
    {
        $query = 'CREATE PROPERTY c.p map link';
        $this->create = new Create('p', 'map', 'link');

        $this->assertCommandGives($query, $this->create->on('c')->getRaw());
    }
}
