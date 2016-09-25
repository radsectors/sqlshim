<?php

namespace
{
use radsectors\sqlshim;

if (!extension_loaded('sqlsrv')) {
    sqlshim::init();

    $ref = new \ReflectionClass('\radsectors\sqlshim');
    foreach ($ref->getConstants() as $const => $value) {
        define("SQLSRV_$const", $value);
    }

    // trying to dynamicize these functions,
    // but you can't really declare a function on the fly like this
    // w/o using eval() which may be okay...
    // the downside is that we lose docblocks...
    // foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $o) {
    //     if ($o->name == 'init') {
    //         continue;
    //     }
    //     echo "$o->name\n";
    // }
    // exit;

    /**
     * SQLSRV_PHPTYPE_STREAM.
     *
     * @param string $encoding
     *
     * @return int
     */
    function SQLSRV_PHPTYPE_STREAM($encoding)
    {
        return sqlshim::PHPTYPE_STREAM($encoding);
    }

    /**
     * SQLSRV_PHPTYPE_STRING.
     *
     * @param string $encoding
     *
     * @return int
     */
    function SQLSRV_PHPTYPE_STRING($encoding)
    {
        return sqlshim::PHPTYPE_STRING($encoding);
    }

    /**
     * SQLSRV_SQLTYPE_BINARY.
     *
     * @param int $byteCount
     *
     * @return int
     */
    function SQLSRV_SQLTYPE_BINARY($byteCount)
    {
        return sqlshim::SQLTYPE_BINARY($byteCount);
    }

    /**
     * SQLSRV_SQLTYPE_CHAR.
     *
     * @param int $charCount
     *
     * @return int
     */
    function SQLSRV_SQLTYPE_CHAR($charCount)
    {
        return sqlshim::SQLTYPE_CHAR($charCount);
    }

    /**
     * SQLSRV_SQLTYPE_DECIMAL.
     *
     * @todo figure out how $scale works into the equation.
     *
     * @param int $precision Precision
     * @param int $scale     Scale
     *
     * @return int
     */
    function SQLSRV_SQLTYPE_DECIMAL($precision, $scale)
    {
        return sqlshim::SQLTYPE_DECIMAL($precision, $scale);
    }

    /**
     * SQLSRV_SQLTYPE_NCHAR.
     *
     * @param int $charCount
     *
     * @return int
     */
    function SQLSRV_SQLTYPE_NCHAR($charCount)
    {
        return sqlshim::SQLTYPE_NCHAR($charCount);
    }

    /**
     * SQLSRV_SQLTYPE_NUMERIC.
     *
     * @todo figure out how $scale works into the equation.
     *
     * @param int $precision Precision
     * @param int $scale     Scale
     *
     * @return int
     */
    function SQLSRV_SQLTYPE_NUMERIC($precision, $scale)
    {
        return sqlshim::SQLTYPE_NUMERIC($precision, $scale);
    }

    /**
     * SQLSRV_SQLTYPE_NVARCHAR.
     *
     * @param int $charCount
     *
     * @return int
     */
    function SQLSRV_SQLTYPE_NVARCHAR($charCount)
    {
        return sqlshim::SQLTYPE_NVARCHAR($charCount);
    }

    /**
     * SQLSRV_SQLTYPE_VARBINARY.
     *
     * @param int $byteCount
     *
     * @return int
     */
    function SQLSRV_SQLTYPE_VARBINARY($byteCount)
    {
        return sqlshim::SQLTYPE_VARBINARY($byteCount);
    }

    /**
     * SQLSRV_SQLTYPE_VARCHAR.
     *
     * @param int $charCount
     *
     * @return int
     */
    function SQLSRV_SQLTYPE_VARCHAR($charCount)
    {
        return sqlshim::SQLTYPE_VARCHAR($charCount);
    }

    /**
     * Begins a database transaction.
     *
     * @param object $conn
     *
     * @return bool
     */
    function sqlsrv_begin_transaction($conn)
    {
        return sqlshim::begin_transaction($conn);
    }

    /**
     * Cancels a statement.
     *
     * @param object $stmt
     *
     * @return bool
     */
    function sqlsrv_cancel($stmt)
    {
        return sqlshim::cancel($stmt);
    }

    /**
     * Returns information about the client and specified connection.
     *
     * @param object $conn
     *
     * @return string[]
     */
    function sqlsrv_client_info($conn)
    {
        return sqlshim::client_info($conn);
    }

    /**
     * Closes an open connection and releases resourses associated with the connection.
     *
     * @param object $conn
     *
     * @return bool
     */
    function sqlsrv_close($conn)
    {
        return sqlshim::close($conn);
    }

    /**
     * Commits a transaction that was begun with sqlsrv_begin_transaction().
     *
     * @param object $conn
     *
     * @return bool
     */
    function sqlsrv_commit($conn)
    {
        return sqlshim::commit($conn);
    }

    /**
     * Changes the driver error handling and logging configurations.
     *
     * @param string $setting
     * @param mixed  $value
     *
     * @return bool
     */
    function sqlsrv_configure($setting, $value)
    {
        return sqlshim::configure($setting, $value);
    }

    /**
     * Opens a connection to a Microsoft SQL Server database.
     *
     * @param string $serverName
     * @param array  $connectionInfo
     *
     * @return \PDO
     */
    function sqlsrv_connect($serverName, $connectionInfo)
    {
        return sqlshim::connect($serverName, $connectionInfo);
    }

    /**
     * Returns error and warning information about the last SQLSRV operation performed.
     *
     * @param int $errorsOrWarnings
     *
     * @return array[]|null
     */
    function sqlsrv_errors($errorsOrWarnings = SQLSRV_ERR_ALL)
    {
        return sqlshim::errors($errorsOrWarnings);
    }

    /**
     * Executes a statement prepared with sqlsrv_prepare().
     *
     * @param object $stmt
     *
     * @return bool
     */
    function sqlsrv_execute($stmt)
    {
        return sqlshim::execute($stmt);
    }

    /**
     * Returns a row as an array.
     *
     * @param \PDOStatement $stmt
     * @param int           $fetchType
     * @param int           $row
     * @param int           $offset
     *
     * @return array|null
     */
    function sqlsrv_fetch_array($stmt, $fetchType = SQLSRV_FETCH_BOTH, $row = SQLSRV_SCROLL_NEXT, $offset = 0)
    {
        return sqlshim::fetch_array($stmt, $fetchType, $row, $offset);
    }

    /**
     * Retrieves the next row of data in a result set as an object.
     *
     * @param \PDOStatement $stmt
     * @param string        $className
     * @param array         $ctorParams
     * @param int           $row
     * @param int           $offset
     *
     * @return object|null|false
     */
    function sqlsrv_fetch_object($stmt, $className = 'stdClass', $ctorParams = [], $row = SQLSRV_SCROLL_NEXT, $offset = 0)
    {
        return sqlshim::fetch_object($stmt, $className, $ctorParams, $row, $offset);
    }

    /**
     * Makes the next row in a result set available for reading.
     *
     * @param \PDOStatement $stmt
     * @param int           $row
     * @param int           $offset
     *
     * @return bool|null
     */
    function sqlsrv_fetch($stmt, $row = SQLSRV_SCROLL_NEXT, $offset = 0)
    {
        return sqlshim::fetch($stmt, $row, $offset);
    }

    /**
     * Retrieves metadata for the fields of a statement prepared by sqlsrv_prepare() or sqlsrv_query().
     *
     * Retrieves metadata for the fields of a statement prepared by sqlsrv_prepare() or sqlsrv_query(). sqlsrv_field_metadata() can be called on a statement before or after statement execution.
     *
     * @param \PDOStatement $stmt The statment resource for which metadata is returned.
     *
     * @return array[]|false Returns an array of arrays on success. Otherwise, FALSE is returned.
     */
    function sqlsrv_field_metadata($stmt)
    {
        return sqlshim::field_metadata($stmt);
    }

    /**
     * Frees all resources for the specified statement.
     *
     * @param \PDOStatement $stmt
     *
     * @return bool
     */
    function sqlsrv_free_stmt($stmt)
    {
        return sqlshim::free_stmt($stmt);
    }

    /**
     * Returns the value of the specified configuration setting.
     *
     * @param string $setting
     *
     * @return mixed
     */
    function sqlsrv_get_config($setting)
    {
        return sqlshim::get_config($setting);
    }

    /**
     * Gets field data from the currently selected row.
     *
     * @param \PDOStatement $stmt
     * @param int           $fieldIndex
     * @param int           $getAsType
     *
     * @return mixed
     */
    function sqlsrv_get_field($stmt, $fieldIndex = 0, $getAsType = null)
    {
        return sqlshim::get_field($stmt, $fieldIndex = 0, $getAsType);
    }

    /**
     * Indicates whether the specified statement has rows.
     *
     * @param \PDOStatement $stmt
     *
     * @return bool
     */
    function sqlsrv_has_rows($stmt)
    {
        return sqlshim::has_rows($stmt);
    }

    /**
     * Makes the next result of the specified statement active.
     *
     * @param \PDOStatement $stmt
     *
     * @return bool|null
     */
    function sqlsrv_next_result($stmt)
    {
        return sqlshim::next_result($stmt);
    }

    /**
     * Retrieves the number of fields (columns) on a statement.
     *
     * @param \PDOStatement $stmt
     *
     * @return int|false
     */
    function sqlsrv_num_fields($stmt)
    {
        return sqlshim::num_fields($stmt);
    }

    /**
     * Retrieves the number of rows in a result set.
     *
     * @param \PDOStatement $stmt
     *
     * @return int|false
     */
    function sqlsrv_num_rows($stmt)
    {
        return sqlshim::num_rows($stmt);
    }

    /**
     * Prepares a query for execution.
     *
     * @param \PDO   $conn
     * @param string $sql
     * @param array  $params
     * @param array  $options
     *
     * @return \PDOStatement|false
     */
    function sqlsrv_prepare($conn, $sql, $params = [], $options = [])
    {
        return sqlshim::prepare($conn, $sql, $params, $options);
    }

    /**
     * Prepares and executes a query.
     *
     * @param \PDO   $conn
     * @param string $sql
     * @param array  $params
     * @param array  $options
     *
     * @return \PDOStatement|false
     */
    function sqlsrv_query($conn, $sql, $params = [], $options = [])
    {
        return sqlshim::query($conn, $sql, $params, $options);
    }

    /**
     * Rolls back a transaction that was begun with sqlsrv_begin_transaction().
     *
     * @param \PDO $conn
     *
     * @return bool
     */
    function sqlsrv_rollback($conn)
    {
        return sqlshim::rollback($conn);
    }

    /**
     * Returns the number of rows modified by the last INSERT, UPDATE, or DELETE query executed.
     *
     * @param \PDOStatement $stmt
     *
     * @return int|false
     */
    function sqlsrv_rows_affected($stmt)
    {
        return sqlshim::rows_affected($stmt);
    }

    /**
     * Sends data from parameter streams to the server.
     *
     * @param \PDOStatement $stmt
     *
     * @return bool
     */
    function sqlsrv_send_stream_data($stmt)
    {
        return sqlshim::send_stream_data($stmt);
    }

    /**
     * Returns information about the server.
     *
     * @param \PDO $conn
     *
     * @return string[]
     */
    function sqlsrv_server_info($conn)
    {
        return sqlshim::server_info($conn);
    }

    return true;
}
}
