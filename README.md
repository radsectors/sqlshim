# sqlshim
Provides replacement sqlsrv driver functions for Linux/OS X.


## Synopsis

This shim does NOT (yet?) replace or duplicate ALL sqlsrv driver functionality.
All of the functions are there, but only the most common have been fully(?)
implemented. Exercise caution and DO NOT use in a propduction environment.


## Code Example

1. Instantiate object...
2. sqlsrv_connect();
3. ???
4. Profit!!


## Motivation

sqlshim was born out of the need for <http://php.net/manual/en/ref.sqlsrv.php>
compatibility in local non-Windows development environments.


## Installation
Most of the OS X instructions are theoretical as they have not been fully
tested.

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

1. Locate ```libtdsodbc.so``` and ```libtdsS.so```

2. Copy the following into your odbcinst.ini (usually ```/etc/odbcinst.ini``` on
Debian/Ubuntu and ```/usr/local/etc/odbcinst.ini``` on Mac OS X) file.

```ini
[ODBC Drivers]
FreeTDS = Installed

[FreeTDS]
Description = FreeTDS driver
Driver = /path/to/libtdsodbc.so
Setup = /path/to/libtdsS.so
FileUsage = 1
UsageCount = 1
```

3. Then, set your ODBCINST environment variable to the location of your
odbcinst.ini file.


## API Reference

Depending on the size of the project, if it is small and simple enough the
reference docs can be added to the README. For medium size to larger projects it
 is important to at least provide a link to where the API reference docs live.

## Tests

Describe and show how to run the tests with code examples.

## Contributors

Let people know how they can dive into the project, include important links to
things like issue trackers, irc, twitter accounts if applicable.

## License

MIT