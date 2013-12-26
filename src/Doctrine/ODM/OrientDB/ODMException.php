<?php

namespace Doctrine\ODM\OrientDB;

use Exception;

/**
 * Base exception class for all ODM exceptions.
 */
class ODMException extends Exception
{
    /**
     * @param string $className
     *
     * @return ODMException
     */
    public static function invalidDocumentRepository($className)
    {
        return new self(
            'Invalid repository class "' . $className . '". It must be a Doctrine\Common\Persistence\ObjectRepository.'
        );
    }
}
