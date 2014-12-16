<?php

/**
 * TestCase class bound to Doctrine\OrientDB.
 *
 * @author Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */

namespace test\PHPUnit;

use Doctrine\Common\Proxy\AbstractProxyFactory;
use Doctrine\ODM\OrientDB\Configuration;
use Doctrine\ODM\OrientDB\Manager;
use Doctrine\ODM\OrientDB\Mapper;
use Doctrine\OrientDB\Binding\HttpBinding;
use Doctrine\OrientDB\Binding\BindingParameters;
use Doctrine\OrientDB\Binding\HttpBindingResultInterface;
use Doctrine\OrientDB\Binding\Adapter\CurlClientAdapter;
use Doctrine\OrientDB\Binding\Client\Http\CurlClient;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    const COLLECTION_CLASS = '\Doctrine\ODM\OrientDB\Collections\ArrayCollection';

    protected function getBindingParameters($options)
    {
        $parameters = array();

        array_walk($options, function ($value, $key) use (&$parameters) {
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

    /**
     * @param String $className
     * @return String
     */
    public function getClassId($className)
    {
        return $this->createHttpBinding()->getClass($className)->getData()->clusters[0];
    }


    protected function getProxyDirectory()
    {
        return __DIR__ . '/../../test/proxies/Doctrine/OrientDB/Proxy/test';
    }

    protected function getConfiguration(array $opts = array())
    {
        return new Configuration(array_merge(
            array(
                'proxy_dir' => $this->getProxyDirectory(),
                'proxy_autogenerate_policy' => AbstractProxyFactory::AUTOGENERATE_ALWAYS,
                'document_dirs' => array(__DIR__.'/../../test/Integration/Document' => 'test')
            ),
            $opts
        ));
    }

    protected function createManager(Array $opts = array())
    {
        $config = $this->getConfiguration($opts);

        $parameters = new BindingParameters(TEST_ODB_HOST, TEST_ODB_PORT, TEST_ODB_USER, TEST_ODB_PASSWORD, TEST_ODB_DATABASE);
        $binding = new HttpBinding($parameters);
        $manager = new Manager($binding, $config);

        return $manager;
    }

    protected function ensureProxy(\stdClass $orientDocument)
    {
        $manager = $this->createManager();

        return $manager->getUnitOfWork()->getHydrator()->hydrate($orientDocument);
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
