# [sqlshim] - PHP sqlsrv for Linux/OS X

The **sqlshim** project aims to replicate [Microsoft SQL Server Driver for PHP][sqlsrv] (**sqlsrv**) on Linux/OS X.

**sqlshim** was conceived out of the need for **[sqlsrv]** (which is a Windows-only PHP extension) in an OS X development environment. It began as a short script that defined a small set of functions.

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
1. `composer require --dev "radsectors/sqlshim:>0"`
2. Include autoloader.
```php
require 'vendor/autoload.php';
```

#### Manual
1. Download the latest [release](https://github.com/radsectors/sqlshim/releases).
2. Extract ```src/sqlshim.php``` and ```src/globals.php```.
3. Include it.
```php
require 'src/sqlshim.php';
```

#### Connection Setup
Please see [wiki article](https://github.com/radsectors/sqlshim/wiki/Connection-Setup).


## Testing
1. Rename ```config-sample.php``` to ```config.php```.
2. Install composer packages.
3. Run ```vendor/bin/phpunit -c phpunit.xml```


## Contributing
See [CONTRIBUTING.md](https://github.com/radsectors/sqlshim/blob/master/CONTRIBUTING.md) for instructions on how to contribute.

You can hit me up on twitter [@radsectors](https://twitter.com/radsectors).

## Known Issues
Please visit the [project on GitHub](https://github.com/radsectors/sqlshim) to view [outstanding issues](https://github.com/radsectors/sqlshim/issues).

## License
**sqlshim** is licensed under the MIT license. See the [LICENSE](https://github.com/radsectors/sqlshim/blob/master/LICENSE) file for details.

[sqlshim]: https://github.com/radsectors/sqlshim
[sqlsrv]: https://github.com/Azure/msphpsql "Microsoft SQL Server Driver for PHP"
