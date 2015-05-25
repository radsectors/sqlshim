<?php
namespace RadSectors\Microshaft;

class SqlShim
{
  private static $error_table;
  private static $fetch_table;
  private static $scroll_table;
  private static $options = [];
  private static $_init = false;

  const NAME = __CLASS__;

  const SQL_INT_MAX = 2147483648;

  const MAGIC_NUM_BINARY = 2139095550;
  const MAGIC_NUM_CHAR = 2139095041;
  const MAGIC_NUM_DECIMAL = 3;
  const MAGIC_NUM_NCHAR = 2139095544;
  const MAGIC_NUM_NVARCHAR = 2139095543;
  const MAGIC_NUM_NUMERIC = 2;
  const MAGIC_NUM_STREAM = 6;
  const MAGIC_NUM_STRING = 8389636;
  const MAGIC_NUM_VARBINARY = 2139095549;
  const MAGIC_NUM_VARCHAR = 2139095052;

  const MAGIC_NUM_ZERO = 8387584;


  /**
   * init
   */
  public static function init( $opts=[] )
  {
    if ( self::$_init ) return;

    require 'register_globals.php';

    self::$options = [
     'driver' => "FreeTDS",
     'tds_version' => "7.2",
    ];
    foreach ( $opts as $opt=>$val )
    {
     $opt = strtolower($opt);
     switch ( $opt )
     {
       case 'driver':
         self::$options[$opt] = $val;
         break;
       default:
         break;
     }
    }

    self::$error_table = [];
    self::$fetch_table = [
     SQLSRV_FETCH_NUMERIC => \PDO::FETCH_NUM,
     SQLSRV_FETCH_ASSOC => \PDO::FETCH_ASSOC,
     SQLSRV_FETCH_BOTH => \PDO::FETCH_BOTH,
    ];
    self::$scroll_table = [
     SQLSRV_SCROLL_NEXT => \PDO::FETCH_ORI_NEXT,
     SQLSRV_SCROLL_FIRST => \PDO::FETCH_ORI_FIRST,
     SQLSRV_SCROLL_LAST => \PDO::FETCH_ORI_LAST,
     SQLSRV_SCROLL_PRIOR => \PDO::FETCH_ORI_PRIOR,
     SQLSRV_SCROLL_ABSOLUTE => \PDO::FETCH_ORI_ABS,
     SQLSRV_SCROLL_RELATIVE => \PDO::FETCH_ORI_REL,
    ];

    self::$_init = true;
  }


  /**
   * err
   */
  private static function err( $errnfo )
  {
    self::$error_table[] = [
      'SQLSTATE' => $errnfo[0],
      'code' => $errnfo[1],
      'message' => $errnfo[2],
    ];
  }

  /**
   * typify
   */
  private static function typify( $row )
  {
    $i = 0;
    foreach ( $row as $col=>&$val )
    {
      $val = self::guesstype($val);
      $i++;
    }
    return $row;
  }

  /**
   * guesstype
   */
  private static function guesstype( $val )
  {
    $num = is_numeric($val);
    $float = $num && strpos($val, '.')!==false;

    if ( $float ) return floatval($val);
    if ( $num ) return intval($val);

    $len = strlen($val);
    $date = ( $len==10 || $len==23 ) && preg_match('/\d{4}-\d{2}-\d{2}.*/', $val);

    if ( $date ) return new DateTime($val);

    return $val;
  }


  /*
   * sqlsrv constants
   */
  const SQLSRV_FETCH_NUMERIC = 1;
  const SQLSRV_FETCH_ASSOC = 2;
  const SQLSRV_FETCH_BOTH = 3;

  const SQLSRV_ERR_ERRORS = 0;
  const SQLSRV_ERR_WARNINGS = 1;
  const SQLSRV_ERR_ALL = 2;

  const SQLSRV_LOG_SYSTEM_ALL = -1;
  const SQLSRV_LOG_SYSTEM_CONN = 2;
  const SQLSRV_LOG_SYSTEM_INIT = 1;
  const SQLSRV_LOG_SYSTEM_OFF = 0;
  const SQLSRV_LOG_SYSTEM_STMT = 4;
  const SQLSRV_LOG_SYSTEM_UTIL = 8;

  const SQLSRV_LOG_SEVERITY_ALL = -1;
  const SQLSRV_LOG_SEVERITY_ERROR = 1;
  const SQLSRV_LOG_SEVERITY_NOTICE = 4;
  const SQLSRV_LOG_SEVERITY_WARNING = 2;

  const SQLSRV_NULLABLE_YES = 1;
  const SQLSRV_NULLABLE_NO = 0;
  const SQLSRV_NULLABLE_UNKNOWN = 2;

  const SQLSRV_PARAM_IN = 1;
  const SQLSRV_PARAM_INOUT = 2;
  const SQLSRV_PARAM_OUT = 4;

  const SQLSRV_PHPTYPE_INT = 2;
  const SQLSRV_PHPTYPE_DATETIME = 5;
  const SQLSRV_PHPTYPE_FLOAT = 3;
  public static function SQLSRV_PHPTYPE_STREAM( $encoding )
	{
    $en = strval($encoding);
    $r = 0;
    if ( $en=='binary' )
    {
      $r = 1; //518;
    }
    elseif ( $en=='char' )
    {
      $r = 1.5; //774;
    }
    return $r*512+self::MAGIC_NUM_STREAM;
  }
  public static function SQLSRV_PHPTYPE_STRING( $encoding )
	{
    $en = strval($encoding);
    // $r = null;
    if ( $en=='binary' )
    {
      $r = 1;
    }
    elseif ( $en=='char' )
    {
      $r = 1.5;
    }
    return (49150+$r)*512+self::MAGIC_NUM_STRING;
  }
  const SQLSRV_PHPTYPE_NULL = 1;

  const SQLSRV_ENC_BINARY = 'binary';
  const SQLSRV_ENC_CHAR = 'char';

  const SQLSRV_SQLTYPE_BIGINT = -5;
  public static function SQLSRV_SQLTYPE_BINARY( $byteCount )
	{
    $bc = intval($byteCount);
    $r = self::MAGIC_NUM_ZERO;
    if ( $bc==-1 )
    {
      $r += 512;
    }
    elseif ( $bc>=1 && $bc<=8000 )
    {
      $r = $bc*512;
    }
    return $r+self::MAGIC_NUM_BINARY;
  }
  const SQLSRV_SQLTYPE_BIT = -7;
  public static function SQLSRV_SQLTYPE_CHAR( $charCount )
	{
    $cc = intval($charCount);
    $r = self::MAGIC_NUM_ZERO;
    if ( $bc==-1 )
    {
      $r += 512;
    }
    elseif ( $cc>0 && $cc<=8000 )
    {
      $r = $cc*512;
    }
    return $r+self::MAGIC_NUM_CHAR;
  }
  const SQLSRV_SQLTYPE_DATE = 5211;
  const SQLSRV_SQLTYPE_DATETIME = 25177693;
  const SQLSRV_SQLTYPE_DATETIME2 = 58734173;
  const SQLSRV_SQLTYPE_DATETIMEOFFSET = 58738021;
  public static function SQLSRV_SQLTYPE_DECIMAL( $precision, $scale )
  {
    $pc = intval($precision);
    $r = self::MAGIC_NUM_ZERO;
    if ( $pc>=0 && $pc<=38 )
    {
      $r = $pc*512;
    };
    return $r+self::MAGIC_NUM_DECIMAL;
  }
  const SQLSRV_SQLTYPE_FLOAT = 6;
  const SQLSRV_SQLTYPE_IMAGE = -4;
  const SQLSRV_SQLTYPE_INT = 4;
  const SQLSRV_SQLTYPE_MONEY = 33564163;
  public static function SQLSRV_SQLTYPE_NCHAR( $charCount )
  {
    $cc = intval($charCount);
    $r = self::MAGIC_NUM_ZERO;
    if ( $bc==-1 )
    {
      $r += 512;
    }
    elseif ( $cc>0 && $cc<=4000 )
    {
      $r = $cc*512;
    }
    return $r+self::MAGIC_NUM_NCHAR;
  }
  public static function SQLSRV_SQLTYPE_NUMERIC( $precision, $scale )
  {
    $pc = intval($precision);
    $r = self::MAGIC_NUM_ZERO;
    if ( $pc>=0 && $pc<=38 )
    {
      $r = $pc*512;
    };
    return $r+self::MAGIC_NUM_NUMERIC;
  }
  public static function SQLSRV_SQLTYPE_NVARCHAR( $charCount )
	{
    $cc = intval($charCount);
    $r = self::MAGIC_NUM_ZERO;
    if ( $bc==-1 )
    {
      $r += 512;
    }
    elseif ( $cc>0 && $cc<=4000 )
    {
      $r = $cc*512;
    }
    return $r+self::MAGIC_NUM_NVARCHAR;
  }
  const SQLSRV_SQLTYPE_NTEXT = -10;
  const SQLSRV_SQLTYPE_REAL = 7;
  const SQLSRV_SQLTYPE_SMALLDATETIME = 8285;
  const SQLSRV_SQLTYPE_SMALLINT = 5;
  const SQLSRV_SQLTYPE_SMALLMONEY = 33559555;
  const SQLSRV_SQLTYPE_TEXT = -1;
  const SQLSRV_SQLTYPE_TIME = 58728806;
  const SQLSRV_SQLTYPE_TIMESTAMP = 4606;
  const SQLSRV_SQLTYPE_TINYINT = -6;
  const SQLSRV_SQLTYPE_UNIQUEIDENTIFIER = -11;
  const SQLSRV_SQLTYPE_UDT = -151;
  public static function SQLSRV_SQLTYPE_VARBINARY( $byteCount )
	{
    $bc = intval($byteCount);
    $r = self::MAGIC_NUM_ZERO;
    if ( $bc==-1 )
    {
      $r += 512;
    }
    elseif ( $bc>0 && $bc<=8000 )
    {
      $r = $bc*512;
    }
    return $r+self::MAGIC_NUM_VARBINARY;
  }
  public static function SQLSRV_SQLTYPE_VARCHAR( $charCount )
	{
    $cc = intval($charCount);
    $r = self::MAGIC_NUM_ZERO;
    if ( $bc==-1 )
    {
      $r += 512;
    }
    elseif ( $cc>0 && $cc<=8000 )
    {
      $r = $cc*512;
    }
    return $r+self::MAGIC_NUM_VARCHAR;
  }
  const SQLSRV_SQLTYPE_XML = -152;

  const SQLSRV_TXN_READ_UNCOMMITTED = 1;
  const SQLSRV_TXN_READ_COMMITTED = 2;
  const SQLSRV_TXN_REPEATABLE_READ = 4;
  const SQLSRV_TXN_SNAPSHOT = 32;
  const SQLSRV_TXN_SERIALIZABLE = 8;

  const SQLSRV_CURSOR_FORWARD = 'forward';
  const SQLSRV_CURSOR_STATIC = 'static';
  const SQLSRV_CURSOR_DYNAMIC = 'dynamic';
  const SQLSRV_CURSOR_KEYSET = 'keyset';
  const SQLSRV_CURSOR_CLIENT_BUFFERED = 'buffered';

  const SQLSRV_SCROLL_NEXT = 1;
  const SQLSRV_SCROLL_FIRST = 2;
  const SQLSRV_SCROLL_LAST = 3;
  const SQLSRV_SCROLL_PRIOR = 4;
  const SQLSRV_SCROLL_ABSOLUTE = 5;
  const SQLSRV_SCROLL_RELATIVE = 6;



  /*
   * sqlsrv functions...
   */

  /**
   * sqlsrv_begin_transaction
   */
  public static function begin_transaction( $conn )
  {
    return $conn->beginTransaction();
  }

  /**
   * sqlsrv_cancel
   */
  public static function cancel( $stmt )
  {
    // no PDO equivalent for this API
  }

  /**
   * sqlsrv_client_info
   */
  public static function client_info( $conn )
  {
    // closest thing i could find.
    return $conn->getAvailableDrivers();
  }

  /**
   * sqlsrv_close
   */
  public static function close( $stmt )
  {
    $return = true;
    return $return;
  }

  /**
   * sqlsrv_commit
   */
  public static function commit( $conn )
  {
    $conn->commit();
  }

  /**
   * sqlsrv_configure
   */
  public static function configure( $setting, $value )
  {
    // http://php.net/manual/en/function.sqlsrv-configure.php#refsect1-function.sqlsrv-configure-parameters
    // http://php.net/manual/en/pdo.setattribute.php#refsect1-pdo.setattribute-description
    return true;
  }

  /**
   * sqlsrv_connect
   */
  public static function connect( $serverName, $connectionInfo )
  {
    // @TODO: research other prefixes? do something with them?
    $serverName = str_replace('tcp:','',$serverName);

    // default port
    list($serverName,$port) = explode(',', $serverName . ",1433", 2);

    $driver = "odbc:" .
      "Driver=" . self::$options['driver'] . ";" .
      "TDS_Version=" . self::$options['tds_version'] . ";";
    $creds =
      "Server=$serverName;" .
      "Port=$port;" .
      "Database=$connectionInfo[Database];";
    $options =
      "Charset=$connectionInfo[CharacterSet];";

    try {
      $return = new \PDO(
        $driver.$creds.$options,
        $connectionInfo['UID'],
        $connectionInfo['PWD']
      );
      $return->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
      $return->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_NATURAL);
      $return->setAttribute(\PDO::ATTR_ORACLE_NULLS, \PDO::NULL_NATURAL);
      $return->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);
      $return->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
      $return->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }
    catch ( \PDOException $e )
    {
      self::err($e->errorInfo);
      return false;
    }
    return $return;
  }

  /**
   * sqlsrv_errors
   */
  public static function errors( $errorsOrWarnings )
  {
    return self::$error_table;
  }

  /**
   * sqlsrv_execute
   */
  public static function execute( $stmt )
  {
    return $stmt->execute();
  }

  /**
   * sqlsrv_fetch_array
   */
  public static function fetch_array( $stmt, $fetchType, $row, $offset )
  {
    try {
      $return = $stmt->fetch(
        \PDO::FETCH_ASSOC | self::$fetch_table[$fetchType],
        self::$scroll_table[$row],
        $offset
      );
      if ( is_array($return) )
      {
        $return = self::typify($return);
      }
    }
    catch ( \PDOException $e )
    {
      self::err($stmt);
      $return = false;
    }

    return $return;
  }

  /**
   * sqlsrv_fetch_object
   */
  public static function fetch_object( $stmt, $className, $ctorParams, $row, $offset )
  {
    try {
      $return = $stmt->fetch(
        \PDO::FETCH_OBJ,
        self::$scroll_table[$row],
        $offset
      );
    }
    catch ( Exception $e )
    { //
      try
      {
        $return = $stmt->fetch(
          \PDO::FETCH_ASSOC | self::$fetch_table[$fetchType],
          self::$scroll_table[$row],
          $offset
        );
        if ( is_array($return) )
        {
          $return = (object)$return;
        }
      }
      catch ( Exception $e )
      {
        self::err($stmt);
        $return = false;
      }
    }

    if ( is_object($return) )
    {
      $return = self::typify($return);
    }

    return $return;
  }

  /**
   * sqlsrv_fetch
   */
  public static function fetch( $stmt, $row, $offset )
  {
    return $stmt->fetch(\PDO::FETCH_NUM, self::$scroll_table[$row], $offset);
  }

  /**
   * sqlsrv_field_metadata
   */
  public static function field_metadata( $stmt, $row, $offset )
  {

  }

  /**
   * sqlsrv_free_stmt
   */
  public static function free_stmt( $stmt )
  {
    $return = $stmt->closeCursor();
    return $return;
  }

  /**
   * sqlsrv_get_config
   */
  public static function get_config( $setting )
  {

  }

  /**
   * sqlsrv_get_field
   */
  public static function get_field( $stmt, $fieldIndex, $getAsType )
  {
    // @TODO: figure out what to do with $getAsType...
    return $stmt->fetchColumn($fieldIndex);
  }

  /**
   * sqlsrv_has_rows
   */
  public static function has_rows( $stmt )
  {

  }

  /**
   * sqlsrv_next_result
   */
  public static function next_result( $stmt )
  {

  }

  /**
   * sqlsrv_num_fields
   */
  public static function num_fields( $stmt )
  {

  }

  /**
   * sqlsrv_num_rows
   */
  public static function num_rows( $stmt )
  {
    // @TODO: OR just get the count before, set it somewhere static and grab...
    // @TODO: test this.
    $row = $stmt->fetch(PDO::FETCH_NUM);
    if ( is_array($row) && count($row) )
    {
      return $row[key($row)];
    }
    return false;
  }

  /**
   * sqlsrv_prepare
   */
  public static function prepare( $conn, $sql, $params, $options )
  {
    $i = 1;
    $count = -1;
    do {
      $sql = preg_replace('/\?/', ":var$i", $sql, 1, $found);
      $i++;
      $count++;
    } while ( $found );

    try {
      $stmt = $conn->prepare($sql);
      $i = 1;
      foreach ( array_slice($params, 0, $count) as $var )
      {
        if ( $i>$count ) break;
        $stmt->bindValue(":var$i", $var);
        $i++;
      }

      return $stmt;
    }
    catch ( Exception $e )
    {
      self::err($stmt);
    }
  }

  /**
   * sqlsrv_query
   */
  public static function query( $conn, $sql, $params, $options )
  {
    $stmt = self::prepare($conn, $sql, $params, $options);

    try {
      if ( $stmt->execute() )
        return $stmt;
      else {
        self::err($stmt);
      }
    }
    catch ( Exception $e )
    {
      self::err($stmt);
    }
    return false;
  }

  /**
   * sqlsrv_rollback
   */
  public static function rollback( $conn )
  {
    return $conn->rollBack();
  }

  /**
   * sqlsrv_rows_affected
   */
  public static function rows_affected( $stmt )
  {
    return $stmt->rowCount();
  }

  /**
   * sqlsrv_send_stream_data
   */
  public static function send_stream_data( $stmt )
  {

  }

  /**
   * sqlsrv_server_info
   */
  public static function server_info( $conn )
  {

  }
}