<?php
namespace { // global namespace

  use RadSectors\Microshaft\SqlShim;

  if ( !function_exists('sqlsrv_connect') )
  {
    define('SQLSRV_FETCH_NUMERIC', 1);
    define('SQLSRV_FETCH_ASSOC', 2);
    define('SQLSRV_FETCH_BOTH', 3);

    define('SQLSRV_ERR_ERRORS', 0);
    define('SQLSRV_ERR_WARNINGS', 1);
    define('SQLSRV_ERR_ALL', 2);

    define('SQLSRV_LOG_SYSTEM_ALL', -1);
    define('SQLSRV_LOG_SYSTEM_CONN', 2);
    define('SQLSRV_LOG_SYSTEM_INIT', 1);
    define('SQLSRV_LOG_SYSTEM_OFF', 0);
    define('SQLSRV_LOG_SYSTEM_STMT', 4);
    define('SQLSRV_LOG_SYSTEM_UTIL', 8);

    define('SQLSRV_LOG_SEVERITY_ALL', -1);
    define('SQLSRV_LOG_SEVERITY_ERROR', 1);
    define('SQLSRV_LOG_SEVERITY_NOTICE', 4);
    define('SQLSRV_LOG_SEVERITY_WARNING', 2);

    define('SQLSRV_NULLABLE_YES', 1);
    define('SQLSRV_NULLABLE_NO', 0);
    define('SQLSRV_NULLABLE_UNKNOWN', 2);

    define('SQLSRV_PARAM_IN', 1);
    define('SQLSRV_PARAM_INOUT', 2);
    define('SQLSRV_PARAM_OUT', 0);

    define('SQLSRV_PHPTYPE_INT', 2);
    define('SQLSRV_PHPTYPE_DATETIME', 5);
    define('SQLSRV_PHPTYPE_FLOAT', 3);
    function SQLSRV_PHPTYPE_STREAM( $encoding ) {
      switch ( strval($encoding) )
      {
        case 'binary':
          $return = 518;
          break;
        case 'char':
          $return = 774;
          break;
        default:
          $return = 6;
          break;
      }
      return $return;
    }
    function SQLSRV_PHPTYPE_STRING( $encoding ) {
      switch ( strval($encoding) )
      {
        case 'binary':
          $return = 33554948;
          break;
        case 'char':
          $return = 33555204;
          break;
        default:
          $return = 33554436;
          break;
      }
      return $return;
    }

    define('SQLSRV_ENC_BINARY', 'binary');
    define('SQLSRV_ENC_CHAR', 'char');

    define('SQLSRV_SQLTYPE_BIGINT', -5);
    function SQLSRV_SQLTYPE_BINARY( $byteCount ) {
      $bc = intval($byteCount);
      switch ( true )
      {
        case $bc>0 && $bc<8001:
          $return = ($bc*512)+SqlShim::MAGIC_NUM_BINARY;
          break;
        case $bc===0:
        default:
          $return = 2147483134;
      }
      return $return;
    }
    define('SQLSRV_SQLTYPE_BIT', -7);
    // define('SQLSRV_SQLTYPE_CHAR', 0);
    define('SQLSRV_SQLTYPE_DATE', 5211);
    define('SQLSRV_SQLTYPE_DATETIME', 25177693);
    define('SQLSRV_SQLTYPE_DATETIME2', 58734173);
    define('SQLSRV_SQLTYPE_DATETIMEOFFSET', 58738021);
    function SQLSRV_SQLTYPE_DECIMAL( $precision, $scale ) {
      // @TODO: figure out how to calculate return value;
      $return = 0;
      return $return;
    }
    define('SQLSRV_SQLTYPE_FLOAT', 6);
    define('SQLSRV_SQLTYPE_IMAGE', -4);
    define('SQLSRV_SQLTYPE_INT', 4);
    define('SQLSRV_SQLTYPE_MONEY', 33564163);
    // define('SQLSRV_SQLTYPE_NCHAR', 0);
    // define('SQLSRV_SQLTYPE_NUMERIC', 0);
    // define('SQLSRV_SQLTYPE_NVARCHAR', 0);
    // define('SQLSRV_SQLTYPE_NVARCHAR(\'max\')', 0);
    define('SQLSRV_SQLTYPE_NTEXT', -10);
    define('SQLSRV_SQLTYPE_REAL', 7);
    define('SQLSRV_SQLTYPE_SMALLDATETIME', 8285);
    define('SQLSRV_SQLTYPE_SMALLINT', 5);
    define('SQLSRV_SQLTYPE_SMALLMONEY', 33559555);
    define('SQLSRV_SQLTYPE_TEXT', -1);
    define('SQLSRV_SQLTYPE_TIME', 58728806);
    define('SQLSRV_SQLTYPE_TIMESTAMP', 4606);
    define('SQLSRV_SQLTYPE_TINYINT', -6);
    define('SQLSRV_SQLTYPE_UNIQUEIDENTIFIER', -11);
    define('SQLSRV_SQLTYPE_UDT', -151);
    // define('SQLSRV_SQLTYPE_VARBINIARY', 0);
    // define('SQLSRV_SQLTYPE_VARBINARY(\'max\')', 0);
    // define('SQLSRV_SQLTYPE_VARCHAR', 0);
    define('SQLSRV_SQLTYPE_XML', -152);

    define('SQLSRV_TXN_READ_UNCOMMITTED', 1);
    define('SQLSRV_TXN_READ_COMMITTED', 2);
    define('SQLSRV_TXN_REPEATABLE_READ', 4);
    define('SQLSRV_TXN_SNAPSHOT', 32);
    // define('SQLSRV_TXN_READ_SERIALIZABLE', 0);

    define('SQLSRV_CURSOR_FORWARD', 'forward');
    define('SQLSRV_CURSOR_STATIC', 'static');
    define('SQLSRV_CURSOR_DYNAMIC', 'dynamic');
    define('SQLSRV_CURSOR_KEYSET', 'keyset');
    // define('SQLSRV_CURSOR_BUFFERED', 0);

    define('SQLSRV_SCROLL_NEXT', 1);
    define('SQLSRV_SCROLL_FIRST', 2);
    define('SQLSRV_SCROLL_LAST', 3);
    define('SQLSRV_SCROLL_PRIOR', 4);
    define('SQLSRV_SCROLL_ABSOLUTE', 5);
    define('SQLSRV_SCROLL_RELATIVE', 6);

    function sqlsrv_begin_transaction( $conn )
    {
      return SqlShim::begin_transaction($conn);
    }

    function sqlsrv_cancel( $stmt )
    {
      return SqlShim::cancel($stmt);
    }

    function sqlsrv_client_info( $conn )
    {
      return SqlShim::client_info($conn);
    }

    function sqlsrv_close( $stmt )
    {
      return SqlShim::close($stmt);
    }

    function sqlsrv_commit( $conn )
    {
      return SqlShim::commit($conn);
    }

    function sqlsrv_configure( $setting, $value )
    {
      return SqlShim::configure($setting, $value);
    }

    function sqlsrv_connect( $dbhost, $cnfo )
    {
      return SqlShim::connect($dbhost, $cnfo);
    }

    function sqlsrv_errors( $errorsOrWarnings=SQLSRV_ERR_ALL )
    {
      return SqlShim::errors($errorsOrWarnings);
    }

    function sqlsrv_execute( $stmt )
    {
      return SqlShim::execute($stmt);
    }

    function sqlsrv_fetch_array( $stmt, $fetchType=SQLSRV_FETCH_BOTH,
      $row=SQLSRV_SCROLL_NEXT, $offset=0 )
    {
      return SqlShim::fetch_array($stmt, $fetchType, $row, $offset);
    }

    function sqlsrv_fetch_object( $stmt, $className='stdClass',
      $ctorParams=array(), $row=SQLSRV_SCROLL_NEXT, $offset=0 )
    {
      return SqlShim::fetch_object($stmt, $className, $ctorParams, $row, $offset);
    }

    function sqlsrv_fetch( $stmt, $row=SQLSRV_SCROLL_NEXT, $offset=0 )
    {
      return SqlShim::fetch($stmt, $row, $offset);
    }

    function sqlsrv_field_metadata( $stmt )
    {
      return SqlShim::field_metadata($stmt);
    }

    function sqlsrv_free_stmt( $stmt )
    {
      return SqlShim::free_stmt($stmt);
    }

    function sqlsrv_get_config( $setting )
    {
      return SqlShim::get_config($setting);
    }

    function sqlsrv_get_field( $stmt, $fieldIndex=0, $getAsType )
    {
      return SqlShim::get_field($stmt, $fieldIndex=0, $getAsType);
    }

    function sqlsrv_has_rows( $stmt )
    {
      return SqlShim::has_rows($stmt);
    }

    function sqlsrv_next_result( $stmt )
    {
      return SqlShim::next_result($stmt);
    }

    function sqlsrv_num_fields( $stmt )
    {
      return SqlShim::num_fields($stmt);
    }

    function sqlsrv_num_rows( $stmt )
    {
      return SqlShim::num_rows($stmt);
    }

    function sqlsrv_prepare( $conn, $sql, $params=[], $options=[] )
    {
      return SqlShim::prepare($conn, $sql, $params, $options);
    }

    function sqlsrv_query( $conn, $sql, $params=[], $options=[] )
    {
      return SqlShim::query($conn, $sql, $params, $options);
    }

    function sqlsrv_rollback( $conn )
    {
      return SqlShim::rollback($conn);
    }

    function sqlsrv_rows_affected( $stmt )
    {
      return SqlShim::rows_affected($stmt);
    }

    function sqlsrv_send_stream_data( $stmt )
    {
      return SqlShim::send_stream_data($stmt);
    }

    function sqlsrv_server_info( $conn )
    {
      return SqlShim::server_info($conn);
    }
  }
}