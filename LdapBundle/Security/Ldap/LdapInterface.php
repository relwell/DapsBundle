<?php

namespace Symfony\Component\Security\Ldap;

use Symfony\Component\Security\Ldap\Exception\ConnectionException;

interface LdapInterface
{
    /**
     * return a connection binded to the ldap
     *
     * @return ressource A connection
     *
     * @throws ConnectionException If username / password are not bindable
     */
    public function getConnection();

    /**
     * getPassword
     *
     * @return string The password
     */
    public function getPassword();

    /**
     * setPassword
     *
     * @param string $password The password
     */
    public function setPassword($password);

    /**
     * getPort
     *
     * @return integer The port
     */
    public function getPort();

    /**
     * setPort
     *
     * @param integer $port The port
     */
    public function setPort($port);

    /**
     * getUsername
     *
     * @return string The username
     */
    public function getUsername();

    /**
     * setUsername
     *
     * @param string $username The username
     */
    public function setUsername($username);

    /*
     * find a username into ldap connection
     *
     * @todo : not needed anymore. But could be usefull to retrieve roles
     *
     * @param  string     $username
     * @param  string     $query
     * @param  string     $filter
     * @return array|null
     */
    public function findByUsername($username, $query, $filter);
}
