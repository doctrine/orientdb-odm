<?php

/*
 * This file is part of the Doctrine\OrientDB package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * An exception raised when an Json object cannot be converted to a POPO
 *
 * @package    Doctrine\ODM
 * @subpackage OrientDB
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\ODM\OrientDB;

use Doctrine\OrientDB\Exception;

class DocumentNotFoundException extends Exception
{
    const MESSAGE = 'The object can not be converted to a POPO mapped by the ODM.';

    public function __construct($explanation = '')
    {
        $this->message = self::MESSAGE;
        if (strlen($explanation) > 0) {
            $this->message .= ' ' . $explanation;
        }
    }

    public static function documentNotFound($className, $identifier)
    {
        return new self(sprintf(
            'The "%s" document with identifier %s could not be found.',
            $className,
            json_encode($identifier)
        ));
    }
}
