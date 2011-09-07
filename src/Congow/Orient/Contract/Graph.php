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
 * Interface Graph
 *
 * @package     Orient
 * @subpackage  Contract
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Contract;

use Congow\Orient\Graph\Vertex;

interface Graph
{    
    /**
     * Adds a new vertex to the current graph.
     *
     * @param   Graph\Vertex $vertex 
     * @return  Congow\Orient\Graph
     * @throws  Congow\Orient\Exception
     */
    public function add(Vertex $vertex);
    
    /**
     * Returns the vertex identified with the $id associated to this graph.
     *
     * @param   mixed $id
     * @return  Congow\Orient\Graph\Vertex
     * @throws  Congow\Orient\Exception
     */
    public function getVertex($id);
    
    /**
     * Returns all the vertices that belong to this graph.
     *
     * @return Array
     */
    public function getVertices();
}
