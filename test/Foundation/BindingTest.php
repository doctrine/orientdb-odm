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
        $this->driver = new Curl(true, 5);
        $this->orient = new Binding($this->driver, '127.0.0.1', '2480', 'admin', 'admin');
    }

    public function testConnectionToADatabase()
    {
        $this->orient->setAuthentication('', '');
        $this->orient->setDatabase('demo');

        $this->assertStatusCode(self::_401, $this->orient->connect('ZOMG'));
        $this->orient->setAuthentication('admin', 'admin');
        $this->assertStatusCode(self::_200, $this->orient->connect('demo'));
    }

    public function testDisconnectionFromTheServer()
    {
        $this->assertEquals("Logged out", $this->orient->disconnect()->getBody());
    }

    public function testManagingAClass()
    {
        $this->orient->setDatabase('demo');
        $this->orient->setAuthentication('admin', 'admin');

        $this->assertStatusCode(self::_500, $this->orient->getClass('OMG'), 'get a non existing class');
        $this->assertStatusCode(self::_201, $this->orient->postClass('OMG'), 'create a class');
        $this->assertStatusCode(self::_204, $this->orient->deleteClass('OMG'), 'delete a class');
    }

    public function testManagingACluster()
    {
        $this->orient->setDatabase('demo');
        $this->orient->setAuthentication('admin', 'admin');

        $this->assertStatusCode(self::_200, $this->orient->cluster('Address'));
        $this->assertStatusCode(self::_200, $this->orient->cluster('Address', false, 1));
        $result = json_decode($this->orient->cluster('Address', false, 1)->getBody(), true);
        $this->assertEquals('Address', $result['schema']['name'], 'The cluster is wrong');
        $this->assertEquals(1, count($result['result']), 'The limi is wrong');

        $result = json_decode($this->orient->cluster('City', false, 10)->getBody(), true);
        $this->assertEquals('City', $result['schema']['name'], 'The cluster is wrong');
        $this->assertEquals(10, count($result['result']), 'The limit is wrong');
    }

    public function testExecutingACommand()
    {
        $this->orient->setDatabase('demo');
        $this->orient->setAuthentication('admin', 'admin');

        $this->assertStatusCode(self::_200, $this->orient->command('select from Address'), 'execute a simple select');
        $this->assertStatusCode(self::_200, $this->orient->command("select from City where name = 'Rome'"), 'execute a select with WHERE condition');
        $this->assertStatusCode(self::_200, $this->orient->command('select from City where name = "Rome"'), 'execute another select with WHERE condition');
        $this->assertStatusCode(self::_500, $this->orient->command("OMG OMG OMG"), 'execute a wrong SQL command');
        # HTTPTODO: status code should be 400 or 404
    }

    public function testManagingADatabase()
    {
        $this->orient->setAuthentication('admin', 'admin');
        
        $this->assertStatusCode(self::_200, $this->orient->getDatabase('demo'), 'get informations about an existing database');
        $this->assertStatusCode(self::_500, $this->orient->getDatabase("OMGOMGOMG"), 'get informations about a non-existing database');
    }

    public function testRetrievingInformationsFromAServer()
    {
        $this->orient->setDatabase('demo');
        $this->orient->setAuthentication('admin', 'admin');
        $this->assertStatusCode(self::_200, $this->orient->getServer());
    }

    public function testExecutingAQuery()
    {
        $this->orient->setDatabase('demo');
        $this->orient->setAuthentication('admin', 'admin');

        $this->orient->setDatabase('demo');
        $this->assertStatusCode(self::_200, $this->orient->query('select from Address'), 'executes a SELECT');
        $this->assertStatusCode(self::_200, $this->orient->query('select from Address', null, 10), 'executes a SELECT with LIMIT');
        $this->assertStatusCode(self::_500, $this->orient->query("update Profile set online = false"), 'tries to xecute an UPDATE with the quesry command');
    }

    public function testRetrievingAuthenticationCredentials()
    {
        $this->orient->setDatabase('demo');
        $this->orient->setAuthentication('admin', 'admin');

        $this->assertEquals($this->orient->getAuthentication(), 'admin:admin', 'gets the authentication credentials');
    }

    public function testSettingAuthentication()
    {
        $this->driver = new Curl();
        $this->orient = new Binding($this->driver, '127.0.0.1', '2480');
        $this->orient->setAuthentication();

        $this->assertEquals($this->orient->getAuthentication(), false, 'sets no authentication in the current request');

        $this->orient->setAuthentication('admin', 'admin');
        $this->assertEquals($this->orient->getAuthentication(), 'admin:admin', 'sets the credentials for the current request');
    }

    public function testInjectionOfAnHttpClient()
    {
        $client = $this->getMock("Congow\Orient\Http\Client\Curl");
        $this->orient = new Binding($client);

        $this->assertEquals($client, $this->orient->getHttpClient());
        $this->assertInstanceOf("Congow\Orient\Http\Client\Curl", $this->orient->getHttpClient());
    }

    /**
     * @expectedException \Exception
     */
    public function testResolvingTheDatabase()
    {
        $client = $this->getMock("Congow\Orient\Http\Client\Curl");
        $this->orient = new Binding($client);
        $this->orient->deleteClass('MyClass');
    }

    public function testManagingADocument()
    {
        $this->orient->setHttpClient(new Curl(false));
        $this->orient->setDatabase('demo');
        $this->orient->setAuthentication('admin', 'admin');

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
    * @todo: test not used
    * */
    public function testFetchplanIsHandledCorrectly()
    {
        $response = $this->orient->getDocument('1:1', 'demo', 'myFetchplan');
    }

    /**
     * @expectedException Congow\Orient\Exception\Http\Response\Void
     */
    public function testAnExceptionIsRaisedWhenExecutingOperationsWithNoHttpClient()
    {
        $this->driver->get('1.1.1.1');
    }
}
