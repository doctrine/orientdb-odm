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
 * Class Graph
 *
 * @package     
 * @subpackage  
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient;

class Graph
{
    protected $vertices = array();
    
    /**
     * @todo missing phpdoc
     * @todo throw custom exception
     */
    public function add(Graph\Vertex $vertex)
    {
        foreach ($this->getVertices() as $oldVertex) {
            if ($oldVertex->getId() == $vertex->getId()) {
                $message = 'Unable to insert multiple Vertices with the same ID in a Graph';
                
                throw new \Exception($message);
            }
        }
        
        $this->vertices[$vertex->getId()] = $vertex; 
    }
    
    /**
     * Returns the vertex identified with the $id associated to this graph.
     *
     * @param   mixed $id
     * @return  Congow\Orient\Graph\Vertex
     * @throws  Congow\Orient\Exception
     */
    public function getVertex($id)
    {
        $vertices = $this->getVertices();
        
        if (array_key_exists($id, $vertices)) {
            return $vertices[$id];
        }
        
        $message = sprintf('Unable to find %s in the Graph', $id);
        
        throw new Exception($message);
    }
    
    /**
     * Returns all the vertices that belong to this graph.
     *
     * @return Array
     */
    public function getVertices()
    {
        return $this->vertices;
    }
}

