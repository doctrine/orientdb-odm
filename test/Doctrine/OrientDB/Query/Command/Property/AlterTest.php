<?php

/**
 * QueryTest
 *
 * @package    Doctrine\OrientDB
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace test\Doctrine\OrientDB\Query\Command\Property;

use test\PHPUnit\TestCase;
use Doctrine\OrientDB\Query\Command\Property\Alter;

class AlterTest extends TestCase
{
    public function setup()
    {
        $this->alter = new Alter('property');
    }

    public function testTheSchemaIsValid()
    {
        $tokens = array(
            ':Class'        => array(),
            ':Property'     => array(),
            ':Attribute'    => array(),
            ':Value'        => array(),
        );

        $this->assertTokens($tokens, $this->alter->getTokens());
    }

    public function testConstructionOfAnObject()
    {
        $query = 'ALTER PROPERTY .property';

        $this->assertCommandGives($query, $this->alter->getRaw());
    }

    public function testFormattingAWholeAlter()
    {
        $this->alter->on('class');
        $this->alter->changing('name', 'prop2');
        $query = 'ALTER PROPERTY class.property name prop2';

        $this->assertCommandGives($query, $this->alter->getRaw());
    }

    public function testUsingTheFluentInterface()
    {
        $this->alter->changing('name', 'prop2')->on('class');
        $query = 'ALTER PROPERTY class.property name prop2';

        $this->assertCommandGives($query, $this->alter->getRaw());
    }
}
