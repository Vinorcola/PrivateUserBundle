# Private User bundle

A Symfony bundle to provide user management for private applications.

This bundle allows a set of pre-authorized users to register on your application. The workflow is the following:

1. An administrator (or any authorized user) configure a list of authorized users (with email address mainly);
1. An allowed user can then register with it's email address; If the email address is not in the list configured by the administrator, the registration will be forbidden;
1. An email will be sent to the user's email address to allow him to choose it's password.

This system is meant to be used on internal application available on the web. It restricts the registration process to allowed email addresses only.

The administrator also has the possibility to disable the access to specific users already registered.

## Configuration

This version must be used with Symfony 5.3. If you have a lower version, please use `v1` branch.

The bundle can work without any configuration. It will create a user table.

You can configure several types of users by defining a list of specific roles:

```yaml
# config/packages/vinorcola_private_user.yaml

vinorcola_private_user:
    sending_email_address: "no-reply@example.com"
    types:
        user:
            roles: [ "ROLE_USER" ]
        admin:
            roles: [ "ROLE_USER", "ROLE_ADMIN" ]
```

If no config is set, the only type available will be "user" with "ROLE_USER".

Because the bundle cannot configure the `security`, you have to add another config file with the following content:

```yaml
# config/packages/private_user.yaml

security:
    enable_authenticator_manager: true # For Symfony 5.3
    password_hashers:
        Vinorcola\PrivateUserBundle\Entity\User:
            algorithm: bcrypt
            cost: 12

    providers:
        main:
            id: Vinorcola\PrivateUserBundle\Security\UserProvider
```

Note that you can set the password encoder you want.

Finally, the bundle routes must be added to the application.

```yaml
# config/routes/private_user.yaml

admin:
    resource: ../../vendor/vinorcola/private-user-bundle/src/Controller/AdminController.php
    type: attribute
    prefix: /admin/user

forgottenPassword:
    resource: ../../vendor/vinorcola/private-user-bundle/src/Controller/ForgottenPasswordController.php
    type: attribute

security:
    resource: ../../vendor/vinorcola/private-user-bundle/src/Controller/SecurityController.php
    type: attribute

registration:
    resource: ../../vendor/vinorcola/private-user-bundle/src/Controller/RegistrationController.php
    type: attribute

profile:
    resource: ../../vendor/vinorcola/private-user-bundle/src/Controller/ProfileController.php
    type: attribute
```

## Customize

The bundle provides simple template files, but it is recommended to copy them in order to customize them. Simply copy the folder `vendor/vinorcola/private-user-bundle/src/Resources/views` in `templates/bundles/VinorcolaPrivateUserBundle` and start modifying them to fit your graphic chart.
