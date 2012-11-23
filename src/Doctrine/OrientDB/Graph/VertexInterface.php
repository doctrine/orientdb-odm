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
 * Interface Vertex
 *
 * @package     Doctrine\OrientDB
 * @subpackage  Graph
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Graph;

interface VertexInterface
{
    /**
     * Connects the vertex to another $vertex.
     * A $distance, to balance the connection, can be specified.
     *
     * @param Vertex $vertex
     * @param integer $distance
     */
    public function connect(VertexInterface $vertex, $distance = 1);

    /**
     * Returns the connections of the current vertex.
     *
     * @return Array
     */
    public function getConnections();

    /**
     * Returns the identifier of this vertex.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Returns vertex's potential.
     *
     * @return integer
     */
    public function getPotential();

    /**
     * Returns the vertex which gave to the current vertex its potential.
     *
     * @return Vertex
     */
    public function getPotentialFrom();

    /**
     * Returns whether the vertex has passed or not.
     *
     * @return boolean
     */
    public function isPassed();

    /**
     * Marks this vertex as passed, meaning that, in the scope of a graph, he
     * has already been processed in order to calculate its potential.
     */
    public function markPassed();

    /**
     * Sets the potential for the vertex, if the vertex has no potential or the
     * one it has is higher than the new one.
     *
     * @param   integer $potential
     * @param   Vertex $from
     * @return  boolean
     */
    public function setPotential($potential, VertexInterface $from);
}
