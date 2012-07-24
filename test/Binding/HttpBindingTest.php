<?php

/**
 * HttpBindingTest
 *
 * @package    Congow\Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     Daniele Alessandri <suppakilla@gmail.com>
 * @version
 */

namespace test\Binding;

use test\PHPUnit\TestCase;
use Congow\Orient\Binding\HttpBinding;
use Congow\Orient\Binding\BindingParameters;
use Congow\Orient\Binding\Adapter\CurlClientAdapter;
use Congow\Orient\Client\Http\CurlClient;

class HttpBindingTest extends TestCase
{
    public function testConnectToDatabase()
    {
        $binding = $this->createHttpBinding(array(
            'odb.username' => TEST_ODB_USER,
            'odb.password' => TEST_ODB_PASSWORD,
            'odb.database' => null,
        ));

        $this->assertHttpStatus(200, $binding->connect(TEST_ODB_DATABASE));
    }

    public function testConnectToDatabaseWithWrongCredentials()
    {
        $binding = $this->createHttpBinding(array(
            'odb.username' => 'invalid',
            'odb.password' => 'invalid',
        ));

        $this->assertHttpStatus(401, $binding->connect('INVALID_DB'));
    }

    public function testDisconnectFromTheServer()
    {
        $binding = $this->createHttpBinding();

        $response = $binding->disconnect()->getInnerResponse();
        $this->assertEquals('Logged out', $response->getBody());
    }

    public function testClassMethods()
    {
        $binding = $this->createHttpBinding();

        $this->assertHttpStatus(500, $binding->getClass('OMG'), 'Get a non existing class');
        $this->assertHttpStatus(201, $binding->postClass('OMG'), 'Create a class');
        $this->assertHttpStatus(204, $binding->deleteClass('OMG'), 'Delete a class');
    }

    public function testClusterMethod()
    {
        $binding = $this->createHttpBinding();

        $this->assertHttpStatus(200, $binding->cluster('Address'));
        $this->assertHttpStatus(200, $binding->cluster('Address', 1));

        $result = json_decode($binding->cluster('Address', 1)->getInnerResponse()->getBody(), true);
        $this->assertSame('Address', $result['schema']['name'], 'The cluster is wrong');

        $result = json_decode($binding->cluster('Country', 10)->getInnerResponse()->getBody(), true);
        $this->assertSame('Country', $result['schema']['name'], 'The cluster is wrong');
        $this->assertCount(10, $result['result'], 'The limit is wrong');
    }

    public function testServerMethod()
    {
        $binding = $this->createHttpBinding();

        $this->assertHttpStatus(200, $binding->getServer());
    }

    public function testDatabaseMethod()
    {
        $binding = $this->createHttpBinding();

        $this->assertHttpStatus(200, $binding->getDatabase(TEST_ODB_DATABASE), 'Get informations about an existing database');
        $this->assertHttpStatus(500, $binding->getDatabase('INVALID_DB'), 'Get informations about a non-existing database');
    }

    public function testCommandMethod()
    {
        $binding = $this->createHttpBinding();

        $this->assertHttpStatus(200, $binding->command('SELECT FROM Address'), 'Execute a simple select');
        $this->assertHttpStatus(200, $binding->command("SELECT FROM City WHERE name = 'Rome'"), 'Execute a select with WHERE condition');
        $this->assertHttpStatus(200, $binding->command('SELECT FROM City WHERE name = "Rome"'), 'Execute another select with WHERE condition');
        $this->assertHttpStatus(500, $binding->command('INVALID SQL'), 'Execute a wrong SQL command');
        # HTTPTODO: status code should be 400 or 404
    }

    public function testQueryMethod()
    {
        $binding = $this->createHttpBinding();

        $this->assertHttpStatus(200, $binding->query('SELECT FROM Address'), 'Executes a SELECT');
        $this->assertHttpStatus(200, $binding->query('SELECT FROM Address', null, 10), 'Executes a SELECT with LIMIT');
        $this->assertHttpStatus(500, $binding->query("UPDATE Profile SET online = false"), 'Tries to execute an UPDATE with the query command');
    }

    public function testSettingAuthentication()
    {
        $adapter = $this->getMock('Congow\Orient\Contract\Binding\Adapter\HttpClientAdapterInterface');
        $adapter->expects($this->at(1))
                ->method('setAuthentication')
                ->with(null, null);
        $adapter->expects($this->at(2))
                ->method('setAuthentication')
                ->with(TEST_ODB_USER, TEST_ODB_PASSWORD);

        $parameters = new BindingParameters();
        $binding = new HttpBinding($parameters, $adapter);

        $binding->setAuthentication();
        $binding->setAuthentication(TEST_ODB_USER, TEST_ODB_PASSWORD);
    }

    public function testInjectHttpClientAdapter()
    {
        $adapter = $this->getMock('Congow\Orient\Contract\Binding\Adapter\HttpClientAdapterInterface');

        $parameters = new BindingParameters();
        $binding = new HttpBinding($parameters, $adapter);

        $this->assertSame($adapter, $binding->getAdapter());
    }

    /**
     * @expectedException Congow\Orient\Exception
     */
    public function testResolveDatabase()
    {
        $adapter = $this->getMock('Congow\Orient\Contract\Binding\Adapter\HttpClientAdapterInterface');

        $parameters = new BindingParameters();
        $binding = new HttpBinding($parameters, $adapter);

        $binding->deleteClass('MyClass');
    }

    public function testDocumentMethods()
    {
        $binding = $this->createHttpBinding();

        $this->assertHttpStatus(500, $binding->getDocument('991'), 'Retrieves a document with an invalid RID');
        $this->assertHttpStatus(404, $binding->getDocument('9:0'), 'Retrieves a non existing document');
        $this->assertHttpStatus(500, $binding->getDocument('999:0'), 'Retrieves a document from a non existing cluster');
        $this->assertHttpStatus(200, $binding->getDocument('1:0'), 'Retrieves a valid document');

        $document = json_encode(array('@class' => 'Address', 'name' => 'Test'));

        $creation = $binding->postDocument($document);
        $this->assertHttpStatus(201, $creation, 'Creates a valid document');
        $rid = str_replace('#', '', $creation->getInnerResponse()->getBody());

        $document = json_encode(array('@rid' => $rid, '@class' => 'Address','name' => 'Test'));
        $this->assertHttpStatus(200, $binding->putDocument($rid, $document), 'Updates a valid document');

        $document = json_encode(array('@class' => 'Address', 'name' => 'Test', '@version' => 1));
        $this->assertHttpStatus(200, $binding->putDocument($rid, $document), 'Updates a valid document');

        $this->assertHttpStatus(500, $binding->putDocument('9991', $document), 'Updates an invalid document');

        /**
         * We must reset the OSESSIONID or we won't get a 409 Conflict response from OrientDB.
         * @see https://github.com/congow/Orient/commit/44dfff40e25251fc2b8941525e71d0464a1867ef#commitcomment-450144
         */
        $binding->getAdapter()->getClient()->restart();
        $this->assertHttpStatus(409, $binding->deleteDocument($rid, 3), 'Deletes a valid document');

        $this->assertHttpStatus(204, $binding->deleteDocument($rid, 2), 'Deletes a valid document');
        $this->assertHttpStatus(500, $binding->deleteDocument('999:1'), 'Deletes a non existing document');
        $this->assertHttpStatus(500, $binding->deleteDocument('9991'), 'Deletes an invalid document');
    }

    /**
     * @see https://github.com/Reinmar/Orient/commit/6110c61778cd7592f4c1e4f5530ea84e79c0f9cd
     */
    public function testFetchPlansAreProperlyEncoded()
    {
        $host = TEST_ODB_HOST;
        $port = TEST_ODB_PORT;

        $adapter = $this->getMock('Congow\Orient\Contract\Binding\Adapter\HttpClientAdapterInterface');
        $adapter->expects($this->once())
                ->method('request')
                ->with('GET', "http://$host:$port/query/DB/sql/SELECT%20OMNOMNOMN/2/%2A%3A1%20field1%3A3", null, null);

        $parameters = new BindingParameters();
        $binding = new HttpBinding($parameters, $adapter);

        $binding->query("SELECT OMNOMNOMN", 2, "*:1 field1:3", "DB");
    }

    public function testOptionalDatabaseArgumentDoesNotSwitchCurrentDatabase()
    {
        $host = TEST_ODB_HOST;
        $port = TEST_ODB_PORT;
        $database = TEST_ODB_DATABASE;

        $adapter = $this->getMock('Congow\Orient\Contract\Binding\Adapter\HttpClientAdapterInterface');
        $adapter->expects($this->at(0))
                ->method('request')
                ->with('POST', "http://$host:$port/command/$database/sql/SELECT%201", null, null);
        $adapter->expects($this->at(1))
                ->method('request')
                ->with('POST', "http://$host:$port/command/HIJACKED/sql/SELECT%202", null, null);
        $adapter->expects($this->at(2))
                ->method('request')
                ->with('POST', "http://$host:$port/command/$database/sql/SELECT%203", null, null);

        $parameters = new BindingParameters(TEST_ODB_HOST, TEST_ODB_PORT, TEST_ODB_USER, TEST_ODB_PASSWORD, TEST_ODB_DATABASE);
        $binding = new HttpBinding($parameters);
        $binding->setAdapter($adapter);

        $binding->command('SELECT 1');
        $binding->command('SELECT 2', "HIJACKED");
        $binding->command('SELECT 3');
    }
}
