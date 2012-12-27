<?php

/**
 * CreateTest
 *
 * @package    Doctrine\OrientDB
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace test\Doctrine\OrientDB\Query\Command\Index;

use test\PHPUnit\TestCase;
use Doctrine\OrientDB\Query\Command\Index\Create;

class CreateTest extends TestCase
{
    public function setup()
    {
        $this->create = new Create('p', 'unique', 'c');
    }

    public function testTheSchemaIsValid()
    {
        $tokens = array(
            ':IndexClass' => array(),
            ':Property' => array(),
            ':Type' => array(),
        );

        $this->assertTokens($tokens, $this->create->getTokens());
    }

    public function testConstructionOfAnObject()
    {
        $query = 'CREATE INDEX c.p unique';

        $this->assertCommandGives($query, $this->create->getRaw());
    }

    public function testConstructionOfAnIndexWithoutClass()
    {
        $query = 'CREATE INDEX p unique';
        $this->create = new Create('p','unique');

        $this->assertCommandGives($query, $this->create->getRaw());
    }

    public function testSettingTheIndexType()
    {
        $query = 'CREATE INDEX p string';
        $this->create = new Create('p', 'string');

        $this->assertCommandGives($query, $this->create->getRaw());
    }

    public function testSettingTheIndexTypeWithTheFluentInterface()
    {
        $query = 'CREATE INDEX c.p string';

        $this->assertCommandGives($query, $this->create->type('string')->getRaw());
    }
}
