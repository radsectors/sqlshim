<?php
require dirname(__DIR__) . '/config.php';
require dirname(__DIR__) . '/vendor/autoload.php';

RadSectors\SqlShim\SqlShim::init();

class GeneralTest extends PHPUnit_Framework_TestCase
{
	public function testGeneralStuff()
	{
    $creds = array("Database" => DBNAME, "UID" => DBUSER, "PWD" => DBPASS,'CharacterSet'=>'UTF-8');
    $con = sqlsrv_connect(DBHOST, $creds);

    var_dump($con);

	}
}


