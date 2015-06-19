# [sqlshim] - PHP sqlsrv for Linux/OS X

The **sqlshim** project aims to replicate [Microsoft SQL Server Driver for PHP][sqlsrv] (**sqlsrv**) on Linux/OS X.

**sqlshim** was conceived out of the need for **[sqlsrv]** (which is a Windows-only PHP extension) in an OS X development environment. It began as a short script that defined a small set of functions.

**sqlshim** is in alpha stages. It is is provided as-is and without warranty. I am not responsible for any damage(s) incurred from its use.


## Usage
1. ```\RadSectors\SqlShim::init();```
2. ```sqlsrv_connect( ... );```
3. ???
4. Profit!!


## API Reference
The project's aim is to completely and accurately replicate the provisions of [Microsoft SQL Server Driver for PHP](https://github.com/Azure/msphpsql). The official documentation should be all that you need.

[Microsoft SQL Server Driver for PHP - PHP.net](http://php.net/manual/en/book.sqlsrv.php)

[SQLSRV Functions - PHP.net](http://php.net/manual/en/ref.sqlsrv.php)

[SQLSRV Driver API Reference - MSDN](https://msdn.microsoft.com/en-us/library/cc296152.aspx)


## Installation
### Manual
1. Download the latest [release](https://github.com/radsectors/sqlshim/releases).
2. Extract ```src/sqlshim.php``` and ```src/globals.php```.
3. ```include 'src/sqlshim.php';```

### Composer
**sqlshim** isn't on packagist yet, so please follow the manual instructions above.

### Connection Setup
Note: OS X instructions are essentially theoretical as they have not been well-tested.

##### Install FreeTDS
Aptitude: ```apt-get isntall freetds-common```<br>
Yum:      ```yum install freetds```<br>
Macports: ```port install freetds```<br>
Homebrew: ```brew install freetds```<br>

##### Install PDO for PHP
Aptitude: ```apt-get install php5-sybase```<br>
Yum:      ```yum install php-pdo```<br>
Macports: ```port install php5*-?```<br>
Homebrew: ```brew install php5-pdo-dblib```<br>

##### Install ODBC
Aptitude: ```apt-get install unixodbc```<br>
Yum:      ```yum install unixodbc```<br>
Macports: ```port install unixodbc```<br>
Homebrew: ```brew install unixodbc```<br>

##### Configure ODBC
1. Locate ```libtdsodbc.so``` and ```libtdsS.so```. Note: I think these have different names in the homebrew packages.

2. Copy the following into your odbcinst.ini (usually ```/etc/odbcinst.ini``` on Debian/Ubuntu and ```/usr/local/etc/odbcinst.ini``` on Mac OS X) file.

```ini
[ODBC Drivers]
FreeTDS = Installed

[FreeTDS]
Driver = /path/to/libtdsodbc.so
```


## Tests
1. Rename ```config-sample.php``` to ```config.php```.
2. Install composer packages.
3. Run ```vendor/bin/phpunit -c phpunit.xml```


## Contributing
See [CONTRIBUTING.md](https://github.com/radsectors/sqlshim/blob/master/CONTRIBUTING.mb) for instructions on how to contribute.

You can hit me up on twitter [@radsectors](https://twitter.com/radsectors).

## Known Issues
Please visit the [project on GitHub](https://github.com/radsectors/sqlshim) to view [outstanding issues](https://github.com/radsectors/sqlshim/issues).

## License
**sqlshim** is licensed under the MIT license. See the [LICENSE](https://github.com/radsectors/sqlshim/blob/master/LICENSE) file for details.

[sqlshim]: https://github.com/radsectors/sqlshim
[sqlsrv]: https://github.com/Azure/msphpsql "Microsoft SQL Server Driver for PHP"