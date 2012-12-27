<?php

/**
 * QueryTest
 *
 * @package    Doctrine\OrientDB
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace test\Doctrine\OrientDB\Query\Command\Credential;

use Doctrine\OrientDB\Query\Command\Credential\Grant;
use test\PHPUnit\TestCase;

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
        $this->grant
                ->permission("read")
                ->to("myUser")
                ->to("myOtherUser")
                ->on("server");

        $sql = 'GRANT read ON server TO myOtherUser';

        $this->assertCommandGives($sql, $this->grant->getRaw());
    }

    public function testOnCommandWorksAndCanBeOverwritten()
    {
        $this->grant->on('resource');
        $query = 'GRANT myPermission ON resource TO';

        $this->assertCommandGives($query, $this->grant->getRaw());

        $this->grant->on('resource2');
        $query = 'GRANT myPermission ON resource2 TO';

        $this->assertCommandGives($query, $this->grant->getRaw());
    }

    public function testToCommandWorksAndCanBeOverwritten()
    {
        $this->grant->to('user');
        $query = 'GRANT myPermission ON TO user';

        $this->assertCommandGives($query, $this->grant->getRaw());

        $this->grant->to('user2');
        $query = 'GRANT myPermission ON TO user2';

        $this->assertCommandGives($query, $this->grant->getRaw());
    }
}
