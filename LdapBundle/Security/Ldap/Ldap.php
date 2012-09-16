<?php

namespace Daps\LdapBundle\Security\Ldap;

use Daps\LdapBundle\Security\Ldap\Exception\ConnectionException;
use Daps\LdapBundle\Security\Ldap\Exception\LdapException;

/*
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 * @author Francis Besset <francis.besset@gmail.com>
 */
class Ldap implements LdapInterface
{
    private $host;
    private $port;
    private $dn;
    private $username;
    private $password;
    private $usernameSuffix;

    private $connection;

    /**
     * contructor
     *
     * @param string  $host
     * @param integer $port
     * @param string  $dn
     * @param string  $usernameSuffix
     */
    public function __construct($host = null, $port = 389, $dn = null, $usernameSuffix = null)
    {
        if (!extension_loaded('ldap')) {
            throw new LdapException('Ldap module is needed. ');
        }

        $this->host           = $host;
        $this->port           = $port;
        $this->dn             = $dn;
        $this->usernameSuffix = $usernameSuffix;

        $this->connection = null;
    }

    /**
     * {@inheritdoc}
     */
    public function findByUsername($username, $query, $filter = '*')
    {
        if (!$this->connection) {
            $this->connect();
        }

        if (!is_array($filter)) {
            $filter = array($filter);
        }

        $query  = sprintf($query, $this->getUsernameWithSuffix($username));
        $search = ldap_search($this->connection, $this->dn, $query, $filter);
        $infos  = ldap_get_entries($this->connection, $search);

        if (0 === $infos['count']) {
            return null;
        }

        return $infos[0];
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        if (!$this->connection) {
            $this->connect();
        }

        return $this->connection;
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    public function getPort()
    {
        return $this->port;
    }

    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    private function connect()
    {
        if (!$this->connection) {
            $time = time();
            $this->connection = ldap_connect($this->host, $this->port);
            $res = ldap_search($this->connection, 'ou=people,dc=example,dc=com', 'uid=John');
            var_dump(ldap_get_entries($this->connection, $res)); die;
            ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($this->connection, LDAP_OPT_REFERRALS, 0);
            if (false === @ldap_bind($this->connection, $this->getFullyQualifiedDN($this->username), $this->password)) {
                var_dump(sprintf('%s %s', $this->getFullyQualifiedDN($this->username), $this->password));
                echo 'no'; die;
                throw new ConnectionException(sprintf('Username / password invalid to connect on Ldap server %s:%s', $this->host, $this->port));
            }
            var_dump(sprintf('%s %s', $this->getFullyQualifiedDN($this->username), $this->password));
            echo 'hi'; die;
        }
        return $this;
    }

    private function disconnect()
    {
        if ($this->connection && is_resource($this->connection)) {
            ldap_unbind($this->connection);
        }

        $this->connection = null;

        return $this;
    }

    private function getFullyQualifiedDN($username = null)
    {
        $username = $username ?: $this->username;
        return $this->getUsernameWithSuffix(sprintf('%s=%s', $this->dn, $username));
    }
    
    private function getUsernameWithSuffix($username = null)
    {
        if (null === $username) {
            $username = $this->username;
        }

        return $username.','.$this->usernameSuffix;
    }
}
