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
 * Class Result encapsulates an hydration made with a mapper.
 *
 * @package    Orient
 * @subpackage ODM
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 */

namespace Congow\Orient\ODM\Mapper\Hydration;

use Congow\Orient\Proxy;
use Congow\Orient\ODM\Mapper\LinkTracker;

class Result
{
    protected $document;
    protected $linkTracker;

    /**
     * Instantiates a new hydration result.
     *
     * @param Proxy         $document
     * @param LinkTracker   $linkTracker
     */
    public function __construct($document, LinkTracker $linkTracker)
    {
        $this->document = $document;
        $this->linkTracker = $linkTracker;
    }

    /**
     * Returns the document associated with this result.
     *
     * @return \stdClass
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Returns the LinkTracker associated with this result.
     *
     * @return Linktracker
     */
    public function getLinkTracker()
    {
        return $this->linkTracker;
    }
}
