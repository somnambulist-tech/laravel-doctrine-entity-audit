# Entity Auditing for Doctrine in Laravel

[![GitHub Actions Build Status](https://img.shields.io/github/actions/workflow/status/somnambulist-tech/laravel-doctrine-entity-audit/tests.yml?logo=github&branch=master)](https://github.com/somnambulist-tech/laravel-doctrine-entity-audit/actions?query=workflow%3Atests)
[![Issues](https://img.shields.io/github/issues/somnambulist-tech/laravel-doctrine-entity-audit?logo=github)](https://github.com/somnambulist-tech/laravel-doctrine-entity-audit/issues)
[![License](https://img.shields.io/github/license/somnambulist-tech/laravel-doctrine-entity-audit?logo=github)](https://github.com/somnambulist-tech/laravel-doctrine-entity-audit/blob/master/LICENSE)
[![PHP Version](https://img.shields.io/packagist/php-v/somnambulist/laravel-doctrine-entity-audit?logo=php&logoColor=white)](https://packagist.org/packages/somnambulist/laravel-doctrine-entity-audit)
[![Current Version](https://img.shields.io/packagist/v/somnambulist/laravel-doctrine-entity-audit?logo=packagist&logoColor=white)](https://packagist.org/packages/somnambulist/laravel-doctrine-entity-audit)

This is a fork of the SimpleThings EntityAudit project, re-worked for usage with Laravel
and the Laravel-Doctrine package. It maintains the same style of working, generating a
revision for all tagged entities.

Need a Symfony based version? Check out the original [SimpleThings EntityAudit](https://github.com/simplethings/EntityAudit).

To protect from conflicts it has been re-namespaced and some internals changed.

## Installation

 * composer require somnambulist/laravel-doctrine-entity-audit
 * add the ServiceProvider to your config/app.php after the Doctrine provider
 * ./artisan vendor:publish
 * update the config file and then generate the audit tables:
   * ./artisan doctrine:migrations:diff
   * ./artisan doctrine:migrations:migrate

### Configuring UserResolver

The UserResolver implementation may be switched out for another implementation by implementing the
`UserResolverInterface` and binding this to the container in your `AppServiceProvider`. This needs
to be during registration and not boot.

Alternatively: the `UserResolver` may be switched out at run time by accessing the `AuditConfiguration`
from the `AuditRegistry` for the entity manager and setting a new resolver instance:

```php
use Somnambulist\EntityAudit\AuditRegistry;

$app->get(AuditRegistry::class)->get(/* $emName */)->getConfiguration()->setUserResolver($resolver);
```

If the entity manager name is not specified, the default will be chosen. Note that the configuration
is shared with the `AuditReader`. It is recommended to set the UserResolver this way as early as
possible and preferably before loading and modifying any entities to avoid issues with user resolution.

## Unit Tests

The existing unit tests have been ported over excluding the Gedmo soft-deleteable.
To run the unit tests, ensure the dev dependencies have been installed:

 * vendor/bin/phpunit

## Todo

 * refactor and simplify the listener internals
 * remove any remaining deprecated Doctrine API calls

From SimpleThings:

 * currently only works with auto-increment databases
 * proper metadata mapping is necessary, allow to disable versioning for fields and associations.
 * support Joined-Table-Inheritance
 * support many-to-many auditing

## Links

 * [Laravel Doctrine](http://laraveldoctrine.org)
 * [Laravel](http://laravel.com)
 * [Doctrine](http://doctrine-project.org)
 * [SimpleThings EntityAudit](https://github.com/simplethings/EntityAudit)
