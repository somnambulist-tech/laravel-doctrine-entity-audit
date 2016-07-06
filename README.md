# Entity Auditing for Doctrine in Laravel

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
