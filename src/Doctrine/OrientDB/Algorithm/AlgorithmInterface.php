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
 * Interface Algorithm
 *
 * @package     Doctrine\OrientDB
 * @subpackage  Algorithm
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Algorithm;

interface AlgorithmInterface
{
    /**
     * Solves the algorithm and returns all possible results.
     *
     * @return mixed
     */
    public function solve();
}
