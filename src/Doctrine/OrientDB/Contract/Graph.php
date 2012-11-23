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
 * @package     Doctrine\OrientDB
 * @subpackage  Contract
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Contract;

use Doctrine\OrientDB\Graph\Vertex;

interface Graph
{
    /**
     * Adds a new vertex to the current graph.
     *
     * @param   Graph\Vertex $vertex
     * @return  Doctrine\OrientDB\Graph
     * @throws  Doctrine\OrientDB\Exception
     */
    public function add(Vertex $vertex);

    /**
     * Returns the vertex identified with the $id associated to this graph.
     *
     * @param   mixed $id
     * @return  Doctrine\OrientDB\Graph\Vertex
     * @throws  Doctrine\OrientDB\Exception
     */
    public function getVertex($id);

    /**
     * Returns all the vertices that belong to this graph.
     *
     * @return Array
     */
    public function getVertices();
}
