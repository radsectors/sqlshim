<?php
namespace RadSectors;

/**
 * PHP sqlsrv functions for Linux/OS X
 *
 * @package sqlshim
 */
class SqlShim
{
  private static $options = [];

  private static $tabcursor;
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
  const MAGIC_NUM_NOSCALE = 2139095040;
  const MAGIC_NUM_SCALE = 8389120;
  const MAGIC_NUM_STREAM = 6;
  const MAGIC_NUM_STRING = 8389636;
  const MAGIC_NUM_VARBINARY = 2139095549;
  const MAGIC_NUM_VARCHAR = 2139095052;
  const MAGIC_NUM_ZERO = 8387584;

  const INVALID_PRECISION = -1;
  const INVALID_SCALE = -1;
  const SIZE_MAX_TYPE = -1;

  // constants for maximums in SQL Server
  const MAX_FIELD_SIZE = 8000;
  const MAX_PRECISION = 38;
  const DEFAULT_PRECISION = 18;
  const DEFAULT_SCALE = 0;


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
      'driver' => "SqlShim",
      'tds_version' => "7.4",
      'autotype_fields' => false,
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

    self::$tabcursor = [
      self::CURSOR_FORWARD => \PDO::CURSOR_FWDONLY,
      self::CURSOR_STATIC => \PDO::CURSOR_FWDONLY, // ???
      self::CURSOR_DYNAMIC => \PDO::CURSOR_SCROLL, // ???
      self::CURSOR_KEYSET => \PDO::CURSOR_FWDONLY, // ???
      self::CURSOR_CLIENT_BUFFERED => \PDO::CURSOR_FWDONLY, // ???
    ];
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
    $registered = require( __DIR__ . '/globals.php');

    self::$_init = true;
  }


  /**
   * @internal Collects error info to internal error log
   *
   * @param mixed $e Error Info
   * @return void
   */
  private static function log_err( $e )
  {
    if ( is_array($e) )
    {
      self::$errors[] = [
        'SQLSTATE' => $e[0],
        'code' => $e[1],
        'message' => $e[2],
      ];
      return;
    }

    if ( is_object($e) && get_class($e)=="PDOException" )
    {
      if ( !empty($e->errorInfo) && is_array($e->errorInfo) )
      {
        self::log_err($e->errorInfo);
        return;
      }
      elseif ( method_exists($e, 'getMessage') && (bool)preg_replace_callback(
          "/SQLSTATE\[(\d+)\] .*: (\d+) (.*)/",
          function ( $matches )
          {
            if ( count($matches) )
            {
              self::log_err(array_slice($matches, 1));
              return "1";
            }
            return "0";
          },
          $e->getMessage()
        )
      )
      {
        return;
      }
    }
    self::log_err(["???","?","Unknown error"]);
    return;
  }

  private static function calc_size( $c, $max=self::MAX_FIELD_SIZE )
  {
    $c = intval($c);
    $r = self::MAGIC_NUM_ZERO;
    if ( $c==-1 )
    {
      $r += 512;
    }
    elseif ( $c>0 && $c<=$max )
    {
      $r = $c*512;
    }
    return $r;
  }

  private static function calc_size_str( $en, $r )
  {
    $en = strval($en);
    if ( $en=='binary' )
    {
      $r += 1;
    }
    elseif ( $en=='char' )
    {
      $r += 1.5;
    }
    return intval($r*512);
  }

  private static function calc_prec_scale( $prec, $scale )
  {
    $p = is_numeric($prec) ?
      intval($prec)
      : self::INVALID_PRECISION
    ;
    $s = is_numeric($scale) ?
      intval($scale)
      : self::INVALID_SCALE
    ;
    if ( $s==-257 ) $s = self::INVALID_SCALE;
    if ( $s<-256 ) $s = $s%256;

    if ( ($s==self::INVALID_PRECISION && ($p<0 || $p>self::MAX_PRECISION)) ) {
      $c = 0;
      $b = $s;
    }
    else
    {
      $c = $s*self::MAGIC_NUM_SCALE;
      $b = $p-$s;

      if ( $p>self::MAX_PRECISION )
      {
        $p = self::INVALID_PRECISION;
      }
      if ( $p<0 )
      {
        $p = self::INVALID_PRECISION;
        $c = ($s+1)*self::MAGIC_NUM_SCALE;
        $b = -$s-2;
      }
      if ( $s>$p )
      {
        $s = self::INVALID_SCALE;
        $c = self::MAGIC_NUM_NOSCALE;
        $b = $p;
      }

      if ( $p==self::INVALID_PRECISION && $s==self::INVALID_SCALE )
      {
        $c = self::MAGIC_NUM_SCALE+self::MAGIC_NUM_NOSCALE;
        $b = -2;
      }
    }
    return intval($c+($b*512));
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
    if ( self::$options['autotype_fields'] )
    {
      $num = is_numeric($val);
      $float = $num && strpos($val, '.')!==false;

      if ( $float ) return floatval($val);
      if ( $num ) return intval($val);
    }

    $len = strlen($val);

    if ( ( $len==10 || $len==23 ) && preg_match('/\d{4}-\d{2}-\d{2}.*/', $val) )
    {
      return new \DateTime($val);
    }

    return $val;
  }

  // client info helper functions.
  public static function client_info_driver_dllname()
  {
    $return = basename(end(explode(" ", exec("cat /etc/odbcinst.ini | grep Driver"))));
    return $return;
  }
  public static function client_info_driver_odbcver()
  {
    return end(explode(" ", exec("isql --version")));
  }
  public static function client_info_driver_ver()
  {
    return end(explode(" ", exec("tsql -C | grep Version")));
  }
  public static function client_info_ext_ver()
  {
    return phpversion('pdo_odbc');
    // return (new \ReflectionExtension('pdo_odbc'))->getVersion();
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
    return self::calc_size_str($encoding, 65536)+self::MAGIC_NUM_STREAM;
  }

  public static function PHPTYPE_STRING( $encoding )
	{
    return self::calc_size_str($encoding, 49150)+self::MAGIC_NUM_STRING;
  }

  public static function SQLTYPE_BINARY( $byteCount )
	{
    return self::calc_size($byteCount)+self::MAGIC_NUM_BINARY;
  }

  public static function SQLTYPE_CHAR( $charCount )
	{
    return self::calc_size($charCount)+self::MAGIC_NUM_CHAR;
  }

  public static function SQLTYPE_DECIMAL( $precision, $scale )
  {
    return self::calc_prec_scale($precision, $scale)+self::MAGIC_NUM_DECIMAL;
  }

  public static function SQLTYPE_NCHAR( $charCount )
  {
    return self::calc_size($charCount, 4000)+self::MAGIC_NUM_NCHAR;
  }

  public static function SQLTYPE_NUMERIC( $precision, $scale )
  {
    return self::calc_prec_scale($precision, $scale)+self::MAGIC_NUM_NUMERIC;
  }

  public static function SQLTYPE_NVARCHAR( $charCount )
	{
    return self::calc_size($charCount, 4000)+self::MAGIC_NUM_NVARCHAR;
  }

  public static function SQLTYPE_VARBINARY( $byteCount )
	{
    return self::calc_size($byteCount)+self::MAGIC_NUM_VARBINARY;
  }

  public static function SQLTYPE_VARCHAR( $charCount )
	{
    return self::calc_size($charCount)+self::MAGIC_NUM_VARCHAR;
  }


  public static function begin_transaction( \PDO $conn )
  {
    return $conn->beginTransaction();
  }

  public static function cancel( \PDOStatement $stmt )
  {
    // no PDO equivalent for this API
  }

  public static function client_info( \PDO $conn )
  {
    // \PDO::ATTR_CLIENT_VERSION (integer)
    // REVIEW: client_info() - these system() calls may be too system-specific. or need some kind of prioritized alternatives.
    // the first one won't even work reliably.
    $return = [
      'DriverDLLName' => self::client_info_driver_dllname(),
      'DriverODBCVer' => self::client_info_driver_odbcver(),
      'DriverVer'     => self::client_info_driver_ver(),
      'ExtensionVer'  => self::client_info_ext_ver(),
    ];
    return $return;
  }

  public static function close( \PDO $conn )
  {
    return ($conn = null);
  }

  public static function commit( \PDO $conn )
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
    // IDEA: connect() - research prefixes? do something with them?
    $serverName = str_replace('tcp:','',$serverName);

    // default port
    list($serverName,$port) = explode(',', $serverName . ",1433", 3);

    try {
      $conn = new \PDO(
        sprintf(
          "odbc:driver=%s;tds_version=%s;server=%s;port=%s;database=%s;clientcharset=%s;",
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
      $conn->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
      $conn->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_NATURAL);
      $conn->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);
      $conn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
      $conn->prepare('SET ANSI_WARNINGS ON')->execute();
      $conn->prepare('SET ANSI_NULLS ON')->execute();
    }
    catch ( \PDOException $e )
    {
      self::log_err($e);
      return false;
    }
    return $conn;
  }

  public static function errors( $errorsOrWarnings=self::ERR_ALL )
  {
    // TODO: errors() figure out if we can differentiate between errors and warnings.
    return self::$errors;
  }

  public static function execute( \PDOStatement $stmt )
  {
    return $stmt->execute();
  }

  public static function fetch_array( \PDOStatement $stmt, $fetchType=self::FETCH_BOTH, $row=self::SCROLL_NEXT, $offset=0 )
  {
    try {
      $array = $stmt->fetch(
        self::$tabfetch[$fetchType],
        self::$tabscroll[$row],
        $offset
      );
      if ( is_array($array) )
      {
        return self::typify($array);
      }
    }
    catch ( \PDOException $e )
    {
      self::log_err($e);
      // error fetching (false)
      return false;
    }

    // fetch with no errors (null)
    return null;
  }

  public static function fetch_object( \PDOStatement $stmt, $className='stdClass', $ctorParams=[], $row=self::SCROLL_NEXT, $offset=0 )
  {
    try {
      $object = $stmt->fetch(
        \PDO::FETCH_ASSOC,
        self::$tabscroll[$row],
        $offset
      );
      // $object = $stmt->fetchObject(
      //   $className,
      //   $ctorParams
      // );
      if ( is_array($object) )
      {
        $object = (object)$object;
      }
      if ( is_object($object) )
      {
        return self::typify($object);
      }
    }
    catch ( \PDOException $e )
    {
      self::log_err($e);
      return false;
    }

    return null; // fetch with no more records (null)
  }

  public static function fetch( \PDOStatement $stmt, $row, $offset )
  {
    try
    {
      $array = $stmt->fetch(\PDO::FETCH_NUM, self::$tabscroll[$row], $offset);
      if ( is_array($array) )
      {
        return self::typify($array);
      }

    }
    catch ( \PDOException $e )
    {
      self::log_err($e);
      return false;
    }
    return null;
  }

  public static function field_metadata( \PDOStatement $stmt )
  {
    // NOTE: field_metadata() - PDOStatement->getColumnMeta() is an "EXPERIMENTAL" function. And its request not supported by driver.
    // HACK: field_metadata() - Implement our own? probably won't work (at first glance)
    // check: http://community.sitepoint.com/t/pdo-getcolumnmeta-bug/3430/2
    // credit: http://vancelucas.com/
    return false;
    // $metadata = [];
    // for ( $i=0; $i<$stmt->columnCount(); $i++ )
    // {
    //   $metadata[] = $stmt->getColumnMeta($i);
    // }
    // return $metadata;
  }

  public static function free_stmt( \PDOStatement $stmt )
  {
    return $stmt->closeCursor();
  }

  public static function get_config( $setting )
  {
    // TODO: get_config()
  }

  public static function get_field( \PDOStatement $stmt, $fieldIndex=0, $getAsType=null )
  {
    // TODO: get_field() - figure out what to do with $getAsType...
    // https://msdn.microsoft.com/en-us/library/cc296193.aspx
    return $stmt->fetchColumn($fieldIndex);
  }

  public static function has_rows( \PDOStatement $stmt )
  {
    // REVEW: this doesn't work unless $stmt->rowCount() works
    return (bool)$stmt->rowCount();
  }

  public static function next_result( \PDOStatement $stmt )
  {
    return $stmt->nextRowset();
  }

  public static function num_fields( \PDOStatement $stmt )
  {
    return $stmt->columnCount();
  }

  public static function num_rows( \PDOStatement $stmt )
  {
    // REVIEW: num_rows() - $stmt->rowCount DOES NOT work for SELECTs in MSSQL PDO.
    $conn = $stmt->conn;
    $sql = $stmt->queryString;
    if ( stripos($sql, "select")>=0 )
    {
      $sql = preg_replace("/SELECT .* FROM/", "SELECT COUNT(*) AS count FROM", $sql);

      var_dump($sql);
      $$cnt = $conn->query($sql);
      $row = $cnt->fetch(\PDO::FETCH_NUM);
      if ( is_array($row) && count($row) )
      {
        return $row[key($row)];
      }
    }
    return false;
  }

  public static function prepare( \PDO $conn, $sql, $params=[], $options=[] )
  {
    // REVIEW: prepare() - is the ?-to-:tag conversion necessary? since ?s are apparently supported.
    $i = 1;
    $count = -1;
    do {
      $sql = preg_replace('/\?/', ":var$i", $sql, 1, $found);
      $i++;
      $count++;
    } while ( $found );

    // translate options array
    $optionsin = $options;
    $options = [];
    foreach ( $options as $opt=>$val )
    {
      switch ( $opt )
      {
        case 'QueryTimeout':
          if ( is_numeric($val) )
          {
            $conn->setAttribute(\PDO::SQLSRV_ATTR_QUERY_TIMEOUT, intval($val));
          }
        break;
        case 'SendStreamParamsAtExec':
          // ???
        break;
        case 'Scrollable':
          if ( isset(self::$tabcursor[$val]) )
          {
            $options[\PDO::ATTR_CURSOR] = self::$tabcursor[$val];
          }
        break;
        default:
        break;
      }
    }

    if ( !is_array($params) ) $params = [];

    try {
      $stmt = $conn->prepare($sql, $options);
      $i = 1;
      foreach ( array_slice($params, 0, $count) as $var )
      {
        if ( $i>$count ) break;
        $bound = $stmt->bindValue(":var$i", $var);
        // if ( !$bound ) { echo "fail $i:$var<br>"; }
        $i++;
      }
      $stmt->conn = $conn; // for ref
      return $stmt;
    }
    catch ( \PDOException $e )
    {
      self::log_err($e->errorInfo);
      return false;
    }
  }

  public static function query( \PDO $conn, $sql, $params=[], $options=[] )
  {
    $stmt = self::prepare($conn, $sql, $params, $options);

    try
    {
      if ( self::execute($stmt) )
      {
        return $stmt;
      }
      else {
        self::log_err($stmt->errorInfo());
        return $stmt;
      }
    }
    catch ( \PDOException $e )
    {
      self::log_err($e->errorInfo);
    }
    return false;
  }

  public static function rollback( \PDO $conn )
  {
    return $conn->rollBack();
  }

  public static function rows_affected( \PDOStatement $stmt )
  {
    return $stmt->rowCount();
  }

  public static function send_stream_data( \PDOStatement $stmt )
  {
    // TODO: send_stream_data() - what is this?
  }

  public static function server_info( \PDO $conn )
  {
    // \PDO::ATTR_SERVER_VERSION (integer)
    // \PDO::ATTR_SERVER_INFO (integer)
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
