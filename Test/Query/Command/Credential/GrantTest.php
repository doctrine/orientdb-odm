<?php

/**
 * QueryTest
 *
 * @package    Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace Orient\Test\Query\Command\Credential;

use Orient\Query\Command\Credential\Grant;
use Orient\Test\PHPUnit\TestCase;

class GrantTest extends TestCase
{
    public function setup()
    {
        $this->grant = new Grant('myPermission');
    }

    public function testSchema()
    {
        $tokens = array(
            ':Permission' => array(),
            ':Resource' => array(),
            ':Role' => array(),
        );

        $this->assertTokens($tokens, $this->grant->getTokens());
    }

    public function testSynthaxIsRightAfterConstruction()
    {
        $query = 'GRANT myPermission ON TO';

        $this->assertCommandGives($query, $this->grant->getRaw());
    }

    public function testGrantCommandWorksAndCanBeOverWritten()
    {
        $query = 'GRANT myPermission ON TO';

        $this->assertCommandGives($query, $this->grant->getRaw());

        $this->grant->permission('READ');
        $query = 'GRANT READ ON TO';

        $this->assertCommandGives($query, $this->grant->getRaw());
    }

    public function testUsingTheFluentInterface()
    {
        $this->grant->permission("read")
                ->to("myUser")
                ->to("myOtherUser")
                ->on("server");
        $sql =
                'GRANT read ON server TO myOtherUser'
        ;

        $this->assertCommandGives($sql, $this->grant->getRaw());
    }
}
