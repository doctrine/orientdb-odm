<?php

/**
 * QueryTest
 *
 * @package    Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace Orient\Test\Query\Command;

class Insert extends PHPUnit_Framework_TestCase
{
  public function testSchema()
  {
    $select  = new \Orient\Query\Command\Insert();
    $queryClass   = get_class($select);
    $tokens       = array(
        ':Target'   => array(),
        ':Fields'   => array(),
        ':Values'   => array(),
    );

    $this->assertEquals(
      $tokens,
      $select->getTokens(),
      'An INSERT should have the following schema: ' . $queryClass::SCHEMA
    );
  }

  public function testConstruct()
  {
    $insert = new \Orient\Query\Command\Insert();
    $query = 'INSERT INTO () VALUES ()';

    $this->assertEquals($query, $insert->getRaw());
  }

  public function testFields()
  {
    $insert = new \Orient\Query\Command\Insert();
    $insert->fields(array('name'));
    $query = 'INSERT INTO (name) VALUES ()';

    $this->assertEquals($query, $insert->getRaw());

    $insert->fields(array('name', 'username'), true);
    $query = 'INSERT INTO (name, username) VALUES ()';

    $this->assertEquals($query, $insert->getRaw());

    $insert->fields(array('name'), false);
    $query = 'INSERT INTO (name) VALUES ()';

    $this->assertEquals($query, $insert->getRaw());
  }

  public function testInto()
  {
    $insert = new \Orient\Query\Command\Insert();
    $insert->into("city");
    $query = 'INSERT INTO city () VALUES ()';

    $this->assertEquals($query, $insert->getRaw());

    $insert->into(array('name', 'username'), true);
    $query = 'INSERT INTO name () VALUES ()';

    $this->assertEquals($query, $insert->getRaw());

    $insert->into('town', false);
    $query = 'INSERT INTO town () VALUES ()';

    $this->assertEquals($query, $insert->getRaw());
  }

  public function testValues()
  {
    $insert = new \Orient\Query\Command\Insert();
    $insert->values(array());
    $query = 'INSERT INTO () VALUES ()';

    $this->assertEquals($query, $insert->getRaw());

    $insert->values(array('ciapa', 'ciapa2'), true);
    $query = 'INSERT INTO () VALUES ("ciapa", "ciapa2")';

    $this->assertEquals($query, $insert->getRaw());

    $insert->values(array('town'), false);
    $query = 'INSERT INTO () VALUES ("town")';

    $this->assertEquals($query, $insert->getRaw());
  }
}

