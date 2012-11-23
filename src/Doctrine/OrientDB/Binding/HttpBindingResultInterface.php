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
 * HTTP bindings results set.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Binding
 * @author     Daniele Alessandri <suppakilla@gmail.com>
 */

namespace Doctrine\OrientDB\Binding;

interface HttpBindingResultInterface extends BindingResultInterface
{
    /**
     * Returns the inner response object returned by the underlying client.
     *
     * @return mixed
     */
    public function getInnerResponse();

    /**
     * Returns if the response from the server is valid or has errors.
     *
     * @return boolean
     */
    public function isValid();
}
