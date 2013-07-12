=============
Documentation
=============

DapsBundle Setup Instructions
=============================

To setup the DapsBundle LdapBundle, follow these steps:

1. Ensure the DapsBundle is placed in the ``src`` directory. e.g. ``src\Daps\LdapBundle``.
2. Ensure you create the ``ldapcredentials.yml`` configuration file and place it in the ``src\Daps\LdapBundle\Resources\config`` directory. You may use the ``ldapcredentials.example.yml`` file in the same directory as your starting point.
3. Modify ``security.yml`` and add your ldap user provider.::

    // app/config/security.yml
    security:
        providers:
            daps_ldap:
                id: daps_ldap_user_provider
                
also tell Symfony how to encode passwords. For example::

    security:
        encoders:
            Daps\LdapBundle\Security\User\LdapUser: plaintext
            
You can now also ensure that you define the parts of your app that will be under LDAP protection. e.g::

      secured_area:
          pattern:    ^/
          form-login-ldap: true

4. Next, in your ``apps/config/config.yml`` file, import the service::

    imports:
        - { resource: parameters.yml }
        - { resource: security.yml }
        - { resource: "@DapsLdapBundle/Resources/config/services.yml"}
        
5. 
