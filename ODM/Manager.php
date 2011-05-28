<?php

/*
 * This file is part of the Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * (c) David Funaro <ing.davidino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Manager class.
 *
 * @package    Orient
 * @subpackage ODM
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 */

namespace Orient\ODM;

use Orient\ODM\Mapper;

class Manager
{
    protected $mapper;

    public function __construct(Mapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function hydrate($json)
    {
        return $this->mapper->hydrate($json);
    }

    /**
     * @todo Directories to look for document classes are hardcoded
     */
    public function getDocumentDirectories()
    {
        return $this->mapper->getDocumentDirectories();
    }

    public function setDocumentDirectories(array $directories)
    {
        $this->mapper->setDocumentDirectories($directories);
    }
}