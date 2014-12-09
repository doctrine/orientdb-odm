<?php

namespace test\Doctrine\ODM\OrientDB\Mapper;


use Doctrine\ODM\OrientDB\Mapper\ClusterMap;
use Doctrine\ODM\OrientDB\Types\Rid;
use test\PHPUnit\TestCase;

class ClusterMapTest extends TestCase
{
    protected function createBinding()
    {
        $binding = $this->getMockBuilder('\Doctrine\OrientDB\Binding\HttpBinding')
            ->disableOriginalConstructor()
            ->getMock();

        $binding->expects($this->once())
            ->method('getDatabaseName')
            ->will($this->returnValue(TEST_ODB_DATABASE));

        return $binding;
    }

    protected function createCache($filled = false)
    {
        $cache = $this->getMock('\Doctrine\Common\Cache\Cache');

        $cache->expects($this->once())
            ->method('contains')
            ->with($this->getCacheKey())
            ->will($this->returnValue($filled));

        if ($filled) {
            $cache->expects($this->once())
                ->method('fetch')
                ->with($this->getCacheKey())
                ->will($this->returnValue($this->prepareMap()));
        }

        return $cache;
    }

    protected function prepareMap()
    {
        return array('Test' => array(1, 2));
    }

    protected function prepareGeneration($binding, $cache)
    {
        $result = $this->getMock('\Doctrine\OrientDB\Binding\BindingResultInterface');
        $data = new \stdClass();
        $class = new \stdClass();
        $class->clusters = array(1, 2);
        $class->name = 'Test';
        $data->classes = array($class);

        $result->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $binding->expects($this->once())
            ->method('getDatabase')
            ->will($this->returnValue($result));

        $cache->expects($this->once())
            ->method('save')
            ->with($this->getCacheKey(), $this->prepareMap());
    }

    protected function getCacheKey()
    {
        return sprintf(ClusterMap::CACHE_KEY, TEST_ODB_DATABASE);
    }

    public function testIdentifyClassWithCache()
    {
        $binding = $this->createBinding();
        $cache = $this->createCache(true);

        $clusterMap = new ClusterMap($binding, $cache);
        $this->assertEquals('Test', $clusterMap->identifyClass(new Rid('1:0')));
    }

    public function testIdentifyClassWithoutCache()
    {
        $binding = $this->createBinding();
        $cache = $this->createCache();
        $this->prepareGeneration($binding, $cache);

        $clusterMap = new ClusterMap($binding, $cache);
        $this->assertEquals('Test', $clusterMap->identifyClass(new Rid('1:0')));
    }

    /**
     * @expectedException \Doctrine\ODM\OrientDB\Mapper\MappingException
     */
    public function testIdentifyClassNotFound()
    {
        $binding = $this->createBinding();
        $cache = $this->createCache(true);

        $clusterMap = new ClusterMap($binding, $cache);
        $clusterMap->identifyClass(new Rid('10:0'));
    }

} 