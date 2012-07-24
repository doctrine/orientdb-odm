<?php

/**
 * ClusterTest
 *
 * @package    Congow\Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace test\Query\Command\Truncate;

use test\PHPUnit\TestCase;
use Congow\Orient\Query\Command\Truncate\Cluster as TruncateCluster;

class ClusterTest extends TestCase
{
    public function testYouGenerateAValidSQLToTruncateAClass()
    {
        $truncate = new TruncateCluster('myClass');

        $this->assertCommandGives("TRUNCATE CLUSTER myClass", $truncate->getRaw());
    }

    public function testTheNameArgumentIsFiltered()
    {
        $truncate = new TruncateCluster('myClass 54..');

        $this->assertCommandGives("TRUNCATE CLUSTER myClass54", $truncate->getRaw());
    }
}
