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
 * Class Dijkstra is an implementation of the famous Dijkstra's algorithm to
 * calculate the shortest path between two vertices of a graph.
 *
 * @package     Orient
 * @subpackage  Graph
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
     * Instantiates a new algorithm, requiring a graph to work with.
     *
     * @param Graph $graph 
     */
    public function __construct(Graph $graph)
    {
        $this->graph  = $graph;
    }
    
    /**
     * Returns the distance between the starting and the ending point.
     *
     * @return integer
     */
    public function getDistance()
    {
        return $this->getEndingVertex()->getPotential();
    }
    
    /**
     * Gets the vertex which we are pointing to.
     *
     * @return Vertex
     */
    public function getEndingVertex()
    {
        return $this->endingVertex;
    }
    
    /**
     * Returns the solution in a human-readable style.
     * 
     * @return string
     */
    public function getLiteralShortestPath()
    {
        $path = $this->solve();
        
        $literal = '';
        
        foreach ($path as $p) {
            $literal .= "{$p->getId()} - ";
        }
        
        return substr($literal, 0, count($literal) - 3);
    }
    
    /**
     * Reverse-calculates the shortest path of the graph thanks the potentials
     * stored in the vertices.
     *
     * @return Array
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
     * Retrieves the vertex which we are starting from to calculate the shortest path.
     *
     * @return Vertex
     */
    public function getStartingVertex()
    {        
        return $this->startingVertex;
    }
    
    /**
     * Sets the vertex which we are pointing to.
     * 
     * @param Vertex $vertex
     */
    public function setEndingVertex(Vertex $vertex)
    {
        $this->endingVertex = $vertex;
    }
    
    /**
     * Sets the vertex which we are starting from to calculate the shortest path.
     * 
     * @param Vertex $vertex
     */
    public function setStartingVertex(Vertex $vertex)
    {
        $this->paths[] = array($vertex);
        $this->startingVertex = $vertex;
    }
    
    /**
     * Solves the algorithm and returns the shortest path as an array.
     *
     * @return  Array
     */
    public function solve()
    {
        $this->calculatePotentials($this->getStartingVertex());
        
        return $this->getShortestPath();
    }
    
    /**
     * Recursively calculates the potentials of the graph, from the
     * starting point you specify with ->setStartingVertex(), traversing
     * the graph due to Vertex's $connections attribute.
     *
     * @param Vertex $vertex 
     */
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
     * Returns the graph associated with this algorithm instance.
     *
     * @return Graph
     */
    protected function getGraph()
    {
        return $this->graph;
    }
    
    /**
     * Returns the possible paths registered in the graph.
     *
     * @return Array
     */
    protected function getPaths()
    {
        return $this->paths;
    }
}

