# **sqlshim** &bull; PHP sqlsrv for Linux/OS X

The **sqlshim** project aims to replicate [Microsoft SQL Server Driver for PHP][sqlsrv] (**sqlsrv**) on Linux/OS X.

**sqlshim** was born out of the need for **[sqlsrv]** functions in OS X. It started as a short script with a small set of functions and has since evolved into a nearly full replacement for **[sqlsrv]**.

**DISCLAIMER:** **sqlshim** is in alpha and is geared primarily toward use in a development environment. It is provided as-is and without warranty. I will not be held responsible for any damage(s) incurred from its use.<br>
Also, please note that despite its Mac origins, access to a proper Mac test environment has been limited (nonexistent) so I can't presently make any promises to Mac users.


## Basic Usage
1. `sqlsrv_connect( ... );`
2. ???
3. Profit!!


## Documentation
For more detailed documentation on **sqlshim**, please see the [sqlshim wiki](https://github.com/radsectors/sqlshim/wiki)


## Installation
See [wiki] for more detailed instructions.

#### Composer (recommended)
1. `composer require "radsectors/sqlshim:dev-master"`
2. Include autoloader.
```php
require 'vendor/autoload.php';
```

#### Manual
1. Download the latest [release](https://github.com/radsectors/sqlshim/releases).
2. Extract `src/sqlshim.php` and `src/globals.php` to wherever you store your 3rd-party libraries.
3. Include `globals.php`.
```php
require '/path/to/sqlshim.php';
require '/path/to/globals.php';
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

## Credits

## License
**sqlshim** is licensed under the MIT license. See the [LICENSE](https://github.com/radsectors/sqlshim/blob/master/LICENSE) file for details.

[sqlshim]: https://github.com/radsectors/sqlshim
[sqlsrv]: https://github.com/Azure/msphpsql "Microsoft SQL Server Driver for PHP"
