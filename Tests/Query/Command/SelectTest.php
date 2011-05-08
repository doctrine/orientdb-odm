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
    $this->query  = new \Orient\Query\Command\Select();
    $queryClass   = get_class($this->query);
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
      $this->query->getTokens(),
      'A SELECT should have the following schema: ' . $queryClass::SCHEMA
    );
  }

  public function testConstruct()
  {
    $this->query = new \Orient\Query\Command\Select();
    $query = 'SELECT FROM';

    $this->assertEquals($query, $this->query->getRaw());

    $this->query = new \Orient\Query\Command\Select(array('myClass'));
    $query = 'SELECT FROM myClass';

    $this->assertEquals($query, $this->query->getRaw());
  }
}

