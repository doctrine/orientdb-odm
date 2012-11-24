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
 * Class Graph is a dataset to easily work with a simulated graph.
 *
 * @package     Orient
 * @subpackage  Graph
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Graph;

use Doctrine\OrientDB\Exception;

class Graph implements GraphInterface
{
    /**
     * All the vertices in the graph
     *
     * @var array
     */
    protected $vertices = array();

    /**
     * {@inheritdoc}
     */
    public function add(VertexInterface $vertex)
    {
        if (array_key_exists($vertex->getId(), $this->getVertices())) {
            throw new Exception('Unable to insert multiple Vertices with the same ID in a Graph');
        }

        $this->vertices[$vertex->getId()] = $vertex;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getVertex($id)
    {
        $vertices = $this->getVertices();

        if (!array_key_exists($id, $vertices)) {
            throw new Exception("Unable to find $id in the Graph");
        }

        return $vertices[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function getVertices()
    {
        return $this->vertices;
    }
}
