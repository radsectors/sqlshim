<?php
namespace radsectors;

class GeneralTest extends \PHPUnit\Framework\TestCase
{
    private static $sqlshim = false;
    private static $globals = false;
    private static $sqlsrv = false;

    public function setUp(): void
    {
    }

    public function tearDown(): void
    {
    }

    /**
     *
     */
    public function testInit()
    {
        echo "\nInitialization tests...\n";
        if (class_exists('\radsectors\sqlshim')) {
            static::$sqlshim = true;
        }
        if (extension_loaded('sqlsrv')) {
            static::$sqlsrv = true;
        } elseif (function_exists('sqlsrv_connect')) {
            static::$globals = true;
        }
        echo "sqlshim: ".(static::$sqlshim ? '' : 'not ')."loaded.\n".
            "sqlsrv : ".(static::$sqlsrv  ? '' : 'not ')."loaded.\n".
            "globals: ".(static::$globals ? '' : 'not ')."loaded.\n\n";

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
        echo "\nsqlshim and slqsrv present.\nRunning const comparison test...\n";
        // test PHPTYPE constants
        $constants = get_defined_constants(true)['sqlsrv'];
        foreach ($constants as $const => $v) {
            if (strpos($const, 'SQLSRV_') === 0) {
                $cval = constant('\radsectors\sqlshim::'.str_replace('SQLSRV_', '', $const));
                $gval = constant($const);
                $compare = ($gval === $cval);
                if (!$compare) {
                    echo "$const: srv: $gval / shim: $cval\n";
                } else {
                    $this->assertTrue($compare);
                }
            }
        }

        // test PHPTYPE functions
        $functions = [
            // 'PHPTYPE_STREAM' => ['rock', 'hello', '', 0, 1, 2, true, false, 'char', 'binary', null],
            // 'PHPTYPE_STRING' => ['rock', 'hello', '', 0, 1, 2, true, false, 'char', 'binary', null],
            'SQLTYPE_BINARY' => [-8001, -8000, -1, 0, 1, 8000, 8001],
            'SQLTYPE_CHAR' => [-8001, -8000, -1, 0, 1, 8000, 8001],
            'SQLTYPE_DECIMAL' => [[-3, 256], [-3, 256]],
            'SQLTYPE_NCHAR' => [-8001, -8000, -1, 0, 1, 8000, 8001],
            'SQLTYPE_NUMERIC' => [[-3, 256], [-3, 256]],
            'SQLTYPE_NVARCHAR' => [-8001, -8000, -1, 0, 1, 8000, 8001],
            'SQLTYPE_VARBINARY' => [-8001, -8000, -1, 0, 1, 8000, 8001],
            'SQLTYPE_VARCHAR' => [-8001, -8000, -1, 0, 1, 8000, 8001],
        ];
        // $functions = get_extension_funcs('sqlsrv');
        $tryfunc = function ($func, $args) {
            $compare = null;
            $cval = call_user_func_array("\\radsectors\\sqlshim::$func", $args);
            $gval = call_user_func_array("sqlsrv_$func", $args);
            $compare = ($gval === $cval);
            if (!$compare) {
                echo "$func(".implode(',', $args)."): srv: $gval / shim: $cval\n";
            } else {
                $this->assertTrue($compare);
            }
            return $compare;
        };
        foreach ($functions as $func => $args) {
            if (strstr($func, 'DECIMAL') || strstr($func, 'NUMERIC')) {
                $p1 = $args[0][0];
                $p2 = $args[1][0];
                for ($args[0][0]; $p1 <= $args[0][1]; $p1++) {
                    for ($args[1][0]; $p2 <= $args[1][1]; $p2++) {
                        $tryfunc($func, [$p1, $p2]);
                    }
                }
            } else {
                foreach ($args as $arg) {
                    $tryfunc($func, [$arg]);
                }
            }
        }
    }

    /**
     * @depends testInit
     */
    public function testConfigure($init)
    {
        echo "\nConfigure function test.\n";
        // TODO: change to NOT test for default values
        $this->assertTrue(sqlsrv_configure('ClientBufferMaxKBSize', 10240));
        $this->assertTrue(sqlsrv_configure('LogSeverity', SQLSRV_LOG_SEVERITY_ERROR));
        $this->assertTrue(sqlsrv_configure('LogSubsystems', SQLSRV_LOG_SYSTEM_OFF));
        $this->assertTrue(sqlsrv_configure('WarningsReturnAsErrors', true));

        $this->assertTrue(sqlsrv_get_config('ClientBufferMaxKBSize') == 10240);
        $this->assertTrue(sqlsrv_get_config('LogSeverity') == SQLSRV_LOG_SEVERITY_ERROR);
        $this->assertTrue(sqlsrv_get_config('LogSubsystems') == SQLSRV_LOG_SYSTEM_OFF);
        $this->assertTrue(sqlsrv_get_config('WarningsReturnAsErrors') == true);
    }

    /**
     * @depends testInit
     */
    public function testConnection($init)
    {
        if (!$init) {
            return false;
        }

        echo "\nConnection test... ";
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
            echo "sqlshim PDO connection.\n";
        } elseif (is_resource($con) && get_resource_type($con) == 'SQL Server Connection') {
            echo "sqlsrv connection.\n";
        } else {
            urp::failed(sqlsrv_errors());
        }
        $this->assertTrue($con !== false);

        return $con;
    }

    /**
     * @depends testConnection
     */
    public function testClientInfo($con)
    {
        echo "\nClient Info function test.\n";
        // TODO: make better tests
        $client_info = sqlsrv_client_info($con);
        urp::info($client_info);
        $this->assertTrue(is_array($client_info));
    }

    /**
     * @depends testConnection
     */
    public function testServer($con)
    {
        echo "\nServer info test.";
        // TODO: make better tests
        $this->assertTrue(is_array(sqlsrv_server_info($con)));
    }

    /**
     * @depends testConnection
     */
    public function testQueries($con)
    {
        echo "\nQuery tests...";
        if ($con !== false) {
            $stmt = sqlsrv_query(
                $con,
                'SELECT * FROM Northwind.Customers;',
                null,
                ['Scrollable' => SQLSRV_CURSOR_KEYSET]
            );
            $rows = [];
            // var_dump(sqlsrv_num_rows($stmt));
            // exit;
            while ($row = sqlsrv_fetch_array($stmt)) {
                $rows[] = $row;
            }
            $this->assertCount(91, $rows);

            // var_dump(sqlsrv_field_metadata($stmt));

            $params = [
                ['UK', SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STRING(SQLSRV_ENC_CHAR)],
                ['Sweden', SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STRING(SQLSRV_ENC_CHAR)],
                ['Mexico', SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STRING(SQLSRV_ENC_CHAR)],
            ];
            // pq($params);

            $stmt = sqlsrv_query($con, 'SELECT * FROM Northwind.Customers WHERE Country IN (?, ?, ?);', $params);
            $rows = [];
            while ($row = sqlsrv_fetch_object($stmt)) {
                $rows[] = $row;
            }
            // pq($rows);
            $this->assertCount(14, $rows);
        }
    }

    /**
     * @depends testConnection
     */
    public function testStoredProcedure($con)
    {
        echo "\nStored procedure test.";
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
        echo "\nTransaction test.";
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
