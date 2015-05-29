<?php
namespace RadSectors;

/**
 * Microsoft SQL Server Shim for PHP
 *
 * @package sqlshim
 */
class SqlShim
{
  private static $options = [];

  private static $tabfetch;
  private static $tabscroll;

  private static $errors = [];
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
   * Initialize the SqlShimmage
   *
   * @param array $options
   * @return void
   */
  public static function init( $options=[] )
  {
    // process options
    self::$options = [
      'driver' => "FreeTDS",
      'tds_version' => "7.2",
      // 'odbcini' => "/etc/odbc.ini",
      // 'odbcinstini' => "/etc/odbcinst.ini",
    ];

    foreach ( $options as $opt=>$val )
    {
      $opt = strtolower($opt);
      switch ( $opt )
      {
        case 'driver':
        case 'tds_version':
          self::$options[$opt] = $val;
          break;
        case 'odbcini':
        case 'odbcinstini':
          if ( file_exists($val) )
          {
            putenv(strtoupper($opt) . "=$val");
          }
          break;
        default:
          break;
      }
    }

    if ( self::$_init ) return;

    self::$errors = [];

    self::$tabfetch = [
      self::FETCH_NUMERIC => \PDO::FETCH_NUM,
      self::FETCH_ASSOC => \PDO::FETCH_ASSOC,
      self::FETCH_BOTH => \PDO::FETCH_BOTH,
    ];
    self::$tabscroll = [
      self::SCROLL_NEXT => \PDO::FETCH_ORI_NEXT,
      self::SCROLL_FIRST => \PDO::FETCH_ORI_FIRST,
      self::SCROLL_LAST => \PDO::FETCH_ORI_LAST,
      self::SCROLL_PRIOR => \PDO::FETCH_ORI_PRIOR,
      self::SCROLL_ABSOLUTE => \PDO::FETCH_ORI_ABS,
      self::SCROLL_RELATIVE => \PDO::FETCH_ORI_REL,
    ];

    // for global function registration
    // @REVIEW open to suggestions on a better way to do this.
    $FUNCTION = self::NAME . "::" . __FUNCTION__;
    $registered = require('register_globals.php');

    self::$_init = true;
  }


  /**
   * @internal Sends error info to error collector
   *
   * @param array $errnfo Error Info
   * @return void
   */
  private static function err( $errnfo )
  {
    self::$errors[] = [
      'SQLSTATE' => $errnfo[0],
      'code' => $errnfo[1],
      'message' => $errnfo[2],
    ];
  }

  /**
   * typify
   * @param object|array $row Database record to be typed.
   * @return $object|array Returns the typed record.
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
   * @param mixed $val The value for which the type is to be guessed.
   * @return mixed Returns the typed value.
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
  const FETCH_NUMERIC = 1;
  const FETCH_ASSOC = 2;
  const FETCH_BOTH = 3;

  const ERR_ERRORS = 0;
  const ERR_WARNINGS = 1;
  const ERR_ALL = 2;

  const LOG_SYSTEM_ALL = -1;
  const LOG_SYSTEM_CONN = 2;
  const LOG_SYSTEM_INIT = 1;
  const LOG_SYSTEM_OFF = 0;
  const LOG_SYSTEM_STMT = 4;
  const LOG_SYSTEM_UTIL = 8;

  const LOG_SEVERITY_ALL = -1;
  const LOG_SEVERITY_ERROR = 1;
  const LOG_SEVERITY_NOTICE = 4;
  const LOG_SEVERITY_WARNING = 2;

  const NULLABLE_YES = 1;
  const NULLABLE_NO = 0;
  const NULLABLE_UNKNOWN = 2;

  const PARAM_IN = 1;
  const PARAM_INOUT = 2;
  const PARAM_OUT = 4;

  const PHPTYPE_INT = 2;
  const PHPTYPE_DATETIME = 5;
  const PHPTYPE_FLOAT = 3; /*
  const PHPTYPE_STREAM = function();
  const PHPTYPE_STRING = function(); */
  const PHPTYPE_NULL = 1;

  const ENC_BINARY = 'binary';
  const ENC_CHAR = 'char';

  const SQLTYPE_BIGINT = -5; /*
  const SQLTYPE_BINARY = function()*/
  const SQLTYPE_BIT = -7; /*
  const SQLTYPE_CHAR = function(); */
  const SQLTYPE_DATE = 5211;
  const SQLTYPE_DATETIME = 25177693;
  const SQLTYPE_DATETIME2 = 58734173;
  const SQLTYPE_DATETIMEOFFSET = 58738021; /*
  const SQLTYPE_DECIMAL = function(); */
  const SQLTYPE_FLOAT = 6;
  const SQLTYPE_IMAGE = -4;
  const SQLTYPE_INT = 4;
  const SQLTYPE_MONEY = 33564163;/*
  const SQLTYPE_NCHAR = function();
  const SQLTYPE_NUMERIC = function();
  const SQLTYPE_NCHAR = function();
  const SQLTYPE_NUMERIC = function();
  const SQLTYPE_NVARCHAR = function(); */
  const SQLTYPE_NTEXT = -10;
  const SQLTYPE_REAL = 7;
  const SQLTYPE_SMALLDATETIME = 8285;
  const SQLTYPE_SMALLINT = 5;
  const SQLTYPE_SMALLMONEY = 33559555;
  const SQLTYPE_TEXT = -1;
  const SQLTYPE_TIME = 58728806;
  const SQLTYPE_TIMESTAMP = 4606;
  const SQLTYPE_TINYINT = -6;
  const SQLTYPE_UNIQUEIDENTIFIER = -11;
  const SQLTYPE_UDT = -151; /*
  const SQLTYPE_VARBINARY = function();
  const SQLTYPE_VARCHAR = function(); */
  const SQLTYPE_XML = -152;

  const TXN_READ_UNCOMMITTED = 1;
  const TXN_READ_COMMITTED = 2;
  const TXN_REPEATABLE_READ = 4;
  const TXN_SNAPSHOT = 32;
  const TXN_SERIALIZABLE = 8;

  const CURSOR_FORWARD = 'forward';
  const CURSOR_STATIC = 'static';
  const CURSOR_DYNAMIC = 'dynamic';
  const CURSOR_KEYSET = 'keyset';
  const CURSOR_CLIENT_BUFFERED = 'buffered';

  const SCROLL_NEXT = 1;
  const SCROLL_FIRST = 2;
  const SCROLL_LAST = 3;
  const SCROLL_PRIOR = 4;
  const SCROLL_ABSOLUTE = 5;
  const SCROLL_RELATIVE = 6;

  public static function PHPTYPE_STREAM( $encoding )
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

  public static function PHPTYPE_STRING( $encoding )
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

  public static function SQLTYPE_BINARY( $byteCount )
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

  public static function SQLTYPE_CHAR( $charCount )
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

  public static function SQLTYPE_DECIMAL( $precision, $scale )
  {
    $pc = intval($precision);
    $r = self::MAGIC_NUM_ZERO;
    if ( $pc>=0 && $pc<=38 )
    {
      $r = $pc*512;
    };
    return $r+self::MAGIC_NUM_DECIMAL;
  }

  public static function SQLTYPE_NCHAR( $charCount )
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

  public static function SQLTYPE_NUMERIC( $precision, $scale )
  {
    $pc = intval($precision);
    $r = self::MAGIC_NUM_ZERO;
    if ( $pc>=0 && $pc<=38 )
    {
      $r = $pc*512;
    };
    return $r+self::MAGIC_NUM_NUMERIC;
  }

  public static function SQLTYPE_NVARCHAR( $charCount )
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

  public static function SQLTYPE_VARBINARY( $byteCount )
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

  public static function SQLTYPE_VARCHAR( $charCount )
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


  public static function begin_transaction( $conn )
  {
    return $conn->beginTransaction();
  }

  public static function cancel( $stmt )
  {
    // no PDO equivalent for this API
  }

  public static function client_info( $conn )
  {
    return [
      'DriverDLLName' => basename(array_pop(explode(" ", exec("cat /etc/odbcinst.ini | grep Driver")))),
      'DriverODBCVer' => array_pop(explode(" ", exec("isql --version"))),
      'DriverVer' => array_pop(explode(" ", exec("tsql -C | grep Version"))),
      'ExtensionVer' => phpversion('pdo_odbc'), // (new \ReflectionExtension('pdo_odbc'))->getVersion(),
    ];
  }

  public static function close( $conn )
  {
    // TODO: close() - test this.
    return ($conn = null);
  }

  public static function commit( $conn )
  {
    $conn->commit();
  }

  public static function configure( $setting, $value )
  {
    // TODO: configure() - see these links
    // http://php.net/manual/en/function.sqlsrv-configure.php#refsect1-function.sqlsrv-configure-parameters
    // http://php.net/manual/en/pdo.setattribute.php#refsect1-pdo.setattribute-description
    return true;
  }

  public static function connect( $serverName, $connectionInfo )
  {
    // TODO: research prefixes? do something with them?
    $serverName = str_replace('tcp:','',$serverName);

    // default port
    list($serverName,$port) = explode(',', $serverName . ",1433", 2);

    try {
      $conn = new \PDO(
        sprintf(
          "odbc:driver=%s;tds_version=%s;server=%s;port=%s;database=%s;charset=%s;",
          self::$options['driver'],
          self::$options['tds_version'],
          $serverName,
          $port,
          $connectionInfo['Database'],
          $connectionInfo['CharacterSet']
        ),
        $connectionInfo['UID'],
        $connectionInfo['PWD']
      );
      $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
      $conn->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_NATURAL);
      $conn->setAttribute(\PDO::ATTR_ORACLE_NULLS, \PDO::NULL_NATURAL);
      $conn->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);
      $conn->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
      $conn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }
    catch ( \PDOException $e )
    {
      preg_replace_callback(
        "/SQLSTATE\[(\d+)\] .*: (\d+) (.*)/",
        function ( $matches )
        {
          if ( count($matches) )
          {
            self::err(array_slice($matches, 1));
          }
        },
        $e->getMessage()
      );
      return false;
    }
    return $conn;
  }

  public static function errors( $errorsOrWarnings=self::ERR_ALL )
  {
    // TODO: errors() figure out if we can differentiate between errors and warnings.
    return self::$errors;
  }

  public static function execute( $stmt )
  {
    return $stmt->execute();
  }

  public static function fetch_array( $stmt, $fetchType=self::FETCH_BOTH, $row=self::SCROLL_NEXT, $offset=0 )
  {
    try {
      $return = $stmt->fetch(
        self::$tabfetch[$fetchType],
        self::$tabscroll[$row],
        $offset
      );
      if ( is_array($return) )
      {
        $return = self::typify($return);
      }
    }
    catch ( \PDOException $e )
    {
      self::err($e);
      $return = false;
    }

    return $return;
  }

  public static function fetch_object( $stmt, $className='stdClass', $ctorParams=[], $row=self::SCROLL_NEXT, $offset=0 )
  {
    try {
      $stmt->setFetchMode(\PDO::FETCH_CLASS, $className, $ctorParams);
      $return = $stmt->fetch(
        \PDO::FETCH_CLASS,
        self::$tabscroll[$row],
        $offset
      );
    }
    catch ( \PDOException $e )
    {
      self::err($e);
      $return = false;
    }

    if ( is_object($return) )
    {
      $return = self::typify($return);
    }

    return $return;
  }

  public static function fetch( $stmt, $row, $offset )
  {
    return $stmt->fetch(\PDO::FETCH_NUM, self::$tabscroll[$row], $offset);
  }

  public static function field_metadata( $stmt, $row, $offset )
  {
    // TODO: field_metadata()
  }

  public static function free_stmt( $stmt )
  {
    $return = $stmt->closeCursor();
    return $return;
  }

  public static function get_config( $setting )
  {
    // TODO: get_config()
  }

  public static function get_field( $stmt, $fieldIndex=0, $getAsType=null )
  {
    // TODO: get_field() - figure out what to do with $getAsType...
    // https://msdn.microsoft.com/en-us/library/cc296193.aspx
    return $stmt->fetchColumn($fieldIndex);
  }

  public static function has_rows( $stmt )
  {
    // TODO: has_rows()
  }

  public static function next_result( $stmt )
  {
    return $sstmt->nextRowset();
  }

  public static function num_fields( $stmt )
  {
    return $stmt->columnCount();
  }

  public static function num_rows( $stmt )
  {
    return $stmt->rowCount();
    // IDEA: get the count before, set it somewhere static and grab...
    // REVIEW: test this.
    $row = $stmt->fetch(\PDO::FETCH_NUM);
    if ( is_array($row) && count($row) )
    {
      return $row[key($row)];
    }
    return false;
  }

  public static function prepare( $conn, $sql, $params=[], $options=[] )
  {
    // REVIEW: is the ?-to-:tag conversion necessary?
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
    catch ( PDOException $e )
    {
      self::err($e);
      return false;
    }
  }

  public static function query( $conn, $sql, $params=[], $options=[] )
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

  public static function rollback( $conn )
  {
    return $conn->rollBack();
  }

  public static function rows_affected( $stmt )
  {
    return $stmt->rowCount();
  }

  public static function send_stream_data( $stmt )
  {
    // TODO: send_stream_data() - what is this?
  }

  public static function server_info( $conn )
  {
    $stmt = self::query(
      $conn,
      "SELECT
        DB_NAME() AS CurrentDatabase,
        SERVERPROPERTY('ResourceVersion') AS SQLServerVersion,
        SERVERPROPERTY('ServerName') AS SQLServerName
      ;"
    );
    return self::fetch_array($stmt, self::FETCH_ASSOC);
  }
}