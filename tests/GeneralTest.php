<?php
use RadSectors\SqlShim;


class GeneralTest extends PHPUnit_Framework_TestCase
{
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
    \RadSectors\SqlShim::init();

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
    // $constants = get_defined_constants(true)['sqlsrv'];
    //
    // foreach ( $constants as $const=>$v )
    // {
    //   if ( strpos($const, 'SQLSRV_')===0 )
    //   {
    //     $cval = constant(SqlShim::NAME . "::" . str_replace('SQLSRV_', '', $const));
    //     $val = constant($const);
    //     $compare = ($val===$cval);
    //     echo !$compare ? "$const: c$cval vs g$val" : "";
    //     $this->assertTrue($compare);
    //   }
    // }
    $functions = [
    //   'PHPTYPE_STREAM' => [0, 'char', 'binary'],
    //   'PHPTYPE_STRING' => [0, 1, 8000],
    //   'SQLTYPE_BINARY' => [0, 1, 8000],
    //   'SQLTYPE_CHAR' => [0, 1, 8000],
      'SQLTYPE_DECIMAL',
    //   'SQLTYPE_NCHAR' => [0, 1, 4000],
      // 'SQLTYPE_NUMERIC',
    //   'SQLTYPE_NVARCHAR' => [0, 1, 4000],
    //   'SQLTYPE_VARBINARY' => [0, 1, 8000],
    //   'SQLTYPE_VARCHAR' => [0, 1, 8000],
    ];
    // $functions = get_extension_funcs('sqlsrv');
    foreach ( $functions as $func )
    {
      for( $p2=-10; $p2<10; $p2++ )
      {
        for( $p1=-10; $p1<38; $p1++ )
        {
          $cval = call_user_func(\RadSectors\SqlShim::NAME . "::$func", $p1, $p2);
          $gval = call_user_func("SQLSRV_$func", $p1, $p2);
          $compare = ($gval===$cval);
          if ( !$compare )
          {
            echo "$func($p2, $p1): global:$gval vs class:$cval\n";
          }
          // $this->assertTrue($compare);
        }
      }
    }
  }

  /**
   * @depends testInit
   */
  public function testConnection( $init )
  {
    return;
    if ( !$init ) return false;

    $con = sqlsrv_connect(
      HOSTNAME,
      [
        'Database' => DATABASE,
        'UID' => USERNAME,
        'PWD' => PASSWORD,
        'CharacterSet'=>'UTF-8',
      ]
    );

    if ( is_object($con) && get_class($con)=="PDO" )
    {
      //
    }
    elseif ( is_resource($con) && get_resource_type($con)=="SQL Server Connection" )
    {
      //
    }
    else
    {
      echo "Errors:\n";
      var_dump(sqlsrv_errors());
    }
    $this->assertTrue($con!==false);

    return $con;
  }


  /**
   * @depends testConnection
   */
  public function testClientInfo( $con )
  {
    //var_dump(sqlsrv_client_info($con));
  }


  /**
   * @depends testConnection
   */
  public function testServer( $con )
  {
    // var_dump(SqlShim::server_info($con));
    //var_dump(sqlsrv_server_info($con));
  }


  /**
   * @depends testConnection
   */
  public function testQueries( $con )
  {
    return;
    if ( $con!==false )
    {
      $stmt = sqlsrv_query($con, "SELECT * FROM Northwind.Customers;");
      $rows = [];
      while ( $row = sqlsrv_fetch_array($stmt) )
      {
        $rows[] = $row;
      }
      var_dump($rows);
      $this->assertCount(91, $rows);

      var_dump(sqlsrv_field_metadata($stmt));

      $stmt = sqlsrv_query($con, "SELECT * FROM Northwind.Customers WHERE Country IN (?, ?, ?);", ['UK', 'Sweden', 'Mexico']);
      $rows = [];
      while ( $row = sqlsrv_fetch_object($stmt) )
      {
        $rows[] = $row;
      }
      $this->assertCount(14, $rows);
    }
  }


  /**
   * @depends testConnection
   */
  // public function testStoredProcedure( $con )
  // {
  //   if ( $con!==false )
  //   {
  //     $stmt = sqlsrv_query($con, "{ CALL SalesByCategory( ?, ? ) }", ["Meat/Poultry", null]);
  //     $rows = [];
  //     while ( $row = sqlsrv_fetch_array($stmt) )
  //     {
  //       $rows[] = $row;
  //     }
  //     var_dump($rows);
  //
  //   }
  //   return false;
  // }


  /**
   * @depends testConnection
   */
  public function testTransactions( $con )
  {
    return;
    if ( $con!==false )
    {
      $stmt = sqlsrv_begin_transaction($con);

      // sqlsrv_prepare($cono, )

      sqlsrv_rollback($con);
    }
    return false;
  }


  /**
   * @depends testConnection
   */
  public function testDataTypes( $con )
  {
    return;
    if ( $con!==false )
    {

      return $con;
    }
    return false;
  }
}
