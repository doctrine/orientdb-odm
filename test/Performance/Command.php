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
 * Class Command
 *
 * @package
 * @subpackage
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author      David Funaro <ing.davidino@gmail.com>
 */

namespace test\Performance;

require __DIR__ . "/../../test/PHPUnit/bootstrap.php";


class Command
{
    protected $time;
    protected $elapsed;

    public function run()
    {
        $this->startTime();
        for ($x = 0; $x < 100000; $x++) {
            new \Doctrine\Orient\Query\Command\Credential\Grant('OMN');
        }

        $this->stop();
        $this->output();
    }

    protected function startTime()
    {
        $this->time = microtime(true);
    }

    protected function stop()
    {
        $this->elapsed = microtime(true) - $this->time;
    }

    protected function output()
    {
        echo "Elapsed: \n{$this->elapsed}\n";
    }
}

$benchmark = new Command();

$benchmark->run();
