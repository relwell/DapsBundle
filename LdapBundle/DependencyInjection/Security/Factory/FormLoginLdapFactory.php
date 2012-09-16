<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daps\LdapBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\FormLoginFactory;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * FormLoginLdapFactory creates services for form login ldap authentication.
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class FormLoginLdapFactory extends FormLoginFactory
{
    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $provider = 'daps_ldap.security.authentication.provider.'.$id;
        $container
            ->setDefinition($provider, new DefinitionDecorator('daps_ldap.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProviderId))
            ->replaceArgument(2, $id)
        ;

        return $provider;
    }

    public function getKey()
    {
        return 'form-login-ldap';
    }
}
