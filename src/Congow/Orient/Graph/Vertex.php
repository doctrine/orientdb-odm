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
 * Class Vertex
 *
 * @package     
 * @subpackage  
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Graph;

use Congow\Orient\Graph;

class Vertex
{
    protected $id;
    protected $connections      = array();
    protected $potential        = null;
    protected $potentialFrom    = null;
    protected $passed           = false;
    
    /**
     *
     * @todo missing phpdoc
     */
    public function __construct($id)
    {
        $this->id = $id;
    }
    
    /**
     *
     * @todo missing phpdoc
     */
    public function connect(Vertex $vertex, $distance = 1)
    {
        $this->connections[$vertex->getId()] = $distance;
    }
    
    /**
     * @todo missing phpdoc
     */
    public function getConnections()
    {
        return $this->connections;
    }
    
    /**
     *
     * @todo missing phpdoc
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     *
     * @todo missing phpdoc
     */
    public function getMostAdjacentConnectionId()
    {
        $connections = array_flip($this->getConnections());
        sort($connections);
        
        return count($connections) ? array_shift($connections) : null;
    }
    
    /**
     *
     * @todo missing phpdoc
     */
    public function getMostAdjacentConnectionIdByPotential(Graph $graph)
    {
        $connections =  array();
        
        foreach ($this->getConnections() as $id => $distance) {
            $connections[$id] = $graph->getVertex($id); 
        }

        usort($connections, function ($v1, $v2) use ($graph) {
            if ($v1->getPotential() >= $v2->getPotential()) {
                return 1;
            }

            return -1;
        });

        return count($this->getConnections()) ? array_shift($connections) : null;
    }
    
    /**
     *
     * @todo missing phpdoc
     */
    public function getPotential()
    {
        return $this->potential;
    }
    
    /**
     *
     * @todo missing phpdoc
     */
    public function getPotentialFrom()
    {
        return $this->potentialFrom;
    }
    
    /**
     *
     * @todo missing phpdoc
     */
    public function isPassed()
    {
        return $this->passed;
    }
    
    /**
     *
     * @todo missing phpdoc
     */
    public function markPassed()
    {
        $this->passed = true;
    }
    
    /**
     *
     * @todo missing phpdoc
     */
    public function setPotential($potential, Vertex $from)
    {
        $potential = (int) $potential;
        
        if (!$this->getPotential() || $potential < $this->getPotential()) {
            $this->potential        = $potential;
            $this->potentialFrom    = $from;
        }
    }
}

