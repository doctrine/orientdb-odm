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
use Doctrine\OrientDB\Query\Command\Index\Drop;

class DropTest extends TestCase
{
    public function setup()
    {
        $this->drop = new Drop('p', 'c');
    }

    public function testTheSchemaIsValid()
    {
        $tokens = array(
            ':IndexClass' => array(),
            ':Property' => array(),
        );

        $this->assertTokens($tokens, $this->drop->getTokens());
    }

    public function testConstructionOfAnObject()
    {
        $query = 'DROP INDEX c.p';

        $this->assertCommandGives($query, $this->drop->getRaw());
    }

    public function testConstructionOfAnIndexWithoutClass()
    {
        $query = 'DROP INDEX p';
        $this->drop = new Drop('p');

        $this->assertCommandGives($query, $this->drop->getRaw());
    }
}
