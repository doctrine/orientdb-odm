<?php

/**
 * BindingTest
 *
 * @package    Congow\Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace test\Foundation;

use test\PHPUnit\TestCase;
use Congow\Orient\Http\Client\Curl;
use Congow\Orient\Foundation\Binding;

class FakeCurl extends Curl
{
    public function get($location)
    {
        return $location;
    }
}

class BindingTest extends TestCase
{
    const _200 = '200';
    const _201 = '201';
    const _204 = '204';
    const _401 = '401';
    const _404 = '404';
    const _409 = '409';
    const _500 = '500';

    public function setup()
    {
        $this->driver = new Curl(false, 10);
        $this->orient = new Binding($this->driver, TEST_ODB_HOST, TEST_ODB_PORT, TEST_ODB_USER, TEST_ODB_PASSWORD);
    }

    public function testConnectionToADatabase()
    {
        $this->orient->setAuthentication('', '');
        $this->orient->setDatabase(TEST_ODB_DATABASE);

        $this->assertStatusCode(self::_401, $this->orient->connect('ZOMG'));
        $this->orient->setAuthentication(TEST_ODB_USER, TEST_ODB_PASSWORD);
        $this->assertStatusCode(self::_200, $this->orient->connect(TEST_ODB_DATABASE));
    }

    public function testDisconnectionFromTheServer()
    {
        $this->assertEquals("Logged out", $this->orient->disconnect()->getBody());
    }

    public function testManagingAClass()
    {
        $this->orient->setDatabase(TEST_ODB_DATABASE);
        $this->orient->setAuthentication(TEST_ODB_USER, TEST_ODB_PASSWORD);

        $this->assertStatusCode(self::_500, $this->orient->getClass('OMG'), 'get a non existing class');
        $this->assertStatusCode(self::_201, $this->orient->postClass('OMG'), 'create a class');
        $this->assertStatusCode(self::_204, $this->orient->deleteClass('OMG'), 'delete a class');
    }

    public function testManagingACluster()
    {
        $this->orient->setDatabase(TEST_ODB_DATABASE);
        $this->orient->setAuthentication(TEST_ODB_USER, TEST_ODB_PASSWORD);

        $this->assertStatusCode(self::_200, $this->orient->cluster('Address'));
        $this->assertStatusCode(self::_200, $this->orient->cluster('Address', false, 1));
        $result = json_decode($this->orient->cluster('Address', false, 1)->getBody(), true);
        $this->assertEquals('Address', $result['schema']['name'], 'The cluster is wrong');
        $this->assertEquals(1, count($result['result']), 'The limi is wrong');

        $result = json_decode($this->orient->cluster('Country', false, 10)->getBody(), true);
        $this->assertEquals('Country', $result['schema']['name'], 'The cluster is wrong');
        $this->assertEquals(10, count($result['result']), 'The limit is wrong');
    }

    public function testExecutingACommand()
    {
        $this->orient->setDatabase(TEST_ODB_DATABASE);
        $this->orient->setAuthentication(TEST_ODB_USER, TEST_ODB_PASSWORD);

        $this->assertStatusCode(self::_200, $this->orient->command('select from Address'), 'execute a simple select');
        $this->assertStatusCode(self::_200, $this->orient->command("select from City where name = 'Rome'"), 'execute a select with WHERE condition');
        $this->assertStatusCode(self::_200, $this->orient->command('select from City where name = "Rome"'), 'execute another select with WHERE condition');
        $this->assertStatusCode(self::_500, $this->orient->command("OMG OMG OMG"), 'execute a wrong SQL command');
        # HTTPTODO: status code should be 400 or 404
    }

    public function testManagingADatabase()
    {
        $this->orient->setAuthentication(TEST_ODB_USER, TEST_ODB_PASSWORD);
        
        $this->assertStatusCode(self::_200, $this->orient->getDatabase(TEST_ODB_DATABASE), 'get informations about an existing database');
        $this->assertStatusCode(self::_500, $this->orient->getDatabase("OMGOMGOMG"), 'get informations about a non-existing database');
    }

    public function testRetrievingInformationsFromAServer()
    {
        $this->orient->setDatabase(TEST_ODB_DATABASE);
        $this->orient->setAuthentication(TEST_ODB_USER, TEST_ODB_PASSWORD);
        $this->assertStatusCode(self::_200, $this->orient->getServer());
    }

    public function testExecutingAQuery()
    {
        $this->orient->setDatabase(TEST_ODB_DATABASE);
        $this->orient->setAuthentication(TEST_ODB_USER, TEST_ODB_PASSWORD);

        $this->orient->setDatabase(TEST_ODB_DATABASE);
        $this->assertStatusCode(self::_200, $this->orient->query('select from Address'), 'executes a SELECT');
        $this->assertStatusCode(self::_200, $this->orient->query('select from Address', null, 10), 'executes a SELECT with LIMIT');
        $this->assertStatusCode(self::_500, $this->orient->query("update Profile set online = false"), 'tries to execute an UPDATE with the query command');
    }

    public function testRetrievingAuthenticationCredentials()
    {
        $user = TEST_ODB_USER;
        $password = TEST_ODB_PASSWORD;

        $this->orient->setDatabase(TEST_ODB_DATABASE);
        $this->orient->setAuthentication($user, $password);

        $this->assertEquals($this->orient->getAuthentication(), "$user:$password", 'gets the authentication credentials');
    }

    public function testSettingAuthentication()
    {
        $this->driver = new Curl();
        $this->orient = new Binding($this->driver, TEST_ODB_HOST, TEST_ODB_PORT);
        $this->orient->setAuthentication();

        $this->assertEquals($this->orient->getAuthentication(), false, 'sets no authentication in the current request');

        $user = TEST_ODB_USER;
        $password = TEST_ODB_PASSWORD;

        $this->orient->setAuthentication($user, $password);
        $this->assertEquals($this->orient->getAuthentication(), "$user:$password", 'sets the credentials for the current request');
    }

    public function testInjectionOfAnHttpClient()
    {
        $client = $this->getMock("Congow\Orient\Http\Client\Curl");
        $this->orient = new Binding($client);

        $this->assertEquals($client, $this->orient->getHttpClient());
        $this->assertInstanceOf("Congow\Orient\Http\Client\Curl", $this->orient->getHttpClient());
    }

    /**
     * @expectedException Congow\Orient\Exception
     */
    public function testResolvingTheDatabase()
    {
        $client = $this->getMock("Congow\Orient\Http\Client\Curl");
        $this->orient = new Binding($client);
        $this->orient->deleteClass('MyClass');
    }

    public function testManagingADocument()
    {
        $this->orient->setHttpClient(new Curl(true));
        $this->orient->setDatabase(TEST_ODB_DATABASE);
        $this->orient->setAuthentication(TEST_ODB_USER, TEST_ODB_PASSWORD);

        $this->assertStatusCode(self::_500, $this->orient->getDocument('991'), 'retrieves a document with an invalid RID');
        $this->assertStatusCode(self::_404, $this->orient->getDocument('9:0'), 'retrieves a non existing document');
        $this->assertStatusCode(self::_500, $this->orient->getDocument('999:0'), 'retrieves a document from a non existing cluster');
        $this->assertStatusCode(self::_200, $this->orient->getDocument('1:0'), 'retrieves a valid document');

        $document = json_encode(array('@class' => 'Address', 'name' => 'Test'));

        $createDocument = $this->orient->postDocument($document);
        $rid = str_replace('#', '', $createDocument->getBody());
        $this->assertStatusCode(self::_201, $createDocument, 'creates a valid document');
        $document = json_encode(array('@rid' => $rid, '@class' => 'Address','name' => 'Test'));
        $this->assertStatusCode(self::_200, $this->orient->putDocument($rid, $document), 'updates a valid document');
        $document = json_encode(array('@class' => 'Address', 'name' => 'Test', '@version' => 1));
        $this->assertStatusCode(self::_200, $this->orient->putDocument($rid, $document), 'updates a valid document');
        $this->assertStatusCode(self::_500, $this->orient->putDocument('9991', $document), 'updates a non valid document');
        
        /**
         * disable reusing curl handle, otherwise we won't get 409 Conflict
         * @see https://github.com/congow/Orient/commit/44dfff40e25251fc2b8941525e71d0464a1867ef#commitcomment-450144
         */ 
        $this->assertStatusCode(self::_409, $this->orient->deleteDocument($rid, 3), 'deletes a valid document');
        $this->assertStatusCode(self::_204, $this->orient->deleteDocument($rid, 2), 'deletes a valid document');
        $this->assertStatusCode(self::_500, $this->orient->deleteDocument('999:1'), 'deletes a non existing document');
        $this->assertStatusCode(self::_500, $this->orient->deleteDocument('9991'), 'deletes a non valid document');
    }

    public function testSettingHttpClient()
    {
        $client = new Curl();

        $this->assertFalse($client === $this->orient->getHttpClient());

        $this->orient->setHttpClient($client);

        $this->assertTrue($client === $this->orient->getHttpClient());
    }

    /**
     * @expectedException Congow\Orient\Exception\Http\Response\Void
     */
    public function testAnExceptionIsRaisedWhenExecutingOperationsWithNoHttpClient()
    {
        $driver = new Curl(false, 1);
        $driver->get('1.1.1.1');
    }

    /**
     * @see https://github.com/Reinmar/Orient/commit/6110c61778cd7592f4c1e4f5530ea84e79c0f9cd
     */
    public function testYouCanSpecifyMultipleFetchPlansAndTheyGetEncodedProperlyInTheUrl()
    {
        $this->orient->setHttpClient(new FakeCurl());
        $sqlSent = $this->orient->query("SELECT OMNOMNOMN", "DB", 2, "*:1 field1:3");

        $host = TEST_ODB_HOST;
        $port = TEST_ODB_PORT;

        $this->assertEquals("$host:$port/query/DB/sql/SELECT+OMNOMNOMN/2/%2A%3A1+field1%3A3", $sqlSent);
    }
}
