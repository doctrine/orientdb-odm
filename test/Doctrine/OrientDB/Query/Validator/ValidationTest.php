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

namespace test\Doctrine\OrientDB\Query\Validator;

use test\PHPUnit\TestCase;
use Doctrine\OrientDB\Query\Validator\ValidationException;

class ValidationTest extends TestCase
{
    /**
     * @expectedException Doctrine\OrientDB\Query\Validator\ValidationException
     * @expectedExceptionMessage Validation of "text" as V failed
     */
    public function testException()
    {
        throw new ValidationException('text', 'V');
    }

    /**
     * @expectedException Doctrine\OrientDB\Query\Validator\ValidationException
     * @expectedExceptionMessage Validation of "a, b" as V failed
     */
    public function testExceptionWithArrayArgument()
    {
        throw new ValidationException(array('a', 'b'), 'V');
    }
}
