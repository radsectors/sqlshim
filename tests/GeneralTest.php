<?php

use RadSectors\SqlShim;

class GeneralTest extends PHPUnit_Framework_TestCase
{
    private static $globals = false;

    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    /**
     *
     */
    public function testInit()
    {
        \RadSectors\SqlShim::init();

        if (!extension_loaded('sqlsrv') && function_exists('sqlsrv_connect')) {
            static::$globals = true;
        }
        if (extension_loaded('sqlsrv')) {
            static::$sqlsrv = true;
        }

        $exists = function_exists('sqlsrv_connect');
        $this->assertTrue($exists);

        return $exists;
    }

    /**
     * @depends testInit
     * @requires extension sqlsrv
     */
    public function testConstants($init)
    {
        // test PHPTYPE constants
        $constants = get_defined_constants(true)['sqlsrv'];
        foreach ($constants as $const => $v) {
            if (strpos($const, 'SQLSRV_') === 0) {
                $cval = constant(SqlShim::NAME.'::'.str_replace('SQLSRV_', '', $const));
                $val = constant($const);
                $compare = ($val === $cval);
                echo !$compare ? "$const: c$cval vs g$val\n" : '';
                $this->assertTrue($compare);
            }
        }

        // test PHPTYPE functions
        $functions = [
            'PHPTYPE_STREAM' => ['bird', '', 0, 1, 2, true, false, 'char', 'binary'],
            'PHPTYPE_STRING' => ['bird', '', 0, 1, 2, true, false, 'char', 'binary'],
            'SQLTYPE_BINARY' => [-8001, -8000, -1, 0, 1, 8000, 8001],
            'SQLTYPE_CHAR' => [-8001, -8000, -1, 0, 1, 8000, 8001],
            'SQLTYPE_DECIMAL' => [[-258, 258], [-258, 258]],
            'SQLTYPE_NCHAR' => [-8001, -8000, -1, 0, 1, 8000, 8001],
            'SQLTYPE_NUMERIC' => [[-258, 258], [-258, 258]],
            'SQLTYPE_NVARCHAR' => [-8001, -8000, -1, 0, 1, 8000, 8001],
            'SQLTYPE_VARBINARY' => [-8001, -8000, -1, 0, 1, 8000, 8001],
            'SQLTYPE_VARCHAR' => [-8001, -8000, -1, 0, 1, 8000, 8001],
        ];
        // $functions = get_extension_funcs('sqlsrv');
        foreach ($functions as $func => $args) {
            if (strstr($func, 'DECIMAL') || strstr($func, 'NUMERIC')) {
                for ($p1 = $args[0][0]; $p1 <= $args[0][1]; ++$p1) {
                    for ($p2 = $args[1][0]; $p2 <= $args[1][1]; ++$p2) {
                        $this->tryfunction($func, [$p1, $p2]);
                    }
                }
            } else {
                // if ( strstr($func, 'STREAM') || strstr($func, 'STRING') )

                foreach ($args as $arg) {
                    $this->tryfunction($func, [$arg]);
                }
            }
        }
    }

    private function tryfunction($func, $args)
    {
        $compare = null;
        $cval = call_user_func_array(\RadSectors\SqlShim::NAME."::$func", $args);
        $gval = call_user_func_array("SQLSRV_$func", $args);
        $compare = ($gval === $cval);
        if (!$compare) {
            // var_dump([$gval,$cval]);
            echo "$func(".implode(',', $args)."): srv: $gval /shim: $cval\n";
        }
        $this->assertTrue($compare);
        // return $compare;
    }

    /**
     * @depends testInit
     */
    public function testConnection($init)
    {
        if (!$init) {
            return false;
        }

        $con = sqlsrv_connect(
            HOSTNAME,
            [
                'Database' => DATABASE,
                'UID' => USERNAME,
                'PWD' => PASSWORD,
                'CharacterSet' => 'UTF-8',
            ]
        );

        if (is_object($con) && get_class($con) == 'PDO') {
            //
        } elseif (is_resource($con) && get_resource_type($con) == 'SQL Server Connection') {
            //
        } else {
            echo "Errors:\n";
            var_dump(sqlsrv_errors());
        }
        $this->assertTrue($con !== false);

        return $con;
    }

    /**
     * @depends testConnection
     */
    public function testClientInfo($con)
    {
        //var_dump(sqlsrv_client_info($con));
    }

    /**
     * @depends testConnection
     */
    public function testServer($con)
    {
        // var_dump(SqlShim::server_info($con));
        // var_dump(sqlsrv_server_info($con));
    }

    /**
     * @depends testConnection
     */
    public function testQueries($con)
    {
        if ($con !== false) {
            $stmt = sqlsrv_query($con, 'SELECT * FROM Northwind.Customers;', null, ['Scrollable' => SQLSRV_CURSOR_KEYSET]);
            $rows = [];
            // var_dump(sqlsrv_num_rows($stmt));
            // exit;
            while ($row = sqlsrv_fetch_array($stmt)) {
                $rows[] = $row;
            }
            $this->assertCount(91, $rows);

            // var_dump(sqlsrv_field_metadata($stmt));

            $stmt = sqlsrv_query($con, 'SELECT * FROM Northwind.Customers WHERE Country IN (?, ?, ?);', ['UK', 'Sweden', 'Mexico']);
            $rows = [];
            while ($row = sqlsrv_fetch_object($stmt)) {
                $rows[] = $row;
            }
            $this->assertCount(14, $rows);
        }
    }

    /**
     * @depends testConnection
     */
    public function testStoredProcedure($con)
    {
        if ($con !== false) {
            $stmt = sqlsrv_query($con, '{ CALL SalesByCategory( ?, ? ) }', ['Meat/Poultry', null]);
            $rows = [];
            if ($stmt) {
                while ($row = sqlsrv_fetch_array($stmt)) {
                    $rows[] = $row;
                }
            }
            // var_dump($rows);
        }

        return false;
    }

    /**
     * @depends testConnection
     */
    public function testTransactions($con)
    {
        if ($con !== false) {
            $stmt = sqlsrv_begin_transaction($con);
            // sqlsrv_prepare($cono, )
            sqlsrv_rollback($con);
        }

        return false;
    }

    /**
     * @depends testConnection
     */
    public function testDataTypes($con)
    {
        if ($con !== false) {
            return $con;
        }

        return false;
    }
}
