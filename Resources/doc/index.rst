=============
Documentation
=============

LdapBundle Setup Instructions
=============================

To setup the LdapBundle, follow these steps:

1. Add ```"daps/ldap-bundle": "*@dev"``` to your ```composer.json```
2. Add ```new Daps\LdapBundle\DapsLdapBundle()``` to the ```app/AppKernel.php```
3. Add the ldap config to ``app/config/parameters.yml``
    ::
        parameters:
            daps_ldap.ldap.admin.dn: cn=admin,cn=Users,dc=example,dc=com
            daps_ldap.ldap.admin.password: admin
            daps_ldap.ldap.admin.enable: true
            daps_ldap.ldap.srv: False # or example.com to use srv records
            daps_ldap.ldap.host: ldap://example.com
            daps_ldap.ldap.port: 389
            daps_ldap.ldap.dn: CN
            daps_ldap.ldap.username_suffix: cn=Users,dc=example,dc=com

4. Modify ``app/config/security.yml`` and add your ldap user provider
    ::

        security:
            providers:
                daps_ldap:
                    id: daps_ldap_user_provider
                
    also tell Symfony how to encode passwords. For example
    ::

        security:
            encoders:
                Daps\LdapBundle\Security\User\LdapUser: plaintext
            
    You can now also ensure that you define the parts of your app that will be under LDAP protection. e.g
    ::

        secured_area:
            pattern:    ^/
            form-login-ldap: true

5. Next, in your ``apps/config/config.yml`` file, import the service
    ::

        imports:
            - { resource: parameters.yml }
            - { resource: security.yml }
            - { resource: "@DapsLdapBundle/Resources/config/services.yml"}
        
6. Setup your ``SecurityController``, routes and templates as detailed in the `Security Chapter <http://symfony.com/doc/current/book/security.html>`_ of the Symfony Documentation.
