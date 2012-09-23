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
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Daps\LdapBundle\Security\Ldap\Exception\ConnectionException;
use Daps\LdapBundle\Security\Ldap\LdapInterface;


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
    public function __construct(LdapInterface $ldap, $inactiveKeyValue)
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
        list($key, $value) = explode('=', $this->inactiveKeyValue);
        if ($this->ldap->usernameHasListing($username, $key, $value)) {
            throw new AuthenticationException("The account for user {$username} is inactive.");
        }
        
        try {
            $this->ldap->setUsername($username);
            $this->ldap->setPassword($password);
            $this->ldap->getConnection();
        } catch (ConnectionException $e) {
            throw new UsernameNotFoundException(sprintf('The presented password is invalid. "%s"', $e->getMessage()));
        }

        // @todo : how to manage roles ?
        return new User($username, null, array('ROLE_USER'));
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return new User($user->getUsername(), null, $user->getRoles());
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }

}
