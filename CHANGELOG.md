# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased][unreleased]
### Added
- Figure out a way to duplicate sqlsrv_field_metadata().
- Figure out a way to duplicate sqlsrv_cancel()

### Changed
- Make client_info() less reliant on external utils.
- Figure out how/why sqlsrv's PHPTYPE_STR(EAM|ING) function return values are randomly different. Though, when they are different, they are always the same different values.
- figure out how to retrieve client_info for dblib. test odbc functions... those utils may not be installed with unixodbc.

## [0.0.7] - 2016-08-25
### Added
- dblib/sybase driver option (and made default) due to simpler setup.

### Changed
- lowercase'd sqlshim classname and radsectors namespace. not sure why I camelcase'd them to begin with.
- made sqlshim class final.
- dynamicized the definition of SQLSRV constants.
- various other small updates and improvements.
- changed the way client_info retrieval functions are organized and accessed.

### Fixed
- bug where prepare() options were not being processed at all. oops.

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
- A glaring bug where the fetch functions return false instead of null when there are no records to fetch.

## [0.0.1] - 2015-05-28
### Added
- First alpha release.

[unreleased]: https://github.com/radsectors/sqlshim/compare/v0.0.4...HEAD
[0.0.7]: https://github.com/radsectors/sqlshim/compare/v0.0.4...v0.0.7
[0.0.4]: https://github.com/radsectors/sqlshim/compare/v0.0.3...v0.0.4
[0.0.3]: https://github.com/radsectors/sqlshim/compare/v0.0.2...v0.0.3
[0.0.2]: https://github.com/radsectors/sqlshim/compare/v0.0.1...v0.0.2
