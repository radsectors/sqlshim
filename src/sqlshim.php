<?php

namespace radsectors;

/**
 * PHP sqlsrv functions for Linux/OS X.
 */
final class sqlshim
{
    private static $config;
    private static $sqlsrvconf;
    private static $cstrdefaults;
    private static $tabcursor;
    private static $tabfetch;
    private static $tabscroll;
    private static $tablogsys;
    private static $client_info;
    private static $errors = [];
    private static $_init = false;

    // const SQL_INT_MAX = 2147483648;

    private function __construct()
    {
        // can't call me.
    }

    /**
     * Initialize the sqlshimmage.
     * Called by globals.php
     *
     * @param array $config
     *
     * @return bool Returns whether sqlshim can have its globals defined.
     */
    public static function init()
    {
        $loadable = !extension_loaded('sqlsrv') && !function_exists('sqlsrv_connect');

        if (self::$_init) {
            return $loadable;
        }

        self::$config = (object)[
            'prefix' => 'dblib',
            'driver' => 'sqlshim',
            'version' => '7.2',
            'autotype_fields' => false,
        ];

        self::$sqlsrvconf = (object)[
            'clientbuffermaxkbsize' => 10240,
            'logseverity' => self::LOG_SEVERITY_ERROR,
            'logsubsystems' => self::LOG_SYSTEM_OFF,
            'warningsreturnaserrors' => 1,
        ];

        self::$cstrdefaults = [
            'applicationintent' => 'ReadWrite',
            'characterset' => self::ENC_CHAR,
            'connectionpooling' => 1,
            'encrypt' => 0,
            'failover_partner' => null,
            'logintimeout' => null,
            'multipleactiveresultsets' => 1,
            'multisubnetfailover' => 'No',
            'quoteid' => 1,
            'returndatesasstrings' => 0,
            'traceon' => 0,
            'transactionisolation' => self::TXN_READ_COMMITTED,
            'trustedservercertificate' => 0,
        ];

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
        self::$tablogsys = [
            self::LOG_SYSTEM_ALL => \PDO::ERRMODE_EXCEPTION,
            self::LOG_SYSTEM_OFF => \PDO::ERRMODE_SILENT,
            self::LOG_SYSTEM_INIT => \PDO::ERRMODE_EXCEPTION,
            self::LOG_SYSTEM_CONN => \PDO::ERRMODE_EXCEPTION,
            self::LOG_SYSTEM_STMT => \PDO::ERRMODE_EXCEPTION,
            self::LOG_SYSTEM_UTIL => \PDO::ERRMODE_EXCEPTION,
        ];

        self::$client_info = [
            'odbc' => [
                'DriverDLLName' => function () {
                    return 'Deprecated';
                },
                'DriverODBCVer' => function () {
                    return 'Deprecated';
                },
                'DriverVer' => function () {
                    return 'Deprecated';
                },
                'ExtensionVer' => function () {
                    return phpversion('pdo_odbc');
                },
            ],
            'dblib' => [
                'DriverDLLName' => function () {
                    return 'pdo_dblib.so';
                },
                'DriverODBCVer' => function () {
                    return 'N/A';
                },
                'DriverVer' => function () {
                    return (new \ReflectionExtension('pdo_dblib'))->getVersion();
                },
                'ExtensionVer' => function () {
                    return 'radsectors\sqlshim '.self::getVersion().'';
                },
            ],
        ];

        self::$_init = true;

        return $loadable;
    }

    /**
     * @internal Gets sqlshim version number.
     *
     * @return string sqlshim version number.
     */
    private static function getVersion()
    {
        return 'alpha'; // TODO: get this from git tag if possible.
    }

    /**
     * @internal Collects error info to internal error log
     *
     * @param mixed $e Error Info
     */
    private static function logErr($e)
    {
        if (is_array($e) && count($e) > 2) {
            self::$errors[] = [
                0 => $e[0],
                'SQLSTATE' => $e[0],
                1 => $e[1],
                'code' => $e[1],
                2 => $e[2] ?: '',
                'message' => $e[2] ?: '',
            ];

            return true;
        }

        is_a($e, 'PDOException') && (
            (!empty($e->errorInfo) && self::logErr($e->errorInfo)) ||
            (bool) preg_replace_callback(
                "/SQLSTATE\[(\d+)\] .*: (\d+) (.*)/",
                function ($matches) {
                    if (count($matches)) {
                        self::logErr(array_slice($matches, 1));

                        return '1';
                    }

                    return '0';
                },
                $e->getMessage()
            )
        ) ||
        self::logErr(['SQLSHIM', -0, 'Unknown sqlshim error.']);
    }

    private static function calcSize($c, $max = 8000)
    {
        $c = intval($c);
        $r = 8387584;
        if ($c == -1) {
            $r += 512;
        } elseif ($c > 0 && $c <= $max) {
            $r = $c * 512;
        }

        return $r;
    }

    private static function calcSizeStr($en)
    {
        $en = strval($en);
        $r = 524288;
        if ($en == 'binary') {
            $r += 1;
        } elseif ($en == 'char') {
            $r += 1.5;
        }

        return intval($r * 512);
    }

    private static function calcPrecScale($prec, $scale)
    {
        $max_precision = 38;
        $scale = 8389120;
        $noscale = 2139095040;
        $invalid = -1;

        $p = is_numeric($prec) ? intval($prec) : $invalid;
        $s = is_numeric($scale) ? intval($scale) : $invalid;
        if ($s == -257) {
            $s = $invalid;
        }
        if ($s < -256) {
            $s = $s % 256;
        }

        if (($s == $invalid && ($p < 0 || $p > $max_precision))) {
            $c = 0;
            $b = $s;
        } else {
            $c = $s * $scale;
            $b = $p - $s;

            if ($p > $max_precision) {
                $p = $invalid;
            }
            if ($p < 0) {
                $p = $invalid;
                $c = ($s + 1) * $scale;
                $b = -$s - 2;
            }
            if ($s > $p) {
                $s = $invalid;
                $c = $noscale;
                $b = $p;
            }
            if ($p == $invalid && $s == $invalid) {
                $c = $scale + $noscale;
                $b = -2;
            }
        }

        return intval($c + ($b * 512));
    }

    /**
     * typifyRow.
     *
     * @param object|array $row Database record to be typed.
     *
     * @return $object|array Returns the typed record.
     */
    private static function typifyRow($row)
    {
        foreach ($row as &$value) {
            $value = self::typifyField($value);
        }

        return $row;
    }

    /**
     * typifyField.
     * TODO: typifyField() 7-year-old comment http://php.net/manual/en/ref.pdo-dblib.php#89827 may be on to something
     *
     * @param mixed $val The value for which the type is to be guessed.
     *
     * @return mixed Returns the typed value.
     */
    private static function typifyField($val)
    {
        if (self::$config->autotype_fields) {
            if (is_numeric($val)) {
                if (is_float($val)) {
                    return floatval($val);
                }
                if (is_int($val)) {
                    return intval($val);
                }
            }
        }
        // always try to convert dates because reasons.
        if (preg_match('/^(19|20)\d\d-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01]).*/', $val)) {
            try {
                return new \DateTime($val);
            } catch (\Exception $e) {
                /* continue... */
            }
        }

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
    const PHPTYPE_FLOAT = 3;
    const PHPTYPE_NULL = 1;

    const ENC_BINARY = 'binary';
    const ENC_CHAR = 'char';

    const SQLTYPE_BIGINT = -5;
    const SQLTYPE_BIT = -7;
    const SQLTYPE_DATE = 5211;
    const SQLTYPE_DATETIME = 25177693;
    const SQLTYPE_DATETIME2 = 58734173;
    const SQLTYPE_DATETIMEOFFSET = 58738021;
    const SQLTYPE_FLOAT = 6;
    const SQLTYPE_IMAGE = -4;
    const SQLTYPE_INT = 4;
    const SQLTYPE_MONEY = 33564163;
    const SQLTYPE_NTEXT = -10;
    const SQLTYPE_REAL = 7;
    const SQLTYPE_SMALLDATETIME = 8285;
    const SQLTYPE_SMALLINT = 5;
    const SQLTYPE_SMALLMONEY = 33559555;
    const SQLTYPE_TEXT = -1; // -1 means unlimited
    const SQLTYPE_TIME = 58728806;
    const SQLTYPE_TIMESTAMP = 4606;
    const SQLTYPE_TINYINT = -6;
    const SQLTYPE_UNIQUEIDENTIFIER = -11;
    const SQLTYPE_UDT = -151;
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

    public static function PHPTYPE_STREAM($encoding)
    {
        return self::calcSizeStr($encoding) + 67108870;
    }

    public static function PHPTYPE_STRING($encoding)
    {
        return self::calcSizeStr($encoding) + 67108868;
    }

    public static function SQLTYPE_BINARY($byteCount)
    {
        return self::calcSize($byteCount) + 2139095550;
    }

    public static function SQLTYPE_CHAR($charCount)
    {
        return self::calcSize($charCount) + 2139095041;
    }

    public static function SQLTYPE_DECIMAL($precision, $scale)
    {
        return self::calcPrecScale($precision, $scale) + 3;
    }

    public static function SQLTYPE_NCHAR($charCount)
    {
        return self::calcSize($charCount, 4000) + 2139095544;
    }

    public static function SQLTYPE_NUMERIC($precision, $scale)
    {
        return self::calcPrecScale($precision, $scale) + 2;
    }

    public static function SQLTYPE_NVARCHAR($charCount)
    {
        return self::calcSize($charCount, 4000) + 2139095543;
    }

    public static function SQLTYPE_VARBINARY($byteCount)
    {
        return self::calcSize($byteCount) + 2139095549;
    }

    public static function SQLTYPE_VARCHAR($charCount)
    {
        return self::calcSize($charCount) + 2139095052;
    }

    public static function begin_transaction(\PDO $conn)
    {
        return $conn->beginTransaction();
    }

    public static function cancel(\PDOStatement $stmt)
    {
        // no PDO equivalent for this API
        return true;
    }

    public static function client_info(\PDO $conn)
    {
        $return = [];
        $info = self::$client_info[self::$config->prefix];
        foreach ($info as $i => $call) {
            $return[$i] = $call();
        }

        return $return;
    }

    public static function close(\PDO $conn)
    {
        return $conn = null;
    }

    public static function commit(\PDO $conn)
    {
        $conn->commit();
    }

    public static function config($config = [])
    {
        // process options
        foreach ($config as $opt => $val) {
            $opt = strtolower($opt);
            if (is_string($val)) {
                $val = strtolower($val);
            }
            switch ($opt) {
                case 'prefix':
                    switch ($val) {
                        case 'sybase':
                        case 'mssql':
                            $val = 'dblib';
                            // no break
                        case 'dblib':
                        case 'odbc':
                            self::$config->$opt = $val;
                            break;
                        default:
                            break;
                    }
                    break;
                case 'driver':
                case 'version':
                    self::$config->$opt = $val;
                    break;
                case 'autotype_fields':
                case 'warningsreturnaserrors':
                    self::$config->$opt = (bool) $val;
                    // no break
                default:
                    self::logErr(["IMSSP", -14, "An invalid parameter was passed to sqlsrv_configure."]);
                    break;
            }
        }
    }

    public static function configure($setting, $value)
    {
        $setting = strtolower($setting);
        if (property_exists(self::$sqlsrvconf, $setting)) {
            switch ($setting) {
                case 'clientbuffermaxkbsize':
                case 'logseverity':
                    return true;
                    break;
                case 'logsubsystems':
                    if (isset(self::$tablogsys[$value])) {
                        self::$sqlsrvconf->$setting = $value;
                        return true;
                    }
                    break;
                case 'warningsreturnaserrors':
                    self::$sqlsrvconf->$setting = !!$value;
                    return true;
                    break;
                default:
                    break;
            }
        }
        // $logseverity = [
        //     self::LOG_SEVERITY_ALL,
        //     self::LOG_SEVERITY_ERROR,
        //     self::LOG_SEVERITY_WARNING,
        //     self::LOG_SEVERITY_NOTICE,
        // ];

        return false;
    }

    public static function connect($serverName, $connectionInfo)
    {
        // strip tcp: prefix
        $serverName = str_replace('tcp:', '', $serverName);
        // lowercase all keys
        $connectionInfo = array_change_key_case($connectionInfo);
        // default port (1433)
        $port = isset($connectionInfo['port']) ? $connectionInfo['port'] : '1433';
        // inline port will take precedent over $connectionInfo['port']
        list(
            $connectionInfo['servername'],
            $connectionInfo['port']
        ) = explode(',', "$serverName,$port", 3);

        $cstrparams = [
            'dblib' => [
                'version' => 'version',
                'servername' => 'host',
                'database' => 'dbname',
                'port' => 'port',
                'characterset' => 'charset',
                'encrypt' => 'secure', // not used (http://php.net/manual/en/ref.pdo-dblib.connection.php)
            ],
            'odbc' => [
                'version' => 'tds_version',
                'servername' => 'server',
                'database' => 'database',
                'port' => 'port',
                'characterset' => 'clientcharset',
                'driver' => 'driver',
            ],
        ];
        $cstr = self::$config->prefix.':';
        foreach ($cstrparams[self::$config->prefix] as $i => $par) {
            if (empty($par)) {
                continue;
            }
            $i = strtolower($i);
            if (isset($connectionInfo[$i])) {
                $cstr .= "$par=$connectionInfo[$i];";
            } elseif (isset(self::$config->$i)) {
                $cstr .= "$par=".self::$config->$i.';';
            }
        }

        try {
            $conn = new \PDO($cstr, $connectionInfo['uid'], $connectionInfo['pwd']);
        } catch (\PDOException $e) {
            self::logErr($e);

            return false;
        }

        $conn->prepare('SET ANSI_WARNINGS ON')->execute();
        $conn->prepare('SET ANSI_PADDING ON')->execute();
        $conn->prepare('SET ANSI_NULLS ON')->execute();
        $conn->prepare('SET QUOTED_IDENTIFIER ON')->execute();
        $conn->prepare('SET CONCAT_NULL_YIELDS_NULL ON')->execute();

        $conn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        $conn->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_NATURAL);
        $conn->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false); // this doesn't do shit. everything comes out string.
        $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(\PDO::ATTR_ORACLE_NULLS, \PDO::NULL_NATURAL);
        // $conn->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);

        return $conn;
    }

    public static function errors($errorsOrWarnings = self::ERR_ALL)
    {
        // TODO: errors() figure out if we can differentiate between errors and warnings.
        return self::$errors;
    }

    public static function execute(\PDOStatement $stmt)
    {
        return $stmt->execute();
    }

    public static function fetch_array(\PDOStatement $stmt, $fetchType = self::FETCH_BOTH, $row = self::SCROLL_NEXT, $offset = 0)
    {
        try {
            $array = $stmt->fetch(self::$tabfetch[$fetchType], self::$tabscroll[$row], $offset);
            if (is_array($array)) {
                return self::typifyRow($array);
            }
        } catch (\PDOException $e) {
            self::logErr($e);

            return false;
        }

        return; // fetch with no errors (null)
    }

    public static function fetch_object(\PDOStatement $stmt, $className = 'stdClass', $ctorParams = [], $row = self::SCROLL_NEXT, $offset = 0)
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
            if (is_array($object)) {
                $object = (object) $object;
            }
            if (is_object($object)) {
                return self::typifyRow($object);
            }
        } catch (\PDOException $e) {
            self::logErr($e);

            return false;
        }

        return; // fetch with no more records (null)
    }

    public static function fetch(\PDOStatement $stmt, $row, $offset)
    {
        try {
            $array = $stmt->fetch(\PDO::FETCH_NUM, self::$tabscroll[$row], $offset);
            if (is_array($array)) {
                return self::typifyRow($array);
            }
        } catch (\PDOException $e) {
            self::logErr($e);

            return false;
        }

        return;
    }

    public static function field_metadata(\PDOStatement $stmt)
    {
        // NOTE: field_metadata() - PDOStatement->getColumnMeta() is an "EXPERIMENTAL" function. And its request not supported by driver.
        // HACK:  7-year-old comment http://php.net/manual/en/ref.pdo-dblib.php#89827 may be on to something
        return false;
        // $metadata = [];
        // for ( $i=0; $i<$stmt->columnCount(); $i++ )
        // {
        //   $metadata[] = $stmt->getColumnMeta($i);
        // }
        // return $metadata;
    }

    public static function free_stmt(\PDOStatement $stmt)
    {
        return $stmt->closeCursor();
    }

    public static function get_config($setting)
    {
        $setting = strtolower($setting);
        if (isset(self::$sqlsrvconf->$setting)) {
            return self::$sqlsrvconf->$setting;
        }
        self::logErr(["IMSSP", -14, "An invalid parameter was passed to sqlsrv_get_config."]);

        return false;
    }

    public static function get_field(\PDOStatement $stmt, $fieldIndex = 0, $getAsType = null)
    {
        $value = $stmt->fetchColumn($fieldIndex);
        // NOTE: AFAIK, there's no way to get any field to come out as anything but a string. So for now, we'll assume string.
        // TODO:get_field() - test to see what happens when asking for a non-convertable value
        switch ($getAsType) {
            case self::PHPTYPE_INT:
                return (int)$value;
            case self::PHPTYPE_FLOAT:
                return (float)$value;
            case self::PHPTYPE_DATETIME:
                // TODO:get_field() - test this especially.
                return date_create($value) || new DateTime();
            case self::PHPTYPE_STRING(self::ENC_BINARY):
                return base64_encode($value);
            case self::PHPTYPE_STRING(self::ENC_CHAR):
                return "$value";
            case self::PHPTYPE_STREAM(self::ENC_BINARY):
                $value = base64_encode($value);
                return fopen("data://base64,$value", 'r+');
            case self::PHPTYPE_STREAM(self::ENC_CHAR):
                return fopen("data://text/plain,$value", 'r+');
            case self::PHPTYPE_NULL:
                return null;
            default:
        }

        return $value;
    }

    public static function has_rows(\PDOStatement $stmt)
    {
        // REVIEW: has_rows() - this doesn't work unless $stmt->rowCount() works.
        // might just need: [PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL] option set
        return (bool) $stmt->rowCount();
    }

    public static function next_result(\PDOStatement $stmt)
    {
        return $stmt->nextRowset();
    }

    public static function num_fields(\PDOStatement $stmt)
    {
        return $stmt->columnCount();
    }

    public static function num_rows(\PDOStatement $stmt)
    {
        // REVIEW: num_rows() - $stmt->rowCount DOES NOT work for SELECTs in MSSQL PDO.
        $sql = $stmt->queryString;
        if (stripos($sql, 'select') >= 0) {
            $sql = preg_replace('/SELECT .* FROM/', 'SELECT COUNT(*) AS count FROM', $sql);
            $row = $stmt->conn->query($sql)->fetch(\PDO::FETCH_NUM);
            if (is_array($row) && count($row)) {
                return reset($row);
            }
        }

        return false;
    }

    public static function prepare(\PDO $conn, $sql, $params = [], $options = [])
    {
        // translate options
        $options = array_intersect_key($options, array_flip([
            'QueryTimeout',
            'SendStreamParamsAtExec',
            'Scrollable',
        ]));
        foreach ($options as $opt => $val) {
            switch ($opt) {
                case 'QueryTimeout':
                    if (is_numeric($val)) {
                        $conn->setAttribute(\PDO::SQLSRV_ATTR_QUERY_TIMEOUT, intval($val));
                    }
                    break;
                case 'SendStreamParamsAtExec':
                    // TODO: prepare() - figure out what this is and what to do with it
                    break;
                case 'Scrollable':
                    if (isset(self::$tabcursor[$val])) {
                        $options[\PDO::ATTR_CURSOR] = self::$tabcursor[$val];
                    }
                    break;
                default:
                    break;
            }
        }

        $sql = stripslashes($sql); // REVIEW: purpose?

        if (!is_array($params)) {
            $params = [];
        }
        $count = mb_substr_count($sql, '?');
        $params = array_slice(array_pad($params, $count, null), 0, $count, false);

        try {
            $stmt = $conn->prepare($sql, $options);
            foreach ($params as $i => $val) {
                if (is_array($val)) {
                    $arr = $val;
                    // TODO: try to support more fully
                    // QUESTION: need type table?
                    $val = reset($arr);
                    $dir = next($arr);
                    $ptype = next($arr);
                    $stype = next($arr);
                }
                $bound = $stmt->bindValue($i + 1, $val);
            }
            $stmt->conn = $conn; // for ref
            return $stmt;
        } catch (\PDOException $e) {
            self::logErr($e->errorInfo);

            return false;
        }
    }

    public static function query(\PDO $conn, $sql, $params = [], $options = [])
    {
        $stmt = self::prepare($conn, $sql, $params, $options);

        try {
            if (self::execute($stmt)) {
                return $stmt;
            } else {
                self::logErr($stmt->errorInfo());

                return $stmt;
            }
        } catch (\PDOException $e) {
            self::logErr($e->errorInfo);
        }

        return false;
    }

    public static function rollback(\PDO $conn)
    {
        return $conn->rollBack();
    }

    public static function rows_affected(\PDOStatement $stmt)
    {
        return $stmt->rowCount();
    }

    public static function send_stream_data(\PDOStatement $stmt)
    {
        // TODO: send_stream_data() - what is this?
    }

    public static function server_info(\PDO $conn)
    {
        $stmt = self::query(
            $conn,
            "SELECT
                DB_NAME() AS CurrentDatabase,
                @@VERSION AS SQLServerVersion,
                @@SERVERNAME AS SQLServerName
            ;"
        );
        if ($stmt !== false) {
            $info = self::fetch_array($stmt, self::FETCH_ASSOC);
            $info['SQLServerVersion'] = preg_replace('/.* (\d+\.\d+\.\d+)\.\d+.*/s', '$1', $info['SQLServerVersion']);
            return $info;
        }
        return false;
    }
}
