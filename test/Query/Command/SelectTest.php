<?php

/**
 * QueryTest
 *
 * @package    Congow\Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace test\Query\Command;

use Congow\Orient\Query\Command\Select;
use test\PHPUnit\TestCase;

class SelectTest extends TestCase
{
    public function setup()
    {
        $this->select = new Select(array('myClass'));
    }

    public function testSchemaIsValid()
    {
        $tokens = array(
            ':Projections' => array(),
            ':Target' => array(),
            ':Where' => array(),
            ':Between' => array(),
            ':OrderBy' => array(),
            ':Limit' => array(),
            ':Range' => array(),
        );

        $this->assertTokens($tokens, $this->select->getTokens());
    }

    public function testConstructionOfAnObject()
    {
        $query = 'SELECT FROM';
        $this->select = new Select();

        $this->assertEquals($query, $this->select->getRaw());

        $this->setup();
        $query = 'SELECT FROM myClass';

        $this->assertCommandGives($query, $this->select->getRaw());
    }

    public function testOrderingTheQueryWithMultipleInstructions()
    {
        $this->select->orderBy("name ASC");
        $query = 'SELECT FROM myClass ORDER BY name ASC';

        $this->assertCommandGives($query, $this->select->getRaw());

        $this->select->orderBy("name ASC");
        $this->select->orderBy("surname DESC");
        $query = 'SELECT FROM myClass ORDER BY name ASC, surname DESC';

        $this->assertCommandGives($query, $this->select->getRaw());

        $this->select->orderBy("id", false);
        $query = 'SELECT FROM myClass ORDER BY id';

        $this->assertCommandGives($query, $this->select->getRaw());

        $this->select->orderBy("name", true, true);
        $query = 'SELECT FROM myClass ORDER BY name, id';

        $this->assertCommandGives($query, $this->select->getRaw());
    }

    public function testLimitingTheQueryWithmultipleInstructions()
    {
        $this->select->limit(10);
        $query = 'SELECT FROM myClass LIMIT 10';

        $this->assertCommandGives($query, $this->select->getRaw());

        $this->select->limit(20);
        $query = 'SELECT FROM myClass LIMIT 20';

        $this->assertCommandGives($query, $this->select->getRaw());
    }

    public function testSpecifyARange()
    {
        $this->select->limit(10);
        $this->select->range('10:3');
        $query = 'SELECT FROM myClass LIMIT 10 RANGE 10:3';

        $this->assertCommandGives($query, $this->select->getRaw());

        $this->select->range(null, '10:4');
        $query = 'SELECT FROM myClass LIMIT 10 RANGE 10:3 10:4';

        $this->assertCommandGives($query, $this->select->getRaw());

        $this->select->range('10:5', '10:6');
        $query = 'SELECT FROM myClass LIMIT 10 RANGE 10:5 10:6';

        $this->assertCommandGives($query, $this->select->getRaw());

        $this->select->range('10:1');
        $query = 'SELECT FROM myClass LIMIT 10 RANGE 10:1 10:6';

        $this->assertCommandGives($query, $this->select->getRaw());

        $this->select->range('10:1', false);
        $query = 'SELECT FROM myClass LIMIT 10 RANGE 10:1';

        $this->assertCommandGives($query, $this->select->getRaw());

        $this->select->range(false, false);
        $query = 'SELECT FROM myClass LIMIT 10';

        $this->assertCommandGives($query, $this->select->getRaw());
    }

    public function testDoAComplexSelect()
    {
        $this->select->limit(10);
        $this->select->limit(20);
        $this->select->from(array('23:2', '12:4'), false);
        $this->select->select(array('id', 'name'));
        $this->select->select(array('name'));
        $this->select->range('10:3');
        $this->select->range(null, '12:0');

        $query = 'SELECT id, name FROM [23:2, 12:4] LIMIT 20 RANGE 10:3 12:0';

        $this->assertCommandGives($query, $this->select->getRaw());
    }

    public function testUsingTheFluentInterface()
    {
        $this->select->select(array('name', 'username', 'email'), false)
                ->from(array('12:0', '12:1'), false)
                ->where('any() traverse ( any() like "%danger%" )')
                ->orWhere("1 = ?", 1)
                ->andWhere("links = ?", 1)
                ->limit(20)
                ->orderBy('username')
                ->orderBy('name', true, true)
                ->range("12:0", "12:1");
        $sql =
                'SELECT name, username, email FROM [12:0, 12:1] WHERE any() traverse ( any() like "%danger%" ) OR 1 = "1" AND links = "1" ORDER BY name, username LIMIT 20 RANGE 12:0 12:1'
        ;

        $this->assertCommandGives($sql, $this->select->getRaw());
    }

    public function testYouCanSelectFromTheIndexes()
    {
        $this->select->from(array('index:coordinates'), false)
                ->between('k', "10.3", "10.7");
        $sql =
                'SELECT FROM index:coordinates WHERE k BETWEEN 10.3 AND 10.7'
        ;

        $this->assertCommandGives($sql, $this->select->getRaw());

        $this->select->resetWhere();
        $this->select->select(array('key'))
                ->from(array('index:coordinates'), false);
        $sql =
                'SELECT key FROM index:coordinates'
        ;

        $this->assertCommandGives($sql, $this->select->getRaw());

        $this->select->resetWhere();
        $this->select->select(array('key', 'value'))
                ->from(array('index:coordinates'), false);
        $sql =
                'SELECT key, value FROM index:coordinates'
        ;

        $this->assertCommandGives($sql, $this->select->getRaw());
    }
}
