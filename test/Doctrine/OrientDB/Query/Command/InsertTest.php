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

use test\PHPUnit\TestCase;
use Doctrine\OrientDB\Query\Command\Insert;

class InsertTest extends TestCase
{
    public function setup()
    {
        $this->insert = new Insert();
    }

    public function testTheSchemaIsValid()
    {
        $tokens = array(
            ':Target'  => array(),
            ':Fields'  => array(),
            ':Values'  => array(),
            ':Returns' => array()
        );

        $this->assertTokens($tokens, $this->insert->getTokens());
    }

    public function testConstructionOfAnObject()
    {
        $query = 'INSERT INTO () VALUES () RETURN';

        $this->assertCommandGives($query, $this->insert->getRaw());
    }

    public function testInsertingFields()
    {
        $this->insert->fields(array('name'));
        $query = 'INSERT INTO (name) VALUES () RETURN';

        $this->assertCommandGives($query, $this->insert->getRaw());

        $this->insert->fields(array('name', 'username'), false);
        $query = 'INSERT INTO (name, username) VALUES () RETURN';

        $this->assertCommandGives($query, $this->insert->getRaw());

        $this->insert->fields(array('name'), true);
        $query = 'INSERT INTO (name, username, name) VALUES () RETURN';

        $this->assertCommandGives($query, $this->insert->getRaw());
    }

    public function testSettingTheToToken()
    {
        $this->insert->into("city");
        $query = 'INSERT INTO city () VALUES () RETURN';

        $this->assertCommandGives($query, $this->insert->getRaw());

        $this->insert->into('town', false);
        $query = 'INSERT INTO town () VALUES () RETURN';

        $this->assertCommandGives($query, $this->insert->getRaw());
    }

    public function testInsertValues()
    {
        $this->insert->values(array());
        $query = 'INSERT INTO () VALUES () RETURN';

        $this->assertCommandGives($query, $this->insert->getRaw());

        $this->insert->values(array('ciapa', 'ciapa2'), true);
        $query = 'INSERT INTO () VALUES ("ciapa", "ciapa2") RETURN';

        $this->assertCommandGives($query, $this->insert->getRaw());

        $this->insert->values(array('town'), false);
        $query = 'INSERT INTO () VALUES ("town") RETURN';

        $this->assertCommandGives($query, $this->insert->getRaw());
    }

    public function testUsingTheFluentInterface()
    {
        $this->insert
             ->into("myClass")
             ->fields(array('name', 'relation', 'links'))
             ->values(array('hello', array('10:1'), array('10:1', '11:1')))
             ->returns(Insert::RETURN_AFTER);

        $sql = 'INSERT INTO myClass (name, relation, links) VALUES ("hello", [10:1], [10:1, 11:1]) RETURN AFTER';

        $this->assertCommandGives($sql, $this->insert->getRaw());
    }
}
