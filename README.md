# sqlshim
Provides replacement sqlsrv driver functions in PHP for Linux/OS X.


## Synopsis

sqlshim does not **yet** fully replace or duplicate ALL sqlsrv driver functionality. All of the functions have been defined, but only the most commonly used (in my own professional experiences) have been fleshed out.
Please remember that this software is in alpha stages and far from production-ready. It is provided as-is and without warranty. I am not responsible for any damages incurred from its use.


## Code Example

1. ```\RadSectors\Microshaft\SqlShim::init();```
2. ```sqlsrv_connect( ... );```
3. ???
4. Profit!!


## Motivation

sqlshim was born out of the need for [sqlsrv](http://php.net/manual/en/book.sqlsrv.php) compatibility in local non-Windows development environments.


## Installation
Note: most of the OS X instructions are theoretical as they have not been fully tested.

### Install FreeTDS

On Debian or Ubuntu:
```apt-get install freetds-bin``` or ```freetds-common```

On Mac OS X, using homebrew
```brew install freetds```


### Install PDO Driver

On Debian or Ubuntu:
```apt-get install php5-sybase```

On Mac OS X:
```brew install php5-pdo-dblib```

### Install ODBC

On Debian or Ubuntu:
```apt-get install unixodbc```

On Mac OS X:
```brew install unixodbc```


### Configure ODBC

1. Locate ```libtdsodbc.so``` and ```libtdsS.so```. Note: I think these have different names in the homebrew packages.

2. Copy the following into your odbcinst.ini (usually ```/etc/odbcinst.ini``` on Debian/Ubuntu and ```/usr/local/etc/odbcinst.ini``` on Mac OS X) file.

```ini
[ODBC Drivers]
FreeTDS = Installed

[FreeTDS]
Driver = /path/to/libtdsodbc.so
```

3. Then, set your ODBCINST environment variable to the location of your odbcinst.ini file.


## API Reference

[Microsoft SQL Server Driver for PHP](http://php.net/manual/en/book.sqlsrv.php)

[SQLSRV Functions](http://php.net/manual/en/ref.sqlsrv.php)

### Other reading

http://www.freetds.org/userguide/

http://lists.ibiblio.org/pipermail/freetds/2011q4/027555.html

http://dunglas.fr/2014/01/connection-to-a-ms-sql-server-from-symfony-doctrine-on-mac-or-linux/

http://www.acloudtree.com/how-to-install-freetds-and-unixodbc-on-osx-using-homebrew-for-use-with-ruby-php-and-perl/

https://msdn.microsoft.com/en-us/library/ff628167.aspx

http://forum.lazarus.freepascal.org/index.php?topic=24352.0


## Tests

Rename ```config-sample.php``` to ```config.php```.

```vendor/bin/phpunit -c phpunit.xml```


## Contributors

You can hit me up on twitter [@radsectors](https://twitter.com/radsectors).


## License

MIT
