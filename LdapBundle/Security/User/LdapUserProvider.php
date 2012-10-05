<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daps\LdapBundle\Security\User;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Daps\LdapBundle\Security\Ldap\Exception\ConnectionException;
use Daps\LdapBundle\Security\Ldap\LdapInterface;
use Daps\LdapBundle\Security\User\LdapUser;
use Daps\LdapBundle\Security\User\LdapUserInterface;


/**
 * LdapUserProvider is a simple user provider on top of ldap.
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class LdapUserProvider implements LdapUserProviderInterface
{
    private $ldap;
    private $inactiveKeyValue;

    /**
     * @param LdapInterface $ldap
     */
    public function __construct(LdapInterface $ldap, $inactiveKeyValue=null)
    {
        $this->ldap = $ldap;
        $this->inactiveKeyValue = $inactiveKeyValue;
    }

    /**
     * {@inheritDoc}
     *
     * Due to Ldap limitation, this method should never be called.
     * Call loadUserByUsernameAndPassword instead.
     */
    public function loadUserByUsername($username)
    {
        throw new \BadMethodCallException(sprintf('You should not call the method "%s"', __METHOD__));
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByUsernameAndPassword($username, $password)
    {
        if ( $this->inactiveKeyValue !== null ) {
            list($key, $value) = explode('=', $this->inactiveKeyValue);
            if ($this->ldap->usernameHasListing($username, $key, $value)) {
                // we could also return a user with enabled = false
                throw new AuthenticationException("The account for user {$username} is inactive.");
            }
        }
        
        try {
            $this->ldap->setUsername($username);
            $this->ldap->setPassword($password);
            $this->ldap->bind();
        } catch (ConnectionException $e) {
            throw new UsernameNotFoundException(sprintf('The presented password is invalid. "%s"', $e->getMessage()));
        }
        
        $roleArray = $this->ldap->getBoundRolesByOrgs();
        
        $user = new LdapUser($username, null, $roleArray);
        $user->setLdapListing($this->ldap->getBoundListing());
        
        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        // we're just going to return the new user, basically
        if (!$user instanceof LdapUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return $class === 'Daps\LdapBundle\Security\User\LdapUser';
    }

}
