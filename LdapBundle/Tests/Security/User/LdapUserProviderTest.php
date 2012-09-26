<?php

namespace Daps\LdapBundle\Tests\Security\User;

use Daps\LdapBundle\Security\User\LdapUserProvider;
use Daps\LdapBundle\Security\Ldap\Exception\ConnectionException;
use Daps\LdapBundle\Security\User\LdapUser;


class LdapUserProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException BadMethodCallException
     */
    public function testLoadUserByUsername()
    {
        $ldap = $this->getMock('Daps\LdapBundle\Security\Ldap\LdapInterface');

        $provider = new LdapUserProvider($ldap);
        $user = $provider->loadUserByUsername('foo');
    }

    public function testLoadUserByUsernameAndPasswordOk()
    {
        $ldap = $this->getMock('Daps\LdapBundle\Security\Ldap\LdapInterface');
        $ldap
            ->expects($this->once())
            ->method('setUsername')
        ;
        $ldap
            ->expects($this->once())
            ->method('setPassword')
        ;
        $ldap
            ->expects($this->once())
            ->method('bind')
        ;
        $ldap
            ->expects($this->once())
            ->method('getBoundRolesByOrgs')
            ->will($this->returnValue(array('ROLE_USER')))
        ;
        $ldap
            ->expects($this->once())
            ->method('getBoundListing')
            ->will($this->returnValue(array('testListing')))
        ;

        $provider = new LdapUserProvider($ldap);
        $user = $provider->loadUserByUsernameAndPassword('foo', 'bar');

        $this->assertInstanceOf('Daps\LdapBundle\Security\User\LdapUser', $user);
        $this->assertEquals('foo', $user->getUsername());
        $this->assertEquals(null, $user->getPassword());
        $this->assertEquals(array('ROLE_USER'), $user->getRoles());
        $this->assertEquals(array('testListing'), $user->getLdapListing());
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testLoadUserByUsernameAndPasswordNOk()
    {
        $ldap = $this->getMock('Daps\LdapBundle\Security\Ldap\LdapInterface');
        $ldap
            ->expects($this->once())
            ->method('setUsername')
        ;
        $ldap
            ->expects($this->once())
            ->method('setPassword')
        ;
        $ldap
            ->expects($this->once())
            ->method('bind')
            ->will($this->throwException(new ConnectionException('baz')))
        ;

        $provider = new LdapUserProvider($ldap);
        $provider->loadUserByUsernameAndPassword('foo', 'bar');
    }
    
    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AuthenticationException
     */
    public function testLoadUserByUserNameAndPasswordInactive()
    {
        $ldap = $this->getMock('Daps\LdapBundle\Security\Ldap\LdapInterface');
        $ldap
            ->expects($this->once())
            ->method('usernameHasListing')
            ->will($this->returnValue(true))
        ;
        
        $provider = new LdapUserProvider($ldap, 'foo=bar');
        $provider->loadUserByUsernameAndPassword('foo', 'bar');
    }
}
