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
 * Class Vertex is the foundation of a graph entity.
 *
 * @package     Doctrine\OrientDB
 * @subpackage  Graph
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Graph;

class Vertex implements VertexInterface
{
    protected $id;
    protected $potential;
    protected $potentialFrom;
    protected $connections = array();
    protected $passed = false;

    /**
     * Instantiates a new vertex, requiring a ID to avoid collisions.
     *
     * @param mixed $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Connects the vertex to another $vertex.
     * A $distance, to balance the connection, can be specified.
     *
     * @param Vertex $vertex
     * @param integer $distance
     */
    public function connect(VertexInterface $vertex, $distance = 1)
    {
        $this->connections[$vertex->getId()] = $distance;
    }

    /**
     * Returns the connections of the current vertex.
     *
     * @return Array
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * Returns the identifier of this vertex.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns vertex's potential.
     *
     * @return integer
     */
    public function getPotential()
    {
        return $this->potential;
    }

    /**
     * Returns the vertex which gave to the current vertex its potential.
     *
     * @return Doctrine\OrientDB\Graph\Vertex
     */
    public function getPotentialFrom()
    {
        return $this->potentialFrom;
    }

    /**
     * Returns whether the vertex has passed or not.
     *
     * @return boolean
     */
    public function isPassed()
    {
        return $this->passed;
    }

    /**
     * Marks this vertex as passed, meaning that, in the scope of a graph, he
     * has already been processed in order to calculate its potential.
     */
    public function markPassed()
    {
        $this->passed = true;
    }

    /**
     * Sets the potential for the vertex, if the vertex has no potential or the
     * one it has is higher than the new one.
     *
     * @param   integer $potential
     * @param   Vertex $from
     * @return  boolean
     */
    public function setPotential($potential, VertexInterface $from)
    {
        $potential = (int) $potential;

        if (!$this->getPotential() || $potential < $this->getPotential()) {
            $this->potential = $potential;
            $this->potentialFrom = $from;

            return true;
        }

        return false;
    }
}
