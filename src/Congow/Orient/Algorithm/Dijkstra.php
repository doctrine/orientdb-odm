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
 * Class Dijkstra
 *
 * @package     
 * @subpackage  
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Algorithm;

use Congow\Orient\Contract\Algorithm as AlgorithmInterface;
use Congow\Orient\Graph;
use Congow\Orient\Graph\Vertex;

class Dijkstra implements AlgorithmInterface
{
    protected $startingVertex   = null;
    protected $endingVertex     = null;
    protected $graph            = null;
    protected $paths            = array();
    
    /**
     * @todo missing phpdoc
     */
    public function __construct(Graph $graph)
    {
        $this->graph  = $graph;
    }
    
    protected function doSolve(Vertex $root)
    {        
        $this->calculatePotentials($root);
        
        return $this->getShortestPath();
    }
    
    /**
     *
     * @todo missing phpdoc
     */
    public function getEndingVertex()
    {
        return $this->endingVertex;
    }
    
    /**
     *
     * @todo missing phpdoc
     */
    public function getShortestPath()
    {   
        $path   = array();
        $vertex = $this->getEndingVertex();
        
        while ($vertex->getId() != $this->getStartingVertex()->getId()) {
            $path[] = $vertex;
            $vertex = $vertex->getPotentialFrom();
        }
        
        $path[] = $this->getStartingVertex();
        
        return array_reverse($path);
    }
    
    /**
     *
     * @todo missing phpdoc
     */
    public function getStartingVertex()
    {        
        return $this->startingVertex;
    }
    
    /**
     *
     * @todo missing phpdoc
     */
    public function setEndingVertex(Vertex $vertex)
    {
        $this->endingVertex = $vertex;
    }
    
    /**
     *
     * @todo missing phpdoc
     */
    public function setStartingVertex(Vertex $vertex)
    {
        $this->paths[] = array($vertex);
        $this->startingVertex = $vertex;
    }
    
    /**
     *
     * @todo missing phpdoc
     */
    public function solve()
    {
        return $this->doSolve($this->getStartingVertex());
    }
    
    protected function calculatePotentials(Vertex $vertex)
    {                
        foreach ($vertex->getConnections() as $id => $distance) {
            $v = $this->getGraph()->getVertex($id);
            $v->setPotential($vertex->getPotential() + $distance, $vertex);
            
            foreach ($this->getPaths() as $path) {
                $count = count($path);
                
                if ($path[$count - 1]->getId() == $vertex->getId()) {
                    $this->paths[] = array_merge($path, array($v));
                }
            }
        }
        
        $vertex->markPassed();
        $mostAdjacentConnectionId = $vertex->getMostAdjacentConnectionId();
        
        if ($mostAdjacentConnectionId && $mostAdjacentConnectionId != $this->getEndingVertex()->getId()) {
            $vertex = $this->getGraph()->getVertex($mostAdjacentConnectionId);
            
            $this->calculatePotentials($vertex);
        }
        
        foreach ($this->getGraph()->getVertices() as $vertex) {
            if (!$vertex->isPassed()) {
                $this->calculatePotentials($vertex);
            }
        }
    }
    
    /**
     *
     * @todo missing phpdoc
     */
    protected function getMostAdjacentConnectionsByPotential(Vertex $vertex)
    {
        $adjacentVertex         = $vertex->getMostAdjacentConnectionIdByPotential($this->getGraph());
        $this->shortestPath[]   = $adjacentVertex;
        
        if ($adjacentVertex && $adjacentVertex->getId() != $this->getEndingVertex()->getId()) {
            $this->getMostAdjacentConnectionsByPotential($adjacentVertex);
        }
        
        return $this->shortestPath;
    }
    
    /**
     *
     * @todo missing phpdoc
     */
    protected function getGraph()
    {
        return $this->graph;
    }
    
    /**
     *
     * @todo missing phpdoc
     */
    protected function getPaths()
    {
        return $this->paths;
    }
}

