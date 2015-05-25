<?php


class GeneralTest extends PHPUnit_Framework_TestCase
{

  private static $windows = false;

  public function setUp()
  {

  }

  public function tearDown() {
    // echo "\n";
  }


  /**
   *
   */
  public function testInit()
  {
    \RadSectors\Microshaft\SqlShim::init();

    // if ( !extension_loaded('sqlsrv') && function_exists('sqlsrv_connect') )
    // {
    //   echo "sqlshim loaded.";
    // }
    // elseif ( extension_loaded('sqlsrv') )
    // {
    //   echo "sqlshim not loaded because sqlsrv previously loaded.\n";
    //   echo "\tContinuing tests using real sqlsrv functions...";
    // }

    $exists = function_exists('sqlsrv_connect');
    $this->assertTrue($exists);
    return $exists;
  }


  /**
   * @depends testInit
   * @requires extension sqlsrv
   */
  public function testConstants( $init )
  {
    $constants = get_defined_constants(true)['sqlsrv'];

    foreach ( $constants as $const=>$v )
    {
      if ( strpos($const, 'SQLSRV_')===0 )
      {
        $cval = constant(\RadSectors\Microshaft\SqlShim::NAME . "::" .$const);
        $val = constant($const);
        $compare = ($val===$cval);
        echo !$compare ? "$const: c$cval vs g$val" : "";
        $this->assertTrue($compare);
      }
    }
    $functions = [
    //   'SQLSRV_PHPTYPE_STREAM' => [0, 'char', 'binary'],
    //   'SQLSRV_PHPTYPE_STRING' => [0, 1, 8000],
    //   'SQLSRV_SQLTYPE_BINARY' => [0, 1, 8000],
    //   'SQLSRV_SQLTYPE_CHAR' => [0, 1, 8000],
      'SQLSRV_SQLTYPE_DECIMAL',
    //   'SQLSRV_SQLTYPE_NCHAR' => [0, 1, 4000],
      'SQLSRV_SQLTYPE_NUMERIC',
    //   'SQLSRV_SQLTYPE_NVARCHAR' => [0, 1, 4000],
    //   'SQLSRV_SQLTYPE_VARBINARY' => [0, 1, 8000],
    //   'SQLSRV_SQLTYPE_VARCHAR' => [0, 1, 8000],
    ];
    $functions = get_extension_funcs('sqlsrv');
    foreach ( $functions as $func )
    {
      if ( strpos($func, 'SQLSRV_')===0 )
      {
        try
        {
          $cval = call_user_func(\RadSectors\Microshaft\SqlShim::NAME . "::$func", -9000);
          // $cval = call_user_func($func, 1);
          $gval = call_user_func($func, -9000);
          $compare = ($gval===$cval);
          echo !$compare ? "$func: c$cval vs g$gval\n" : "";
          $this->assertTrue($compare);
        }
        catch ( Exception $e )
        {
          // var_dump($e);
        }
      }
    }
  }

  /**
   * @depends testInit
   */
  public function testConnection( $init )
  {
    if ( !$init ) return false;

    $con = sqlsrv_connect(
      HOSTNAME,
      [
        "Database" => DATABASE,
        "UID" => USERNAME,
        "PWD" => PASSWORD,
        'CharacterSet'=>'UTF-8',
      ]
    );

    // if ( is_object($con) && get_class($con)=="PDO" )
    // {
    //   echo "sqlshim.";
    // }
    // elseif ( get_resource_type($con)=="SQL Server Connection" )
    // {
    //   echo "sqlsrv.";
    // }
    // else
    // {
    //   echo "failure.";
    //   var_dump(sqlsrv_errors());
    // }
    $this->assertTrue($con!==false);

    return $con;
  }


  /**
   * @depends testConnection
   */
  public function testClientInfo( $con )
  {
    if ( $con!==false )
    {
      // var_dump(sqlsrv_client_info($con));
    }
  }


  /**
   * @depends testConnection
   */
  public function testQueries( $con )
  {
    if ( $con!==false )
    {
      $stmt = sqlsrv_query($con, "SELECT * FROM Northwind.Customers;", ['UK', 'Sweden', 'Mexico']);
      $rows = [];
      while ( $row = sqlsrv_fetch_object($stmt) )
      {
        $rows[] = $row;
      }
      $this->assertCount(91, $rows);

      $stmt = sqlsrv_query($con, "SELECT * FROM Northwind.Customers WHERE Country IN (?, ?, ?);", ['UK', 'Sweden', 'Mexico']);
      $rows = [];
      while ( $row = sqlsrv_fetch_object($stmt) )
      {
        $rows[] = $row;
      }
      $this->assertCount(14, $rows);

      return $con;
    }
    return false;
  }


  /**
   * @depends testConnection
   */
  public function testTransactions( $con )
  {
    if ( $con!==false )
    {
      $stmt = sqlsrv_begin_transaction($con);

      // sqlsrv_prepare($cono, )

      sqlsrv_rollback($con);

      return $con;
    }
    return false;
  }


  /**
   * @depends testConnection
   */
  public function testDataTypes( $con )
  {
    if ( $con!==false )
    {

      return $con;
    }
    return false;
  }
}
