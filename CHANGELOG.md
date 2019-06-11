# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased][unreleased]
### Added
- Figure out a way to duplicate `sqlsrv_field_metadata()`.
- Figure out a way to duplicate `sqlsrv_cancel()`

### Changed
- Figure out how/why sqlsrv's PHPTYPE_STR(EAM|ING) function return values are
randomly different.
- PHPTYPE_DECIMAL and SQLTYPE_NUMERIC need fixing.
- Improve test modules.

### Deprecated
- odbc support

### Removed
- odbc support


## [0.0.10] - 2016-11-23
### Added
- `get_field()`
- Legit error now logged for invalid `configure()` and `get_config()` parameters.

### Changed
- Improved error logging. Maybe. I hope?
- Renamed some internal functions.
- Some PHPTYPE_* functions needed to be updated.
- Began deprecating odbc bits.

### Fixed
- ServerName:Port parsing.


## [0.0.9] - 2016-09-25
### Added
- `configure()` and `get_config()` functions.
- client info for dblib.

### Chanaged
- `init()` is now called automatically by `globals.php` which is now autoloaded
via composer. This will affect users who are using custom init config options
as well as those who `include()` manually.
- improved `server_info()`
- cleaned up some unnecessary static vars.

### Removed
- ODBC config options. they were never used or tested anyway.


## [0.0.8] - 2016-09-08
### Changed
- improved `guesstype()` function.

### Fixed
- some camelcasing

### Removed
- `convertDataType()` function.


## [0.0.7] - 2016-08-25
### Added
- dblib/sybase driver option (and made default) due to simpler setup.

### Changed
- lowercase'd sqlshim classname and radsectors namespace. not sure why I
camelcase'd them to begin with.
- made sqlshim class final.
- dynamicized the definition of SQLSRV constants.
- various other small updates and improvements.
- changed the way client_info retrieval functions are organized and accessed.

### Fixed
- bug where `prepare()` options were not being processed at all. oops.

### Removed
- ?-to-:tag conversion in prepare as it was highly unnecessary


## [0.0.4] - 2015-09-21
### Added
- option parsing for prepare()
- connection ref variable.

### Removed
- has_rows() and num_rows() were assumed to be working, but do not. no longer pretending.

### Fixed
- bug improperly handling invalid parameters.


## [0.0.3] - 2015-06-17
### Added
- Worked out some ugly logic for SQLTYPE_(DECIMAL|NUMERIC) functions.

### Changed
- Improved and optimized SQLTYPE functions. Reduced repeated code.


## [0.0.2] - 2015-06-09
### Added
- Logic to a few more functions.

### Changed
- Renamed class constants (removed SQLSRV_ prefixes.)
- Improved (I think) error logging function.

### Fixed
- A glaring bug where the fetch functions return false instead of null when
there are no records to fetch.


## [0.0.1] - 2015-05-28
### Added
- First alpha release.

[unreleased]: https://github.com/radsectors/sqlshim/compare/v0.0.10...HEAD
[0.0.10]: https://github.com/radsectors/sqlshim/compare/v0.0.9...v0.0.10
[0.0.9]: https://github.com/radsectors/sqlshim/compare/v0.0.8...v0.0.9
[0.0.8]: https://github.com/radsectors/sqlshim/compare/v0.0.7...v0.0.8
[0.0.7]: https://github.com/radsectors/sqlshim/compare/v0.0.4...v0.0.7
[0.0.4]: https://github.com/radsectors/sqlshim/compare/v0.0.3...v0.0.4
[0.0.3]: https://github.com/radsectors/sqlshim/compare/v0.0.2...v0.0.3
[0.0.2]: https://github.com/radsectors/sqlshim/compare/v0.0.1...v0.0.2
