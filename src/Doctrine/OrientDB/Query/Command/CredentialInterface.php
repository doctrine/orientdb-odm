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
 * The Credential interface manages the SQL statements dealing with th CRUD
 * of credentials in Doctrine\OrientDB.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Command;

interface CredentialInterface
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
