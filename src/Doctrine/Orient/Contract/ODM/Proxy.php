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
 * Interface Proxy
 *
 * @package     Doctrine\Orient
 * @subpackage  Contract
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\Orient\Contract\ODM;

interface Proxy
{
    /**
     * Method used to serve proxies to POPOs.
     *
     * POPO implement lazy loading by calling:
     *
     * <code>
     *   $popo->getLazy()
     *
     *   function getLazy()
     *   {
     *     return call_user_func($this->lazy); // fires __invoke
     *   }
     * </code>
     */
    public function __invoke();
}
