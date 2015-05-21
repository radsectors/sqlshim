<?php
namespace RadSectors\Microshaft;

class SqlShim
{
  private static $error_table;
  private static $fetch_table;
  private static $scroll_table;
  private static $options = [];
  private static $_init = false;

  const SQLSRV_INT_MAX = 2147483648;

  const MAGIC_NUM_BINARY = 2139095550;

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

  }

  /**
   * sqlsrv_client_info
   */
  public static function client_info( $conn )
  {

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

  }

  /**
   * sqlsrv_configure
   */
  public static function configure( $setting, $value )
  {

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

      $return->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
      $return->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
      // $return->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);
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



  /**
   * Helper functions
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
   * sqlsrv_typify
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
}