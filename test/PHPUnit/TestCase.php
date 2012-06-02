<?php

/**
 * TestCase class bound to Congow\Orient.
 *
 * @author Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */

namespace test\PHPUnit;

use Congow\Orient\ODM\Manager;
use Congow\Orient\ODM\Mapper;
use Congow\Orient\Binding\HttpBinding;
use Congow\Orient\Binding\BindingParameters;
use Congow\Orient\Contract\Binding\HttpBindingResultInterface;
use Congow\Orient\Client\Http\CurlClient;
use Congow\Orient\Binding\Adapter\CurlClientAdapter;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    protected function getBindingParameters($options)
    {
        $parameters = array();

        array_walk($options, function($value, $key) use (&$parameters) {
            if (0 === $pos = strpos($key, 'odb.')) {
                $parameters[substr($key, strpos($key, '.') + 1)] = $value;
            }
        });

        return BindingParameters::fromArray($parameters);
    }

    protected function createHttpBinding(Array $opts = array())
    {
        $opts = array_merge(array(
            'http.adapter' => null,
            'http.restart' => false,
            'http.timeout' => TEST_ODB_TIMEOUT,
            'odb.host' => TEST_ODB_HOST,
            'odb.port' => TEST_ODB_PORT,
            'odb.username' => TEST_ODB_USER,
            'odb.password' => TEST_ODB_PASSWORD,
            'odb.database' => TEST_ODB_DATABASE,
        ), $opts);

        if (!isset($opts['adapter'])) {
            $client = new CurlClient($opts['http.restart'], $opts['http.timeout']);
            $opts['adapter'] = new CurlClientAdapter($client);
        }

        $parameters = $this->getBindingParameters($opts);
        $binding = new HttpBinding($parameters, $opts['adapter']);

        return $binding;
    }

    protected function createManager(Array $opts = array())
    {
        $opts = array_merge(array(
            'mismatches_tolerance' => false,
            'proxies_dir' => __DIR__ . '/../../proxies',
            'document_dir' => array('./test/Integration/Document' => 'test'),
        ), $opts);

        $mapper = new Mapper($opts['proxies_dir']);
        $mapper->setDocumentDirectories($opts['document_dir']);

        if ($opts['mismatches_tolerance']) {
            $mapper->enableMismatchesTolerance();
        }

        $parameters = new BindingParameters(TEST_ODB_HOST, TEST_ODB_PORT, TEST_ODB_USER, TEST_ODB_PASSWORD, TEST_ODB_DATABASE);
        $binding = new HttpBinding($parameters);
        $manager = new Manager($mapper, $binding);

        return $manager;
    }

    public function assertHttpStatus($expected, HttpBindingResultInterface $result, $message = null)
    {
        $response = $result->getInnerResponse();
        $status = $response->getStatusCode();
        $message = $message ?: $response->getBody();

        return $this->assertSame($expected, $status, $message);
    }

    public function assertCommandGives($expected, $got)
    {
        return $this->assertEquals($expected, $got, 'The raw command does not match the given SQL query');
    }

    public function assertTokens($expected, $got)
    {
        return $this->assertEquals($expected, $got, 'The given command tokens do not match');
    }
}
