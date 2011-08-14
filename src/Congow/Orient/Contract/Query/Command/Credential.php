<?php

/*
 * This file is part of the Congow\Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * The Credential interface manages the SQL statements dealing with th CRUD
 * of credentials in Congow\OrientDB.
 *
 * @package    Congow\Orient
 * @subpackage Contract
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Contract\Query\Command;

interface Credential
{
    /**
     * Sets the $permission to grant/revoke.
     *
     * @param   string  $permission
     * @return  Credential
     */
    public function permission($permission);

    /**
     * Sets the $resource on which a credential is granted/revoked.
     *
     * @param  string  $resource
     * @return Credential
     */
    public function on($resource);

    /**
     * Sets the user/group subject of the credential addition/removal.
     *
     * @param   string  $role
     * @return  Credential
     */
    public function to($role);
}
