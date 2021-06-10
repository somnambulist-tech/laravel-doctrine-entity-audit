Change Log
==========

2021-06-10
----------

Changed:

 * Fix reserved SQL keywords as column names are not escaped when building auditing queries

2021-06-03
----------

Changed:

 * Fix memory leak in `LogRevisionsListener` not clearing tracked entities internal array
 * Fix compound identity where clause in `LogRevisionsListener`

2021-05-19
----------

Changed:

 * Fix for missing types on join columns as suggested by [@Rezaldy](https://github.com/Rezaldy)

2021-05-18
----------

Changed:

 * Separated `UserResolver` into an interface with a default service implementation
 * Fixed type issue where `datetime` was hard-wired with date_create() in LogRevisionsListener

2021-05-03
----------

Changed:

 * Updated PHPUnit version and compatibility with PHPUnit 9.5
 * Added GitHub workflow for running tests

2017-09-02
----------

Changed:

 * Removed dependency on entity-behaviours as it is not needed
 * Updated PHPUnit to v6
 * Updated unit tests with no assertions

2017-02-11
----------

Changed:

 * Fixed bug with aliasing of the specific entity audit classes

2017-02-03
----------

Changed:

 * Updated dependencies for Laravel 5.4 / Laravel-Doctrine
 
2016-06-30
----------

Initial commit and conversion from SimpleThings.
