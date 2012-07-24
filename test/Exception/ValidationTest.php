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
 * Class ValidationTest
 *
 * @package
 * @subpackage
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace test\Exception;

use test\PHPUnit\TestCase;
use Congow\Orient\Exception\Validation;

class ValidationTest extends TestCase
{
    /**
     * @expectedException Congow\Orient\Exception\Validation
     * @expectedExceptionMessage Validation of "text" as V failed
     */
    public function testException()
    {
        throw new Validation('text', 'V');
    }

    /**
     * @expectedException Congow\Orient\Exception\Validation
     * @expectedExceptionMessage Validation of "a, b" as V failed
     */
    public function testExceptionWithArrayArgument()
    {
        throw new Validation(array('a', 'b'), 'V');
    }
}
