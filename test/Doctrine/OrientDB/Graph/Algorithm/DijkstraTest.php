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
 * Class DijkstraTest
 *
 * @package
 * @subpackage
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace test\Doctrine\OrientDB\Graph\Algorithm;

use test\PHPUnit\TestCase;
use Doctrine\OrientDB\Graph\Graph;
use Doctrine\OrientDB\Graph\Vertex;
use Doctrine\OrientDB\Graph\Algorithm\Dijkstra;

class DijkstraTest extends TestCase
{
    public function setup()
    {
        $this->graph = new Graph();

        $this->rome       = new Vertex('rome');
        $this->zurich     = new Vertex('zurich');
        $this->amsterdam  = new Vertex('amsterdam');
        $this->london     = new Vertex('london');

        $this->graph->add($this->rome);
        $this->graph->add($this->zurich);
        $this->graph->add($this->amsterdam);
        $this->graph->add($this->london);

        $this->algorithm = new Dijkstra($this->graph);
    }

    public function testSPBetweenTwoNodesAreThemselves()
    {
        $this->rome->connect($this->zurich, 2);

        $this->algorithm->setStartingVertex($this->rome);
        $this->algorithm->setEndingVertex($this->zurich);

        $this->assertEquals(array($this->rome, $this->zurich), $this->algorithm->solve());

        $this->amsterdam->connect($this->london, 2);

        $this->algorithm->setStartingVertex($this->amsterdam);
        $this->algorithm->setEndingVertex($this->london);

        $this->assertEquals(array($this->amsterdam, $this->london), $this->algorithm->solve());
    }

    public function testSPBetweenThreeNodesWithoutAlternativePathsAreThemselves()
    {
        $this->rome->connect($this->zurich);
        $this->zurich->connect($this->amsterdam);

        $this->algorithm->setStartingVertex($this->rome);
        $this->algorithm->setEndingVertex($this->amsterdam);

        $this->assertEquals(array($this->rome, $this->zurich, $this->amsterdam), $this->algorithm->solve());
    }

    public function testSPBetweenThreeNodesWithAlternativePathsAreGood()
    {
        $this->rome->connect($this->zurich, 2);
        $this->rome->connect($this->amsterdam, 3);
        $this->zurich->connect($this->amsterdam, 2);

        $this->algorithm->setStartingVertex($this->rome);
        $this->algorithm->setEndingVertex($this->amsterdam);

        $this->assertEquals(array($this->rome, $this->amsterdam), $this->algorithm->solve());
    }

    public function testSPBetween4NodesWithAlternativePathsAreGood()
    {
        $this->rome->connect($this->zurich, 2);
        $this->rome->connect($this->amsterdam, 3);
        $this->rome->connect($this->london, 9);
        $this->zurich->connect($this->amsterdam, 2);
        $this->zurich->connect($this->london, 6);
        $this->amsterdam->connect($this->london, 3);

        $this->algorithm->setStartingVertex($this->rome);
        $this->algorithm->setEndingVertex($this->london);

        $this->assertEquals(array($this->rome, $this->amsterdam, $this->london), $this->algorithm->solve());
    }

    /**
     * @see http://it.wikipedia.org/wiki/File:Ricerca_operativa_percorso_minimo_09.gif
     */
    public function testWikipediaExample()
    {
        $graph  = new Graph();
        $home   = new Vertex('home');
        $a      = new Vertex('a');
        $b      = new Vertex('b');
        $c      = new Vertex('c');
        $d      = new Vertex('d');
        $e      = new Vertex('e');
        $office = new Vertex('office');

        $graph->add($home);
        $graph->add($a);
        $graph->add($b);
        $graph->add($c);
        $graph->add($d);
        $graph->add($e);
        $graph->add($office);

        $home->connect($a, 2);
        $home->connect($d, 8);
        $a->connect($b, 6);
        $a->connect($c, 2);
        $b->connect($office, 5);
        $c->connect($d, 2);
        $c->connect($e, 9);
        $d->connect($e, 3);
        $e->connect($office);

        $algorithm = new Dijkstra($graph);
        $algorithm->setStartingVertex($home);
        $algorithm->setEndingVertex($office);

        $this->assertEquals(array($home, $a, $c, $d, $e, $office), $algorithm->solve());
    }

    public function testYouGetTheDistance()
    {
        $this->rome->connect($this->zurich, 4);

        $this->algorithm->setStartingVertex($this->rome);
        $this->algorithm->setEndingVertex($this->zurich);
        $this->algorithm->solve();

        $this->assertEquals(4, $this->algorithm->getDistance());
    }

    /**
     * @expectedException \Doctrine\OrientDB\LogicException
     */
    public function testYouNeedToSolveTheAlgorithmBeforeCalculatingTheDistance()
    {
        $this->rome->connect($this->zurich, 4);
        $this->algorithm->setStartingVertex($this->rome);
        $this->algorithm->setEndingVertex($this->zurich);
        $this->algorithm->getDistance();
    }

    public function testYouRetrieveANiceStringToOutputThePath()
    {
        $this->rome->connect($this->zurich, 4);
        $this->algorithm->setStartingVertex($this->rome);
        $this->algorithm->setEndingVertex($this->zurich);

        $this->assertEquals('rome - zurich', $this->algorithm->getLiteralShortestPath());
    }

    /**
     * @expectedException \Doctrine\OrientDB\LogicException
     */
    public function testYouCantSolveTheAlgorithmWithoutaStart()
    {
        $this->rome->connect($this->zurich, 4);
        $this->algorithm->setEndingVertex($this->zurich);
        $this->algorithm->solve();
    }

    /**
     * @expectedException \Doctrine\OrientDB\LogicException
     */
    public function testYouCantSolveTheAlgorithmWithoutaEnd()
    {
        $this->rome->connect($this->zurich, 4);
        $this->algorithm->setStartingVertex($this->zurich);
        $this->algorithm->solve();
    }
}
