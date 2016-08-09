# [sqlshim] - PHP sqlsrv for Linux/OS X

The **sqlshim** project aims to replicate [Microsoft SQL Server Driver for PHP][sqlsrv] (**sqlsrv**) on Linux/OS X.

**sqlshim** was conceived out of the need for **[sqlsrv]** (a Windows-only PHP extension) in an OS X development environment. It began as a short script that defined a small set of functions. Unfortunately,

**DISCLAIMER:** **sqlshim** is in alpha and is geared primarily toward use in a development environment. It is provided as-is and without warranty. I will not be held responsible for any damage(s) incurred from its use.


## Basic Usage
1. ```\RadSectors\SqlShim::init();```
2. ```sqlsrv_connect( ... );```
3. ???
4. Profit!!


## Documentation
The project's aim is to completely and accurately replicate the provisions of **sqlsrv**. Any official **sqlsrv** documentation should suffice.

[Microsoft SQL Server Driver - PHP.net](http://php.net/manual/en/book.sqlsrv.php)

[SQLSRV Functions - PHP.net](http://php.net/manual/en/ref.sqlsrv.php)

[SQLSRV Driver API Reference - MSDN](https://msdn.microsoft.com/en-us/library/cc296152.aspx)


## Installation

#### Composer
1. `composer require "radsectors/sqlshim:dev-master"`
2. Include autoloader.
```php
require 'vendor/autoload.php';
```

#### Manual
1. Download the latest [release](https://github.com/radsectors/sqlshim/releases).
2. Extract ```src/sqlshim.php``` and ```src/globals.php``` to wherever you store your 3rd-party libraries.
3. Include `sqlshim.php`.
```php
require '/path/to/sqlshim.php';
```

#### 3rd-party Lib Setup
Please see [wiki article](https://github.com/radsectors/sqlshim/wiki/3rd‑party-Lib-Setup).

#### Connection Setup
Please see PHP.net's article on [sqlsrv_connect()](http://php.net/manual/en/function.sqlsrv-connect.php)

## Testing
Please see [wiki article](https://github.com/radsectors/sqlshim/wiki/Unit-Testing)


## Contributing
See [CONTRIBUTING.md](https://github.com/radsectors/sqlshim/blob/master/CONTRIBUTING.md) for instructions on how to contribute.

You can hit me up on twitter [@radsectors](https://twitter.com/radsectors).

## Known Issues
Please visit the [project on GitHub](https://github.com/radsectors/sqlshim) to view [outstanding issues](https://github.com/radsectors/sqlshim/issues).

## License
**sqlshim** is licensed under the MIT license. See the [LICENSE](https://github.com/radsectors/sqlshim/blob/master/LICENSE) file for details.

[sqlshim]: https://github.com/radsectors/sqlshim
[sqlsrv]: https://github.com/Azure/msphpsql "Microsoft SQL Server Driver for PHP"

#DBLIB Based Version (dexterfichuk)
## Intro
I've been using this for awhile and performed some changes to the original one. The original copy is indeed great the way it is for some, but this one has some significant changes. Below are a few of the fundamental changes, but I've done other various tweaks to get it up and working for myself.

## Changes
- Changed from the ODBC PDO driver to use DBLIB, couldn't connect with ODBC
- Rewrote the parameter function, as it wouldn't pass in any parameters for me.
- Had to rewrite a converter for datatypes. With DBLIB connections everything is returned as a string, so this will fix numbers and bool's into the proper format. 
- May now be dependent on a few more packages
- Forces the use of FreeTDS 7.2

## Benefits
- The original sqlshim can't retrieve the "text" datatype, and any query which calls a text type field will be cut off and return null fields thereafter. The DBLIB driver however does not have this problem.
- Parameters work whereas they did not for me prior.
- Stored parameters always work because of forced FreeTDS version.

## Issues
- Retrieves dates as strings
- Weird memory leak errors if using on PHP 5.5.19 or lower. I recommend using PHP 5.6.23. The warnings do not affect performance and are just a PHP bug if you remain on a lower PHP version.
