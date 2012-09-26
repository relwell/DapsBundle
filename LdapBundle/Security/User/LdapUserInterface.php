<?php 

namespace Daps\LdapBundle\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;

interface LdapUserInterface extends UserInterface
{
    /**
     * Sets the listing for an LDAP user
     * @param array $listing
     */
    public function setLdapListing( array $listing );
    
    /**
     * Provides the LDAP listing for a given user
     * 
     * @return array
     */
    public function getLdapListing();
    
    /**
     * @TODO: might be a good idea to make a user update the LDAP listing on save
     */
}