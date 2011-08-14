<?php

/*
 * This file is part of the Congow\Orient package.
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
 * @package    Congow\Orient
 * @subpackage ODM
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 */

namespace Congow\Orient\ODM;

use Congow\Orient\ODM\Mapper;

class Manager
{
    protected $mapper;
    
    /**
     * @param Mapper $mapper
     */
    public function __construct(Mapper $mapper)
    {
        $this->mapper = $mapper;
    }
    
    /**
     * delegate the hydration of orientDB record to the mapper
     * @param JSON $json
     * @return mixed the hydrated object
     */
    public function hydrate($json)
    {
        return $this->mapper->hydrate($json);
    }
    
    /**
     * get the document directories paths
     * @return Array 
     */
    public function getDocumentDirectories()
    {
        return $this->mapper->getDocumentDirectories();
    }
    
    /**
     * Set the document directories paths
     * @param Array $directories
     * @return void
     */
    public function setDocumentDirectories(array $directories)
    {
        $this->mapper->setDocumentDirectories($directories);
    }
}