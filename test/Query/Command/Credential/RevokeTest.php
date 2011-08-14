<?php

/**
 * RevokeTest
 *
 * @package    Congow\Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace test\Query\Command\Credential;

use Congow\Orient\Query\Command\Credential\Revoke;
use test\PHPUnit\TestCase;

class RevokeTest extends TestCase
{
    public function setUp()
    {
        $this->revoke = new Revoke('myPermission');
    }

    public function testRevokeHasSomeKnownTokens()
    {
        $tokens = array(
            ':Permission' => array(),
            ':Resource' => array(),
            ':Role' => array(),
        );

        $this->assertTokens($tokens, $this->revoke->getTokens());
    }

    public function testSynthaxIsRightAfterObjectCreation()
    {
        $query = 'REVOKE myPermission ON FROM';

        $this->assertCommandGives($query, $this->revoke->getRaw());
    }

    public function testRevokeCommandWorksAndCanBeOverwritten()
    {
        $query = 'REVOKE myPermission ON FROM';

        $this->assertCommandGives($query, $this->revoke->getRaw());

        $this->revoke->permission('READ');
        $query = 'REVOKE READ ON FROM';

        $this->assertCommandGives($query, $this->revoke->getRaw());
    }

    public function testUsingTheFluentInterface()
    {
        $this->revoke->permission("read")
                ->to("myUser")
                ->to("myOtherUser")
                ->on("server");
        $sql =
                'REVOKE read ON server FROM myOtherUser'
        ;

        $this->assertEquals($sql, $this->revoke->getRaw());
    }
}
