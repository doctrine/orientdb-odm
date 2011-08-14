<?php

/**
 * QueryTest
 *
 * @package    Congow\Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace test\Query\Command\OClass;

use test\PHPUnit\TestCase;
use Congow\Orient\Query\Command\OClass\Alter;

class AlterTest extends TestCase
{
    public function setup()
    {
        $this->alter = new Alter('class', 'prop', 'value');
    }

    public function testTheSchemaIsValid()
    {
        $tokens = array(
            ':Class'        => array(),
            ':Attribute'    => array(),
            ':Value'        => array(),
        );

        $this->assertTokens($tokens, $this->alter->getTokens());
    }

    public function testConstructionOfAnObject()
    {
        $query = 'ALTER CLASS class prop value';

        $this->assertCommandGives($query, $this->alter->getRaw());
    }
}
