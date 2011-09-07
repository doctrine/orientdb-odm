<?php

/*
 * This file is part of the Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class GraphTest
 *
 * @package     
 * @subpackage  
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace test;

use test\PHPUnit\TestCase;
use Congow\Orient\Graph;

class GraphTest extends TestCase
{
    public function setup()
    {
        $this->graph = new Graph();
        
        $this->v1 = new Graph\Vertex('v1');
    }
    
    public function  testYouCanAddVerticesToAGraph()
    {
        $this->assertEquals(array('v1' => $this->v1), $this->graph->add($this->v1)->getVertices());
    }
    
    /**
     * @expectedException Congow\Orient\Exception
     */
    public function  testYouCannotAddVerticesWithTheSameIdToAGraph()
    {
        $this->assertEquals(array('v1' => $this->v1), $this->graph->add($this->v1)->add($this->v1)->getVertices());
    }
    
    public function  testYouCanRetrieveAVertexFromTheGraph()
    {
        $this->assertEquals($this->v1, $this->graph->add($this->v1)->getVertex($this->v1->getId()));
    }

    /**
     * @expectedException Congow\Orient\Exception
     */
    public function  testAnExceptionIsRaisedWhenTryingToRetrieveANonExistingVertexFromTheGraph()
    {
        $this->assertEquals($this->v1, $this->graph->add($this->v1)->getVertex('OMGOMG'));
    }
}

