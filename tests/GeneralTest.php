<?php


class GeneralTest extends PHPUnit_Framework_TestCase
{
  private static $con;

  private static $windows = false;

  public function setup()
  {

  }

  public function testSqlsrvExists()
  {
    if ( function_exists('sqlsrv_connect') )
  	{
      self::$windows = true;
    }
    else {
      \RadSectors\Microshaft\SqlShim::init();
    }
  }

  public function testConnection()
  {
    self::$con = sqlsrv_connect(
      HOSTNAME,
      [
        "Database" => DATABASE,
        "UID" => USERNAME,
        "PWD" => PASSWORD,
        'CharacterSet'=>'UTF-8',
      ]
    );

    if ( !self::$windows )
    {
      $this->assertInstanceOf("PDO", self::$con);
    }
    else
    {
      $this->assertInternalType("resource", self::$con);
    }
  }

  public function testSimpleQuery()
  {
    $stmt = sqlsrv_query(self::$con, "SELECT * FROM Northwind.Customers;");

    while ( $row = sqlsrv_fetch_object($stmt) )
    {
      $rows[] = $row;
    }

    $this->assertCount(91, $rows);
  }

  public function testParameterizedQuery()
  {
    $stmt = sqlsrv_query(self::$con, "SELECT * FROM Northwind.Customers WHERE Country IN (?, ?, ?) ;", ['UK', 'Sweden', 'Mexico']);
    while ( $row = sqlsrv_fetch_object($stmt) )
    {
      $rows[] = $row;
    }

    $this->assertCount(14, $rows);
  }
}
