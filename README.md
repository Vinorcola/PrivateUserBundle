# Private User bundle

A Symfony bundle to provide user management for private applications.

If you are building an internal application where you do not want anyone to be able to register, this bundle is for you.

Only authorized user can register to the application. An admin must configure a list of authorized email addresses. Then, when a user register, if its email address is not authorized, he won't be able to register.

The bundle is still being develop but can be used for simple usage.

## Configuration

The bundle can work without any configuration. It will create user table.

You can configure several type of users by defining a list of specific roles:

```yaml
# config/packages/vinorcola_private_user.yaml

vinorcola_private_user:
    types:
        user:
            roles: [ "ROLE_USER" ]
        admin:
            roles: [ "ROLE_USER", "ROLE_ADMIN" ]
```

By default (if no config is set), the only type available will be "user" with "ROLE_USER".

Because the bundle cannot configure the `security`, you have to add another config file with the following content:

```yaml
# config/packages/private_user.yaml

security:
    encoders:
        Vinorcola\PrivateUserBundle\Entity\User:
            algorithm: bcrypt
            cost: 12

    providers:
        main:
            id: Vinorcola\PrivateUserBundle\Security\UserProvider
```

Note that you can set the password encoder you want.

## Customize

The bundle provide simple template files, but it is recommended to copy them in order to customize them. Simply copy the folder `vendor/vinorcola/private-user-bundle/src/Resources/views` in `templates/bundles/VinorcolaPrivateUserBundle` and start modifying them.
