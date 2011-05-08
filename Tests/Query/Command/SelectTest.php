<?php

/**
 * QueryTest
 *
 * @package    Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */
class SelectTest extends PHPUnit_Framework_TestCase
{
  public function testSchema()
  {
    $select  = new \Orient\Query\Command\Select();
    $queryClass   = get_class($select);
    $tokens       = array(
        ':Projections'  => array(),
        ':Target'       => array(),
        ':Where'        => array(),
        ':OrderBy'      => array(),
        ':Limit'        => array(),
        ':Range'        => array(),
    );

    $this->assertEquals(
      $tokens,
      $select->getTokens(),
      'A SELECT should have the following schema: ' . $queryClass::SCHEMA
    );
  }

  public function testConstruct()
  {
    $select = new \Orient\Query\Command\Select();
    $query = 'SELECT FROM';

    $this->assertEquals($query, $select->getRaw());

    $select = new \Orient\Query\Command\Select(array('myClass'));
    $query = 'SELECT FROM myClass';

    $this->assertEquals($query, $select->getRaw());
  }

  public function testOrderBy()
  {
    $select = new \Orient\Query\Command\Select(array('myClass'));
    $select->orderBy("name ASC");
    $query = 'SELECT FROM myClass ORDER BY name ASC';

    $this->assertEquals($query, $select->getRaw());

    $select->orderBy("name ASC");
    $select->orderBy("surname DESC");
    $query = 'SELECT FROM myClass ORDER BY name ASC, surname DESC';

    $this->assertEquals($query, $select->getRaw());

    $select->orderBy("id", false);
    $query = 'SELECT FROM myClass ORDER BY id';

    $this->assertEquals($query, $select->getRaw());

    $select->orderBy("name", true, true);
    $query = 'SELECT FROM myClass ORDER BY name, id';

    $this->assertEquals($query, $select->getRaw());
  }

  public function testLimit()
  {
    $select = new \Orient\Query\Command\Select(array('myClass'));
    $select->limit(10);
    $query = 'SELECT FROM myClass LIMIT 10';

    $this->assertEquals($query, $select->getRaw());

    $select->limit(20);
    $query = 'SELECT FROM myClass LIMIT 20';

    $this->assertEquals($query, $select->getRaw());
  }

  public function testRange()
  {
    $select = new \Orient\Query\Command\Select(array('myClass'));
    $select->limit(10);
    $select->range('10:3');
    $query = 'SELECT FROM myClass LIMIT 10 RANGE 10:3';

    $this->assertEquals($query, $select->getRaw());

    $select->range(null, '10:4');
    $query = 'SELECT FROM myClass LIMIT 10 RANGE 10:3, 10:4';

    $this->assertEquals($query, $select->getRaw());

    $select->range('10:5', '10:6');
    $query = 'SELECT FROM myClass LIMIT 10 RANGE 10:5, 10:6';

    $this->assertEquals($query, $select->getRaw());

    $select->range('10:1');
    $query = 'SELECT FROM myClass LIMIT 10 RANGE 10:1, 10:6';

    $this->assertEquals($query, $select->getRaw());

    $select->range('10:1', false);
    $query = 'SELECT FROM myClass LIMIT 10 RANGE 10:1';

    $this->assertEquals($query, $select->getRaw());

    $select->range(false, false);
    $query = 'SELECT FROM myClass LIMIT 10';

    $this->assertEquals($query, $select->getRaw());
  }

  public function testComplexSelect()
  {
    $select = new \Orient\Query\Command\Select(array('myClass'));
    $select->limit(10);
    $select->limit(20);
    $select->from(array('23:2', '12:4'), false);
    $select->select(array('id', 'name'));
    $select->select(array('name'));
    $select->range('10:3');
    $select->range(null, '12:0');

    $query = 'SELECT id, name FROM [23:2, 12:4] LIMIT 20 RANGE 10:3, 12:0';

    $this->assertEquals($query, $select->getRaw());
  }
}

