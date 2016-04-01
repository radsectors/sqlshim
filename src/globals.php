<?php
namespace
{
  use \RadSectors\SqlShim;

  if ( !extension_loaded('sqlsrv') )
  {
    define('SQLSRV_FETCH_NUMERIC', SqlShim::FETCH_NUMERIC);
    define('SQLSRV_FETCH_ASSOC', SqlShim::FETCH_ASSOC);
    define('SQLSRV_FETCH_BOTH', SqlShim::FETCH_BOTH);

    define('SQLSRV_ERR_ERRORS', SqlShim::ERR_ERRORS);
    define('SQLSRV_ERR_WARNINGS', SqlShim::ERR_WARNINGS);
    define('SQLSRV_ERR_ALL', SqlShim::ERR_ALL);

    define('SQLSRV_LOG_SYSTEM_ALL', SqlShim::LOG_SYSTEM_ALL);
    define('SQLSRV_LOG_SYSTEM_CONN', SqlShim::LOG_SYSTEM_CONN);
    define('SQLSRV_LOG_SYSTEM_INIT', SqlShim::LOG_SYSTEM_INIT);
    define('SQLSRV_LOG_SYSTEM_OFF', SqlShim::LOG_SYSTEM_OFF);
    define('SQLSRV_LOG_SYSTEM_STMT', SqlShim::LOG_SYSTEM_STMT);
    define('SQLSRV_LOG_SYSTEM_UTIL', SqlShim::LOG_SYSTEM_UTIL);

    define('SQLSRV_LOG_SEVERITY_ALL', SqlShim::LOG_SEVERITY_ALL);
    define('SQLSRV_LOG_SEVERITY_ERROR', SqlShim::LOG_SEVERITY_ERROR);
    define('SQLSRV_LOG_SEVERITY_NOTICE', SqlShim::LOG_SEVERITY_NOTICE);
    define('SQLSRV_LOG_SEVERITY_WARNING', SqlShim::LOG_SEVERITY_WARNING);

    define('SQLSRV_NULLABLE_YES', SqlShim::NULLABLE_YES);
    define('SQLSRV_NULLABLE_NO', SqlShim::NULLABLE_NO);
    define('SQLSRV_NULLABLE_UNKNOWN', SqlShim::NULLABLE_UNKNOWN);

    define('SQLSRV_PARAM_IN', SqlShim::PARAM_IN);
    define('SQLSRV_PARAM_INOUT', SqlShim::PARAM_INOUT);
    define('SQLSRV_PARAM_OUT', SqlShim::PARAM_OUT);

    define('SQLSRV_PHPTYPE_INT', SqlShim::PHPTYPE_INT);
    define('SQLSRV_PHPTYPE_DATETIME', SqlShim::PHPTYPE_DATETIME);
    define('SQLSRV_PHPTYPE_FLOAT', SqlShim::PHPTYPE_FLOAT);
    define('SQLSRV_PHPTYPE_NULL', SqlShim::PHPTYPE_NULL);

    define('SQLSRV_ENC_BINARY', SqlShim::ENC_BINARY);
    define('SQLSRV_ENC_CHAR', SqlShim::ENC_CHAR);

    define('SQLSRV_SQLTYPE_BIGINT', SqlShim::SQLTYPE_BIGINT);
    define('SQLSRV_SQLTYPE_BIT', SqlShim::SQLTYPE_BIT);
    define('SQLSRV_SQLTYPE_DATE', SqlShim::SQLTYPE_DATE);
    define('SQLSRV_SQLTYPE_DATETIME', SqlShim::SQLTYPE_DATETIME);
    define('SQLSRV_SQLTYPE_DATETIME2', SqlShim::SQLTYPE_DATETIME2);
    define('SQLSRV_SQLTYPE_DATETIMEOFFSET', SqlShim::SQLTYPE_DATETIMEOFFSET);
    define('SQLSRV_SQLTYPE_FLOAT', SqlShim::SQLTYPE_FLOAT);
    define('SQLSRV_SQLTYPE_IMAGE', SqlShim::SQLTYPE_IMAGE);
    define('SQLSRV_SQLTYPE_INT', SqlShim::SQLTYPE_INT);
    define('SQLSRV_SQLTYPE_MONEY', SqlShim::SQLTYPE_MONEY);
    define('SQLSRV_SQLTYPE_NTEXT', SqlShim::SQLTYPE_NTEXT);
    define('SQLSRV_SQLTYPE_REAL', SqlShim::SQLTYPE_REAL);
    define('SQLSRV_SQLTYPE_SMALLDATETIME', SqlShim::SQLTYPE_SMALLDATETIME);
    define('SQLSRV_SQLTYPE_SMALLINT', SqlShim::SQLTYPE_SMALLINT);
    define('SQLSRV_SQLTYPE_SMALLMONEY', SqlShim::SQLTYPE_SMALLMONEY);
    define('SQLSRV_SQLTYPE_TEXT', SqlShim::SQLTYPE_TEXT);
    define('SQLSRV_SQLTYPE_TIME', SqlShim::SQLTYPE_TIME);
    define('SQLSRV_SQLTYPE_TIMESTAMP', SqlShim::SQLTYPE_TIMESTAMP);
    define('SQLSRV_SQLTYPE_TINYINT', SqlShim::SQLTYPE_TINYINT);
    define('SQLSRV_SQLTYPE_UNIQUEIDENTIFIER', SqlShim::SQLTYPE_UNIQUEIDENTIFIER);
    define('SQLSRV_SQLTYPE_UDT', SqlShim::SQLTYPE_UDT);
    define('SQLSRV_SQLTYPE_XML', SqlShim::SQLTYPE_XML);

    define('SQLSRV_TXN_READ_UNCOMMITTED', SqlShim::TXN_READ_UNCOMMITTED);
    define('SQLSRV_TXN_READ_COMMITTED', SqlShim::TXN_READ_COMMITTED);
    define('SQLSRV_TXN_REPEATABLE_READ', SqlShim::TXN_REPEATABLE_READ);
    define('SQLSRV_TXN_SNAPSHOT', SqlShim::TXN_SNAPSHOT);
    define('SQLSRV_TXN_SERIALIZABLE', SqlShim::TXN_SERIALIZABLE);

    define('SQLSRV_CURSOR_FORWARD', SqlShim::CURSOR_FORWARD);
    define('SQLSRV_CURSOR_STATIC', SqlShim::CURSOR_STATIC);
    define('SQLSRV_CURSOR_DYNAMIC', SqlShim::CURSOR_DYNAMIC);
    define('SQLSRV_CURSOR_KEYSET', SqlShim::CURSOR_KEYSET);
    define('SQLSRV_CURSOR_CLIENT_BUFFERED', SqlShim::CURSOR_CLIENT_BUFFERED);

    define('SQLSRV_SCROLL_NEXT', SqlShim::SCROLL_NEXT);
    define('SQLSRV_SCROLL_FIRST', SqlShim::SCROLL_FIRST);
    define('SQLSRV_SCROLL_LAST', SqlShim::SCROLL_LAST);
    define('SQLSRV_SCROLL_PRIOR', SqlShim::SCROLL_PRIOR);
    define('SQLSRV_SCROLL_ABSOLUTE', SqlShim::SCROLL_ABSOLUTE);
    define('SQLSRV_SCROLL_RELATIVE', SqlShim::SCROLL_RELATIVE);


    /**
     * SQLSRV_PHPTYPE_STREAM
     *
     * @param string $encoding
     * @return integer
     */
    function SQLSRV_PHPTYPE_STREAM( $encoding )
    {
      return SqlShim::PHPTYPE_STREAM($encoding);
    }

    /**
     * SQLSRV_PHPTYPE_STRING
     *
     * @param string $encoding
     * @return integer
     */
    function SQLSRV_PHPTYPE_STRING( $encoding )
    {
      return SqlShim::PHPTYPE_STRING($encoding);
    }

    /**
     * SQLSRV_SQLTYPE_BINARY
     *
     * @param integer $byteCount
     * @return integer
     */
    function SQLSRV_SQLTYPE_BINARY( $byteCount )
    {
      return SqlShim::SQLTYPE_BINARY($byteCount);
    }

    /**
     * SQLSRV_SQLTYPE_CHAR
     *
     * @param integer $charCount
     * @return integer
     */
    function SQLSRV_SQLTYPE_CHAR( $charCount )
    {
      return SqlShim::SQLTYPE_CHAR($charCount);
    }

    /**
     * SQLSRV_SQLTYPE_DECIMAL
     * @todo figure out how $scale works into the equation.
     * @param integer $precision Precision
     * @param integer $scale Scale
     * @return integer
     */
    function SQLSRV_SQLTYPE_DECIMAL( $precision, $scale )
    {
      return SqlShim::SQLTYPE_DECIMAL($precision, $scale);
    }

    /**
     * SQLSRV_SQLTYPE_NCHAR
     *
     * @param integer $charCount
     * @return integer
     */
    function SQLSRV_SQLTYPE_NCHAR( $charCount )
    {
      return SqlShim::SQLTYPE_NCHAR($charCount);
    }

    /**
     * SQLSRV_SQLTYPE_NUMERIC
     * @todo figure out how $scale works into the equation.
     * @param integer $precision Precision
     * @param integer $scale Scale
     * @return integer
     */
    function SQLSRV_SQLTYPE_NUMERIC( $precision, $scale )
    {
      return SqlShim::SQLTYPE_NUMERIC($precision, $scale);
    }

    /**
     * SQLSRV_SQLTYPE_NVARCHAR
     *
     * @param integer $charCount
     * @return integer
     */
    function SQLSRV_SQLTYPE_NVARCHAR( $charCount )
    {
      return SqlShim::SQLTYPE_NVARCHAR($charCount);
    }

    /**
     * SQLSRV_SQLTYPE_VARBINARY
     *
     * @param integer $byteCount
     * @return integer
     */
    function SQLSRV_SQLTYPE_VARBINARY( $byteCount )
    {
      return SqlShim::SQLTYPE_VARBINARY($byteCount);
    }

    /**
     * SQLSRV_SQLTYPE_VARCHAR
     *
     * @param integer $charCount
     * @return integer
     */
    function SQLSRV_SQLTYPE_VARCHAR( $charCount )
    {
      return SqlShim::SQLTYPE_VARCHAR($charCount);
    }

    /**
     * Begins a database transaction
     *
     * @param object $conn
     * @return boolean
     */
    function sqlsrv_begin_transaction( $conn )
    {
      return SqlShim::begin_transaction($conn);
    }

    /**
     * Cancels a statement
     *
     * @param object $stmt
     * @return boolean
     */
    function sqlsrv_cancel( $stmt )
    {
      return SqlShim::cancel($stmt);
    }

    /**
     * Returns information about the client and specified connection
     *
     * @param object $conn
     * @return string[]
     */
    function sqlsrv_client_info( $conn )
    {
      return SqlShim::client_info($conn);
    }

    /**
     * Closes an open connection and releases resourses associated with the connection
     *
     * @param object $conn
     * @return boolean
     */
    function sqlsrv_close( $conn )
    {
      return SqlShim::close($conn);
    }

    /**
     * Commits a transaction that was begun with sqlsrv_begin_transaction()
     *
     * @param object $conn
     * @return boolean
     */
    function sqlsrv_commit( $conn )
    {
      return SqlShim::commit($conn);
    }

    /**
     * Changes the driver error handling and logging configurations
     *
     * @param string $setting
     * @param mixed $value
     * @return boolean
     */
    function sqlsrv_configure( $setting, $value )
    {
      return SqlShim::configure($setting, $value);
    }

    /**
     * Opens a connection to a Microsoft SQL Server database
     *
     * @param string $serverName
     * @param array $connectionInfo
     * @return \PDO
     */
    function sqlsrv_connect( $serverName, $connectionInfo )
    {
      return SqlShim::connect($serverName, $connectionInfo);
    }

    /**
     * Returns error and warning information about the last SQLSRV operation performed
     *
     * @param integer $errorsOrWarnings
     * @return array[]|null
     */
    function sqlsrv_errors( $errorsOrWarnings=SQLSRV_ERR_ALL )
    {
      return SqlShim::errors($errorsOrWarnings);
    }

    /**
     * Executes a statement prepared with sqlsrv_prepare()
     *
     * @param object $stmt
     * @return boolean
     */
    function sqlsrv_execute( $stmt )
    {
      return SqlShim::execute($stmt);
    }

    /**
     * Returns a row as an array
     *
     * @param \PDOStatement $stmt
     * @param integer $fetchType
     * @param integer $row
     * @param integer $offset
     * @return array|null
     */
    function sqlsrv_fetch_array( $stmt, $fetchType=SQLSRV_FETCH_BOTH, $row=SQLSRV_SCROLL_NEXT, $offset=0 )
    {
      return SqlShim::fetch_array($stmt, $fetchType, $row, $offset);
    }

    /**
     * Retrieves the next row of data in a result set as an object
     *
     * @param \PDOStatement $stmt
     * @param string $className
     * @param array $ctorParams
     * @param integer $row
     * @param integer $offset
     * @return object|null|false
     */
    function sqlsrv_fetch_object( $stmt, $className='stdClass', $ctorParams=[], $row=SQLSRV_SCROLL_NEXT, $offset=0 )
    {
      return SqlShim::fetch_object($stmt, $className, $ctorParams, $row, $offset);
    }

    /**
     * Makes the next row in a result set available for reading
     *
     * @param \PDOStatement $stmt
     * @param integer $row
     * @param integer $offset
     * @return boolean|null
     */
    function sqlsrv_fetch( $stmt, $row=SQLSRV_SCROLL_NEXT, $offset=0 )
    {
      return SqlShim::fetch($stmt, $row, $offset);
    }

    /**
     * Retrieves metadata for the fields of a statement prepared by sqlsrv_prepare() or sqlsrv_query()
     *
     * Retrieves metadata for the fields of a statement prepared by sqlsrv_prepare() or sqlsrv_query(). sqlsrv_field_metadata() can be called on a statement before or after statement execution.
     *
     * @param \PDOStatement $stmt The statment resource for which metadata is returned.
     * @return array[]|false Returns an array of arrays on success. Otherwise, FALSE is returned.
     */
    function sqlsrv_field_metadata( $stmt )
    {
      return SqlShim::field_metadata($stmt);
    }

    /**
     * Frees all resources for the specified statement
     *
     * @param \PDOStatement $stmt
     * @return boolean
     */
    function sqlsrv_free_stmt( $stmt )
    {
      return SqlShim::free_stmt($stmt);
    }

    /**
     * Returns the value of the specified configuration setting
     *
     * @param string $setting
     * @return mixed
     */
    function sqlsrv_get_config( $setting )
    {
      return SqlShim::get_config($setting);
    }

    /**
     * Gets field data from the currently selected row
     *
     * @param \PDOStatement $stmt
     * @param integer $fieldIndex
     * @param integer $getAsType
     * @return mixed
     */
    function sqlsrv_get_field( $stmt, $fieldIndex=0, $getAsType=null )
    {
      return SqlShim::get_field($stmt, $fieldIndex=0, $getAsType);
    }

    /**
     * Indicates whether the specified statement has rows
     *
     * @param \PDOStatement $stmt
     * @return boolean
     */
    function sqlsrv_has_rows( $stmt )
    {
      return SqlShim::has_rows($stmt);
    }

    /**
     * Makes the next result of the specified statement active
     *
     * @param \PDOStatement $stmt
     * @return boolean|null
     */
    function sqlsrv_next_result( $stmt )
    {
      return SqlShim::next_result($stmt);
    }

    /**
     * Retrieves the number of fields (columns) on a statement
     *
     * @param \PDOStatement $stmt
     * @return integer|false
     */
    function sqlsrv_num_fields( $stmt )
    {
      return SqlShim::num_fields($stmt);
    }

    /**
     * Retrieves the number of rows in a result set
     *
     * @param \PDOStatement $stmt
     * @return integer|false
     */
    function sqlsrv_num_rows( $stmt )
    {
      return SqlShim::num_rows($stmt);
    }

    /**
     * Prepares a query for execution
     *
     * @param \PDO $conn
     * @param string $sql
     * @param array $params
     * @param array $options
     * @return \PDOStatement|false
     */
    function sqlsrv_prepare( $conn, $sql, $params=[], $options=[] )
    {
      return SqlShim::prepare($conn, $sql, $params, $options);
    }

    /**
     * Prepares and executes a query.
     *
     * @param \PDO $conn
     * @param string $sql
     * @param array $params
     * @param array $options
     * @return \PDOStatement|false
     */
    function sqlsrv_query( $conn, $sql, $params=[], $options=[] )
    {
      return SqlShim::query($conn, $sql, $params, $options);
    }

    /**
     * Rolls back a transaction that was begun with sqlsrv_begin_transaction()
     *
     * @param \PDO $conn
     * @return boolean
     */
    function sqlsrv_rollback( $conn )
    {
      return SqlShim::rollback($conn);
    }

    /**
     * Returns the number of rows modified by the last INSERT, UPDATE, or DELETE query executed
     *
     * @param \PDOStatement $stmt
     * @return integer|false
     */
    function sqlsrv_rows_affected( $stmt )
    {
      return SqlShim::rows_affected($stmt);
    }

    /**
     * Sends data from parameter streams to the server
     *
     * @param \PDOStatement $stmt
     * @return boolean
     */
    function sqlsrv_send_stream_data( $stmt )
    {
      return SqlShim::send_stream_data($stmt);
    }

    /**
     * Returns information about the server
     *
     * @param \PDO $conn
     * @return string[]
     */
    function sqlsrv_server_info( $conn )
    {
      return SqlShim::server_info($conn);
    }

    return true;
  }
}
