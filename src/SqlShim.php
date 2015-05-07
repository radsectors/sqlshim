<?php
namespace RadSectors\SqlShim;

class SqlShim
{
  private static $sqlsrv_error_table;
  private static $sqlsrv_fetch_table;
  private static $sqlsrv_scroll_table;
  private static $_init = false;

  public static function init()
  {
    if ( self::$_init ) return;
    require 'register_globals.php';

    self::$sqlsrv_error_table = [];
    self::$sqlsrv_fetch_table = [
      SQLSRV_FETCH_NUMERIC => \PDO::FETCH_NUM,
      SQLSRV_FETCH_ASSOC => \PDO::FETCH_ASSOC,
      SQLSRV_FETCH_BOTH => \PDO::FETCH_BOTH,
    ];
    self::$sqlsrv_scroll_table = [
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
  public static function begin_transaction( $conn ) {

  }

  /**
   * sqlsrv_cancel
   */
  public static function cancel( $stmt ) {

  }

  /**
   * sqlsrv_client_info
   */
  public static function client_info( $conn ) {

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
  public static function configure( $setting, $value ) {

  }

  /**
   * sqlsrv_connect
   */
  public static function connect( $dbhost, $cnfo )
  {
    self::init();

    $dbhost = str_replace('tcp:','',$dbhost);
    list($dbhost,$port) = explode(',',$dbhost,2);

    // http://lists.ibiblio.org/pipermail/freetds/2011q4/027555.html
    // http://www.freetds.org/userguide/choosingtdsprotocol.htm
    $constr = "odbc:Driver=FreeTDS;TDS_Version=7.2;Server=$dbhost;Port=$port;Database=$cnfo[Database];Charset=$cnfo[CharacterSet];";

    try {
      $return = new \PDO($constr, $cnfo['UID'], $cnfo['PWD']);
      $return->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
      // $return->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);
    }
    catch ( ErrorException $e ) {
      self::err($return);
    }
    return $return;
  }

  /**
   * sqlsrv_errors
   */
  public static function errors( $errorsOrWarnings )
  {
    return self::$sqlsrv_error_table;
  }

  /**
   * sqlsrv_execute
   */
  public static function execute( $stmt ) {

  }

  /**
   * sqlsrv_fetch_array
   */
  public static function fetch_array( $stmt, $fetchType, $row, $offset )
  {
    try {
      $return = $stmt->fetch(
        \PDO::FETCH_ASSOC | self::$sqlsrv_fetch_table[$fetchType]
        ,self::$sqlsrv_scroll_table[$row]
        ,$offset
      );
      if ( is_array($return) ) {
        $return = self::typify($return);
      }
    }
    catch ( ErrorException $e ) {
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
        \PDO::FETCH_OBJ
        ,self::$sqlsrv_scroll_table[$row]
        ,$offset
      );
      if ( is_object($return) ) {
        $return = self::typify($return);
      }
    }
    catch ( ErrorException $e ) {
      self::err($stmt);
      $return = false;
    }

    return $return;
  }

  /**
   * sqlsrv_fetch
   */
  public static function fetch( $stmt, $row, $offset ) {

  }

  /**
   * sqlsrv_field_metadata
   */
  public static function field_metadata( $stmt, $row, $offset ) {

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
  public static function get_config( $setting ) {

  }

  /**
   * sqlsrv_get_field
   */
  public static function get_field( $stmt, $fieldIndex, $getAsType ) {
    // @TODO: figure out what to do with $getAsType...
    return $stmt->fetchColumn($fieldIndex);
  }

  /**
   * sqlsrv_has_rows
   */
  public static function has_rows( $stmt ) {

  }

  /**
   * sqlsrv_next_result
   */
  public static function next_result( $stmt ) {

  }

  /**
   * sqlsrv_num_fields
   */
  public static function num_fields( $stmt ) {

  }

  /**
   * sqlsrv_num_rows
   */
  public static function num_rows( $stmt ) {
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
      foreach ( array_slice($params, 0, $count) as $var ) {
        if ( $i>$count ) break;
        $stmt->bindParam(":var$i", $var);
        $i++;
      }

      return $stmt;
    }
    catch ( ErrorException $e ) {
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
    catch ( ErrorException $e ) {
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

  private static function err( $obj )
  {
    $err = $obj->errorInfo();
    self::$sqlsrv_error_table[] = [
      'SQLSTATE' => $err[0],
      'code' => $err[1],
      'message' => $err[2],
    ];
  }

  /**
   * sqlsrv_typify
   */
  private static function typify( $row )
  {
    $i = 0;
    foreach ( $row as $col=>&$val ) {
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