<?php

/**
 * HttpBindingTest
 *
 * @package    Doctrine\OrientDB
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     Daniele Alessandri <suppakilla@gmail.com>
 * @version
 */

namespace test\Doctrine\OrientDB\Binding;

use test\PHPUnit\TestCase;
use Doctrine\OrientDB\Binding\HttpBinding;
use Doctrine\OrientDB\Binding\BindingParameters;
use Doctrine\OrientDB\Binding\Adapter\CurlClientAdapter;
use Doctrine\OrientDB\Client\Http\CurlClient;

/**
 * @group integration
 */
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

        $this->assertHttpStatus(500, $binding->cluster('Address'));
        $this->assertHttpStatus(200, $binding->cluster('Address', 1));

        $result = json_decode($binding->cluster('Address', 1)->getInnerResponse()->getBody(), true);
        $this->assertSame('Address', $result['result'][0]['@class'], 'The cluster is wrong');

        $result = json_decode($binding->cluster('Country', 10)->getInnerResponse()->getBody(), true);
        $this->assertSame('Country', $result['result'][0]['@class'], 'The cluster is wrong');
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
        $this->assertHttpStatus(401, $binding->getDatabase('INVALID_DB'), 'Get informations about a non-existing database');
    }

    public function testListDatabasesMethod()
    {
        $binding = $this->createHttpBinding();
        $this->assertHttpStatus(200, $response = $binding->listDatabases(), 'List existing databases');
        $this->assertInternalType('array', $response->getData()->databases);
    }

    public function testCreateDatabaseMethod()
    {
        $binding = $this->createHttpBinding();

        $this->assertHttpStatus(200, $binding->createDatabase(TEST_ODB_DATABASE . '_temporary'), 'Create a new database');
        $this->assertHttpStatus(500, $binding->createDatabase(TEST_ODB_DATABASE . '_temporary'), 'Create an already existing database');
    }

    /**
     * @depends testCreateDatabaseMethod
     */
    public function testDeleteDatabaseMethod()
    {
        $binding = $this->createHttpBinding();

        $this->assertHttpStatus(204, $binding->deleteDatabase(TEST_ODB_DATABASE . '_temporary'), 'Delete a existing database');
        $this->assertHttpStatus(500, $binding->deleteDatabase(TEST_ODB_DATABASE . '_temporary'), 'Delete a non-existing database');
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
        $adapter = $this->getMock('Doctrine\OrientDB\Binding\Adapter\HttpClientAdapterInterface');
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
        $adapter = $this->getMock('Doctrine\OrientDB\Binding\Adapter\HttpClientAdapterInterface');

        $parameters = new BindingParameters();
        $binding = new HttpBinding($parameters, $adapter);

        $this->assertSame($adapter, $binding->getAdapter());
    }

    /**
     * @expectedException Doctrine\OrientDB\Exception
     */
    public function testResolveDatabase()
    {
        $adapter = $this->getMock('Doctrine\OrientDB\Binding\Adapter\HttpClientAdapterInterface');

        $parameters = new BindingParameters();
        $binding = new HttpBinding($parameters, $adapter);

        $binding->deleteClass('MyClass');
    }

    public function testDocumentMethods()
    {
        $binding = $this->createHttpBinding();

        $this->assertHttpStatus(500, $binding->getDocument('991'), 'Retrieves a document with an invalid RID');
        $this->assertHttpStatus(404, $binding->getDocument('9:10000'), 'Retrieves a non existing document');
        $this->assertHttpStatus(500, $binding->getDocument('999:0'), 'Retrieves a document from a non existing cluster');
        $this->assertHttpStatus(200, $binding->getDocument('1:0'), 'Retrieves a valid document');
    }

    public function testCreateDocument()
    {
        $binding = $this->createHttpBinding();

        $document = json_encode(array('@class' => 'Address', 'name' => 'Pippo'));

        $creation = $binding->postDocument($document);

        $this->assertHttpStatus(201, $creation, 'Creates a valid document');
        $body = str_replace('#', '', $creation->getInnerResponse()->getBody());

        $decode = json_decode($body,true);

        return $decode['@rid'];
    }

    /**
     * @depends testCreateDocument
     */
    public function testUpdateAnExistingRecord($rid)
    {
        $binding = $this->createHttpBinding();

        $binding->getAdapter()->getClient()->restart();

        $_document = json_decode($binding->getDocument($rid)->getInnerResponse()->getBody(),true);
        $document = json_encode(array('@rid' => $rid, '@class' => 'Address','name' => 'Test','@version' => $_document['@version']));
        $putResult = $binding->putDocument($rid, $document);

        $this->assertEquals(200, $putResult->getInnerResponse()->getStatusCode(), "Wrong Status Code");
        $document = json_encode(array('@rid' => 898989, '@class' => 'Address','name' => 'Test','@version' => $_document['@version']));
        $this->assertHttpStatus(500, $binding->putDocument('9991', $document), 'Updates an invalid document');

        return $rid;
    }

    /**
     * @depends testUpdateAnExistingRecord
     */
    public function testDeleteADocument($rid)
    {
        $binding = $this->createHttpBinding();

        $binding->getAdapter()->getClient()->restart();

        $this->assertHttpStatus(204, $binding->deleteDocument($rid), 'Deletes a valid document');
        $this->assertHttpStatus(404, $binding->deleteDocument('999:1'), 'Deletes a non existing document');
        $this->assertHttpStatus(500, $binding->deleteDocument('9991'), 'Deletes an invalid document');

    }

    public function testGetDatabaseName()
    {
        $binding = $this->createHttpBinding();
        $this->assertEquals(TEST_ODB_DATABASE, $binding->getDatabaseName());
    }

    /**
     * @see https://github.com/Reinmar/Orient/commit/6110c61778cd7592f4c1e4f5530ea84e79c0f9cd
     */
    public function testFetchPlansAreProperlyEncoded()
    {
        $host = TEST_ODB_HOST;
        $port = TEST_ODB_PORT;
        $database = TEST_ODB_DATABASE;

        $adapter = $this->getMock('Doctrine\OrientDB\Binding\Adapter\HttpClientAdapterInterface');
        $adapter->expects($this->once())
                ->method('request')
                ->with('GET', "http://$host:$port/query/$database/sql/SELECT%20OMNOMNOMN/2/%2A%3A1%20field1%3A3", null, null);

        $parameters = new BindingParameters($host, $port, null, null, $database);
        $binding = new HttpBinding($parameters, $adapter);

        $binding->query("SELECT OMNOMNOMN", 2, "*:1 field1:3");
    }

    public function testOptionalDatabaseArgumentDoesNotSwitchCurrentDatabase()
    {
        $host = TEST_ODB_HOST;
        $port = TEST_ODB_PORT;
        $database = TEST_ODB_DATABASE;

        $adapter = $this->getMock('Doctrine\OrientDB\Binding\Adapter\HttpClientAdapterInterface');
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
        $binding->command('SELECT 2', HttpBinding::LANGUAGE_SQLPLUS, "HIJACKED");
        $binding->command('SELECT 3');
    }
}
