<?php

namespace test\Doctrine\ODM\OrientDB\Integration\Mapper;


use Doctrine\ODM\OrientDB\Mapper\ClassMetadataFactory;
use test\PHPUnit\TestCase;

class ClassMetadataFactoryTest extends TestCase
{
    protected $metadataFactory;

    public function setup()
    {
        $this->metadataFactory = $this->createManager(array('document_dirs' => array('test/Doctrine/ODM/OrientDB/Document/Stub' => 'test')))->getMetadataFactory();
    }

    public function testConvertPathToClassName()
    {
        $className = $this->metadataFactory->getClassByPath('./test/Doctrine/ODM/OrientDB/Document/Stub/City.php', 'test');
        $this->assertEquals('\test\Doctrine\ODM\OrientDB\Document\Stub\City', $className);
    }

    public function testConvertPathToClassNameWhenProvidingNestedNamespaces()
    {
        $className = $this->metadataFactory->getClassByPath('./test/Doctrine/ODM/OrientDB/Document/Stub/City.php', 'test\Doctrine\ODM\OrientDB');
        $this->assertEquals('\test\Doctrine\ODM\OrientDB\Document\Stub\City', $className);
    }

    public function testGettingTheDirectoriesInWhichTheMapperLooksForPOPOs()
    {
        $metadataFactory = new ClassMetadataFactory(
            $this->getMock('\Doctrine\ODM\OrientDB\Mapper\Annotations\ReaderInterface'),
            $this->getMock('\Doctrine\Common\Cache\Cache')
        );

        $directories = array('dir' => 'namespace', 'dir2' => 'namespace2');

        $metadataFactory->setDocumentDirectories($directories);

        $this->assertEquals($directories, $metadataFactory->getDocumentDirectories());
    }
} 