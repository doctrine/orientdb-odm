<?php

/**
 * QueryTest
 *
 * @package    Doctrine\OrientDB
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace test\Doctrine\OrientDB\Query;

use Doctrine\OrientDB\Query\Query;
use Doctrine\OrientDB\Query\Command as QueryCommand;
use test\PHPUnit\TestCase;
use Doctrine\OrientDB\Query\Formatter\Query as Formatter;

class StubCommand extends QueryCommand
{
    protected function getSchema()
    {
        return ":Target :Where";
    }
}

class StubExceptionedCommand extends QueryCommand
{
    protected function getSchema()
    {
        return ":NotFoundToken";
    }
}

class NullCommand extends QueryCommand
{
}

class Command extends TestCase
{
    public function setup()
    {
        $this->command = new StubCommand();
    }

    public function testSchema()
    {
        $command = new NullCommand;
        $this->assertEmpty($command->getRaw());
    }

    /**
     * @expectedException Doctrine\OrientDB\Exception
     */
    public function testAnExceptionIsRaisedIfYouDontExplicitHowToFormatAToken()
    {
        $this->command = new StubExceptionedCommand();
        $this->command->getRaw();
    }

    /**
     * @expectedException Doctrine\OrientDB\LogicException
     */
    public function testAnExceptionIsRaisedIfYouMakeAWhereWithDifferentParamsAndValues()
    {
        $this->command = new StubCommand();
        $this->command->where('c = ? AND b = ?', array(1));
        $this->command->getRaw();
    }

    public function testYouCanInjectACustomQueryFormatter()
    {
        $this->command  = new StubExceptionedCommand();
        $formatter      = new Formatter();
        $this->command->setFormatter($formatter);
    }

    public function testAddingFromToken()
    {
        $from = array('Cities');
        $this->command->from($from);

        $this->assertCommandGives($from, $this->command->getTokenValue('Target'));
    }

    public function testRetrieveTheRawCommand()
    {
        $from = array('Cities');
        $this->command->from($from);

        $this->assertCommandGives("Cities", $this->command->getRaw());
    }

    public function testTheCommandTokensAreValid()
    {
        $command = new StubCommand();
        $this->assertTokens(array(':Target' => array(), ':Where' => array()), $command->getTokens());
    }

    public function testYouCanResetAllTheWheresOfACommand()
    {
        $from = array('Cities');
        $this->command->where("i loves ?", "U");
        $this->command->resetWhere();

        $this->assertCommandGives(array(), $this->command->getTokenValue('Where'));
    }

    public function testAddAWhere()
    {
        $this->command->where("i loves ?", "U");
        $this->command->where("mark loves ?", "me", true, "OR");

        $this->assertCommandGives("WHERE i loves \"U\" OR mark loves \"me\"", $this->command->getRaw());
    }

    public function testYouCanSpecifyMultipleValuesInAWhere()
    {
        $this->command->where("i loves ? AND you love ?", array("U", 'me'));

        $this->assertCommandGives("WHERE i loves \"U\" AND you love \"me\"", $this->command->getRaw());
    }

    public function testTheWhereWorksCorrectlyWithReUsedPrivateNamesLikeANDOrORWHERE()
    {
        $this->command->where("i loves ?", "ME, AND YOU");
        $statement = 'WHERE i loves "ME, AND YOU"';

        $this->assertCommandGives($statement, $this->command->getRaw());

        $this->command->where("i loves ?", "ME, OR YOU");
        $statement = 'WHERE i loves "ME, OR YOU"';

        $this->assertCommandGives($statement, $this->command->getRaw());
    }

    /**
     * @expectedException Doctrine\OrientDB\Query\TokenNotFoundException
     */
    public function testCheckAnExceptionRaisedWhenRequestingInvalidToken()
    {
        $command = new StubCommand();
        $command->getTokenValue('buffalo');
    }
}
