<?php

/**
 * QueryTest
 *
 * @package    Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */
class QueryTest extends PHPUnit_Framework_TestCase
{
  public function initialize()
  {
    $this->query = new \Orient\Query();
  }

  public function testSelect()
  {
    $this->query = new \Orient\Query();
    $tokens = array(
        ':Projections' => array(),
        ':Target' => array(),
        ':Where' => array(),
        ':OrderBy' => array(),
        ':Limit' => array(),
        ':Range' => array(),
    );
    $this->assertEquals($tokens, $this->query->getTokens());

    $this->query = new \Orient\Query(array('myClass'));
    $query = 'SELECT FROM myClass';

    $this->assertEquals($query, $this->query->getRaw());

    $this->query->select(array('name'));
    $query = 'SELECT name FROM myClass';

    $this->assertEquals($query, $this->query->getRaw());

    $this->query->select(array('city'));
    $query = 'SELECT name, city FROM myClass';

    $this->assertEquals($query, $this->query->getRaw());

    $this->query->select(array('city'));
    $query = 'SELECT name, city FROM myClass';

    $this->assertEquals($query, $this->query->getRaw());

    $this->query->select(array('city'), false);
    $query = 'SELECT city FROM myClass';

    $this->assertEquals($query, $this->query->getRaw());

    $this->query->from(array('City'));
    $query = 'SELECT city FROM [myClass, City]';

    $this->assertEquals($query, $this->query->getRaw());

    $this->query->from(array('City'), false);
    $query = 'SELECT city FROM City';

    $this->assertEquals($query, $this->query->getRaw());

    $this->query->from(array('City'));
    $query = 'SELECT city FROM City';

    $this->assertEquals($query, $this->query->getRaw());

    $this->query->where("city = ?", 'Milan');
    $query = 'SELECT city FROM City WHERE city = "Milan"';

    $this->assertEquals($query, $this->query->getRaw());

    $this->query->where("city = ?", 'Turin');
    $query = 'SELECT city FROM City WHERE city = "Turin"';

    $this->assertEquals($query, $this->query->getRaw());

    $this->query->andWhere("city = ?", 'Bologna');
    $query = 'SELECT city FROM City WHERE city = "Turin" AND city = "Bologna"';

    $this->assertEquals($query, $this->query->getRaw());

    $this->query->orWhere("city = ?", 'Roma');
    $query = 'SELECT city FROM City WHERE city = "Turin" AND city = "Bologna" OR city = "Roma"';

    $this->assertEquals($query, $this->query->getRaw());

    $this->query->resetWhere();
    $query = 'SELECT city FROM City';

    $this->assertEquals($query, $this->query->getRaw());
  }
}
