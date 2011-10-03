<?php

/**
 * QueryTest
 *
 * @package    Congow\Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 * @version
 */

namespace test\ODM;

use test\PHPUnit\TestCase;
use Congow\Orient\ODM\Manager;

class TestMapper extends \Congow\Orient\ODM\Mapper
{
    public function __construct()
    {

    }
}

class TestAdapter extends \Congow\Orient\Foundation\Protocol\Adapter\Http
{
    public function __construct()
    {

    }
}

class ManagerTest extends TestCase
{
    public function setup()
    {
        $this->manager = new Manager(new TestMapper(), new TestAdapter());
    }
    
    public function testMethodUsedToTryTheManager()
    {
        $this->manager->getClassMetadata("test\ODM\Document\Stub\Contact\Address");
    }
}