<?php

// taken from the godforsaken cookbook. might as well be "how to serve man"

namespace Daps\LdapBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class LdapUserToken extends AbstractToken
{
    public $created;
    public $digest;
    public $nonce;

    public function __construct(array $roles = array())
    {
        parent::__construct($roles);

        // If the user has roles, consider it authenticated
        $this->setAuthenticated(count($roles) > 0);
    }

    public function getCredentials()
    {
        return '';
    }
}