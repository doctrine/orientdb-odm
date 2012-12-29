<?php

/**
 * QueryTest
 *
 * @package    Doctrine\OrientDB
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace test\Doctrine\OrientDB\Query\Command;

use Doctrine\OrientDB\Query\Command\Select;
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
            ':Skip' => array()
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

        $this->select->orderBy("name ASC", false);
        $this->select->orderBy("surname DESC");
        $query = 'SELECT FROM myClass ORDER BY name ASC, surname DESC';

        $this->assertCommandGives($query, $this->select->getRaw());

        $this->select->orderBy("id", false);
        $query = 'SELECT FROM myClass ORDER BY id';

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

    public function testQueryPaginationSkip()
    {
        $this->select->skip(0);
        $query = 'SELECT FROM myClass SKIP 0';

        $this->assertCommandGives($query, $this->select->getRaw());

        $this->select->skip(10)->limit(20);
        $query = 'SELECT FROM myClass SKIP 10 LIMIT 20';

        $this->assertCommandGives($query, $this->select->getRaw());
    }

    public function testDoAComplexSelect()
    {
        $this->select->limit(10);
        $this->select->limit(20);
        $this->select->from(array('23:2', '12:4'), false);
        $this->select->select(array('id', 'name'));
        $this->select->select(array('lastname'));
        $this->select->skip(10);

        $query = 'SELECT id, name, lastname FROM [23:2, 12:4] SKIP 10 LIMIT 20';

        $this->assertCommandGives($query, $this->select->getRaw());
    }

    public function testUsingTheFluentInterface()
    {
        $this->select
                ->select(array('name', 'username', 'email'), false)
                ->from(array('12:0', '12:1'), false)
                ->where('any() traverse ( any() like "%danger%" )')
                ->orWhere("1 = ?", 1)
                ->orWhere("1 = ?", NULL)
                ->andWhere("links = ?", "1")
                ->limit(20)
                ->orderBy('username')
                ->orderBy('name', true, true)
                ->skip(10);

        $sql = 'SELECT name, username, email FROM [12:0, 12:1] WHERE any() traverse ( any() like "%danger%" ) OR 1 = 1 OR 1 IS NULL AND links = "1" ORDER BY name, username SKIP 10 LIMIT 20';

        $this->assertCommandGives($sql, $this->select->getRaw());
    }

    public function testYouCanSelectFromTheIndexes()
    {
        $this->select
                ->from(array('index:coordinates'), false)
                ->between('k', "10.3", "10.7");

        $sql ='SELECT FROM index:coordinates WHERE k BETWEEN 10.3 AND 10.7';

        $this->assertCommandGives($sql, $this->select->getRaw());

        $this->select->resetWhere();
        $this->select
                ->select(array('key'))
                ->from(array('index:coordinates'), false);

        $sql = 'SELECT key FROM index:coordinates';

        $this->assertCommandGives($sql, $this->select->getRaw());

        $this->select->resetWhere();
        $this->select
                ->select(array('key', 'value'), false)
                ->from(array('index:coordinates'), false);

        $sql = 'SELECT key, value FROM index:coordinates';

        $this->assertCommandGives($sql, $this->select->getRaw());
    }

    public function testAllowsFieldAliases()
    {
        $this->select
                ->select(array('name AS aliased_name', 'surname AS aliased surname!'))
                ->from(array('profile'), false);

        $sql = 'SELECT name AS aliased_name, surname AS aliased surname! FROM profile';

        $this->assertCommandGives($sql, $this->select->getRaw());
    }

    public function testAllowsSQLFunctionOnFields()
    {
        $this->select
                ->select(array('MIN(x) AS fn_min', 'MAX(y) AS fn_max', 'COUNT(*) AS fn_count'))
                ->from(array('MapPoint'), false);

        $sql = 'SELECT MIN(x) AS fn_min, MAX(y) AS fn_max, COUNT(*) AS fn_count FROM MapPoint';

        $this->assertCommandGives($sql, $this->select->getRaw());
    }

    public function testAllowsFilterMethodsOnFields()
    {
        $this->select
                ->select(array('name.toUpperCase() AS name_uppercase', 'surname.toLowerCase() AS surname_lowercase'))
                ->from(array('profile'), false);

        $sql = 'SELECT name.toUpperCase() AS name_uppercase, surname.toLowerCase() AS surname_lowercase FROM profile';

        $this->assertCommandGives($sql, $this->select->getRaw());
    }

    public function testAllowsReferencingFieldsOfLinks()
    {
        $this->select
                ->select(array('city.country.name AS country'))
                ->from(array('address'), false);

        $sql = 'SELECT city.country.name AS country FROM address';

        $this->assertCommandGives($sql, $this->select->getRaw());
    }
}
