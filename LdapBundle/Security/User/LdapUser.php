<?php 

namespace Daps\LdapBundle\Security\User;

use Symfony\Component\Security\Core\User\User;
use Daps\LdapBundle\Security\User\LdapUserInterface;

class LdapUser 
    extends User 
    implements LdapUserInterface
{
    protected $ldapListing;
    
    public function setLdapListing( array $listing )
    {
        $this->ldapListing = $listing;
    }
    
    public function getLdapListing()
    {
        return $this->ldapListing;
    }
}