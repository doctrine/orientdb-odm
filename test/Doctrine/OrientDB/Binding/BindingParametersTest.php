<?php

/**
 * BindingParametersTest
 *
 * @package    Doctrine\OrientDB
 * @subpackage Test
 * @author     Daniele Alessandri <suppakilla@gmail.com>
 * @version
 */

namespace test\Doctrine\OrientDB\Binding;

use test\PHPUnit\TestCase;
use Doctrine\OrientDB\Binding\BindingParameters;

class BindingParametersTest extends TestCase
{
    public function testDefaultParameters()
    {
        $parameters = new BindingParameters();

        $this->assertSame('127.0.0.1', $parameters->getHost());
        $this->assertSame(2480, $parameters->getPort());
        $this->assertNull($parameters->getUsername());
        $this->assertNull($parameters->getPassword());
        $this->assertNull($parameters->getDatabase());
    }

    public function testCustomParameters()
    {
        $parameters = new BindingParameters(TEST_ODB_HOST, TEST_ODB_PORT, TEST_ODB_USER, TEST_ODB_PASSWORD, TEST_ODB_DATABASE);

        $this->assertSame(TEST_ODB_HOST, $parameters->getHost());
        $this->assertSame(TEST_ODB_PORT, $parameters->getPort());
        $this->assertSame(TEST_ODB_USER, $parameters->getUsername());
        $this->assertSame(TEST_ODB_PASSWORD, $parameters->getPassword());
        $this->assertSame(TEST_ODB_DATABASE, $parameters->getDatabase());
    }

    public function testParametersFromEmptyURIString()
    {
        $parameters = BindingParameters::fromString('');

        $this->assertSame('127.0.0.1', $parameters->getHost());
        $this->assertSame(2480, $parameters->getPort());
        $this->assertNull($parameters->getUsername());
        $this->assertNull($parameters->getPassword());
        $this->assertNull($parameters->getDatabase());
    }

    public function testParametersFromMinimalURIString()
    {
        $parameters = BindingParameters::fromString('http://10.0.0.1:6000');

        $this->assertSame('10.0.0.1', $parameters->getHost());
        $this->assertSame(6000, $parameters->getPort());
        $this->assertNull($parameters->getUsername());
        $this->assertNull($parameters->getPassword());
        $this->assertNull($parameters->getDatabase());
    }

    public function testParametersFromURIStringWithDatabase()
    {
        $parameters = BindingParameters::fromString('http://10.0.0.1:6000/dbase');

        $this->assertSame('10.0.0.1', $parameters->getHost());
        $this->assertSame(6000, $parameters->getPort());
        $this->assertNull($parameters->getUsername());
        $this->assertNull($parameters->getPassword());
        $this->assertSame('dbase', $parameters->getDatabase());
    }

    public function testParametersFromURIStringWithAuthentication()
    {
        $parameters = BindingParameters::fromString('http://foo:bar@10.0.0.1:6000');

        $this->assertSame('10.0.0.1', $parameters->getHost());
        $this->assertSame(6000, $parameters->getPort());
        $this->assertSame('foo', $parameters->getUsername());
        $this->assertSame('bar', $parameters->getPassword());
        $this->assertNull($parameters->getDatabase());
    }

    public function testParametersFromCompleteURIString()
    {
        $parameters = BindingParameters::fromString('http://foo:bar@10.0.0.1:6000/dbase');

        $this->assertSame('10.0.0.1', $parameters->getHost());
        $this->assertSame(6000, $parameters->getPort());
        $this->assertSame('foo', $parameters->getUsername());
        $this->assertSame('bar', $parameters->getPassword());
        $this->assertSame('dbase', $parameters->getDatabase());
    }


    public function testParametersFromEmptyArray()
    {
        $parameters = BindingParameters::fromArray(array());

        $this->assertSame('127.0.0.1', $parameters->getHost());
        $this->assertSame(2480, $parameters->getPort());
        $this->assertNull($parameters->getUsername());
        $this->assertNull($parameters->getPassword());
        $this->assertNull($parameters->getDatabase());
    }

    public function testParametersFromMinimalArray()
    {
        $config = array(
            'host' => '10.0.0.1',
            'port' => 6000,
        );

        $parameters = BindingParameters::fromArray($config);

        $this->assertSame('10.0.0.1', $parameters->getHost());
        $this->assertSame(6000, $parameters->getPort());
        $this->assertNull($parameters->getUsername());
        $this->assertNull($parameters->getPassword());
        $this->assertNull($parameters->getDatabase());
    }

    public function testParametersFromCompleteArray()
    {
        $config = array(
            'host' => TEST_ODB_HOST,
            'port' => TEST_ODB_PORT,
            'username' => TEST_ODB_USER,
            'password' => TEST_ODB_PASSWORD,
            'database' => TEST_ODB_DATABASE,
        );

        $parameters = BindingParameters::fromArray($config);

        $this->assertSame(TEST_ODB_HOST, $parameters->getHost());
        $this->assertSame(TEST_ODB_PORT, $parameters->getPort());
        $this->assertSame(TEST_ODB_USER, $parameters->getUsername());
        $this->assertSame(TEST_ODB_PASSWORD, $parameters->getPassword());
        $this->assertSame(TEST_ODB_DATABASE, $parameters->getDatabase());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid parameters type
     */
    public function testParametersCreateAcceptsOnlyStringsOrArrays()
    {
        $this->assertInstanceOf('Doctrine\OrientDB\Binding\BindingParameters', BindingParameters::create(array()));
        $this->assertInstanceOf('Doctrine\OrientDB\Binding\BindingParameters', BindingParameters::create(''));

        BindingParameters::create((object) array());
    }
}
