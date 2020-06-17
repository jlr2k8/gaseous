<?php
/**
* Created by Josh L. Rogers
* Copyright (c) 2018 All Rights Reserved.
* 4/10/2018
*
* Query.php
*
* Run basic PDO/MySQL queries
*
**/

namespace Db;

use Error;
use Log;
use PDO;
use PDOException;
use PDOStatement;

class Query extends PdoMySql
{
    const DEFAULT_ORDER_SORT    = 'ASC';
    const MAX_ROWS              = 18446744073709551615; // if you're storing this many rows in a table, you've got problems bigger than this integer....
    const FOR_DEFAULT_LOCK_TYPE = 'UPDATE';

    public $query, $con;
    protected $bind_array = [];
    protected $sql, $select, $from, $where, $or_where, $join, $inner_join, $cross_join, $straight_join, $left_join,
        $right_join, $natural_join, $natural_left_join, $natural_right_join, $group_by, $having, $window, $order_by,
        $limit, $for, $into, $update, $insert, $delete;

    protected static $select_expressions = [
        '*',
        'ALL',
        'DISTINCT',
        'DISTINCT_ROW',
        'HIGH_PRIORITY',
        'STRAIGHT_JOIN',
        'SQL_SMALL_RESULT',
        'SQL_BIG_RESULT',
        'SQL_BUFFER_RESULT',
        'SQL_NO_CACHE',
        'SQL_CACHE_FOUND_ROWS',
        'FROM',
        'PARTITION',
        'WHERE',
        'GROUP BY',
        'WITH ROLLUP',
        'HAVING',
        'WINDOW',
        'AS',
        'ORDER BY',
        'ASC',
        'DESC',
        'LIMIT',
        'OFFSET',
        'FOR',
        'UPDATE',
        'SHARE',
        'NOWAIT',
        'SKIP LOCKED',
        'LOCK IN SHARE MODE',
        'INTO OUTFILE',
        'CHARACTER SET',
        'INTO DUMPFILE',
        'INTO',
    ];

    protected static $update_expressions = [
        'LOW_PRIORITY',
        'SET',
        'WHERE',
        'ORDER BY',
        'LIMIT',
        'DEFAULT',
    ];
    
    protected static $insert_expressions = [
        'LOW_PRIORITY',
        'DELAYED',
        'HIGH_PRIORITY',
        'IGNORE',
        'INTO',
        'VALUE',
        'VALUES',
        'AS',
        'ON DUPLICATE KEY UPDATE',
        'DEFAULT',
        'ROW',
    ];

    protected static $delete_expressions = [
        'LOW_PRIORITY',
        'QUICK',
        'IGNORE',
        'FROM',
        'AS',
        'PARTITION',
        'WHERE',
        'ORDER BY',
        'LIMIT',
    ];
    
    protected static $functions = [
        '&',
        '>',
        '>>',
        '>=',
        '<',
        '<>',
        '!=',
        '<<',
        '<=',
        '<=>',
        '%',
        'MOD',
        '*',
        '+',
        '-',
        '-',
        '->',
        '->>',
        '/',
        ':=',
        '=',
        '=',
        '^',
        'ABS',
        'ACOS',
        'ADDDATE',
        'ADDTIME',
        'AES_DECRYPT',
        'AES_ENCRYPT',
        'AND',
        '&&',
        'ANY_VALUE',
        'AS',
        'ASCII',
        'ASIN',
        'ASYMMETRIC_DECRYPT',
        'ASYMMETRIC_DERIVE',
        'ASYMMETRIC_ENCRYPT',
        'ASYMMETRIC_SIGN',
        'ASYMMETRIC_VERIFY',
        'ATAN',
        'ATAN2',
        'AVG',
        'BENCHMARK',
        'BETWEEN',
        'BIN',
        'BIN_TO_UUID',
        'BINARY',
        'BIT_AND',
        'BIT_COUNT',
        'BIT_LENGTH',
        'BIT_OR',
        'BIT_XOR',
        'CAN_ACCESS_COLUMN',
        'CAN_ACCESS_DATABASE',
        'CAN_ACCESS_TABLE',
        'CAN_ACCESS_VIEW',
        'CASE',
        'CAST',
        'CEIL',
        'CEILING',
        'CHAR',
        'CHAR_LENGTH',
        'CHARACTER_LENGTH',
        'CHARSET',
        'COALESCE',
        'COERCIBILITY',
        'COLLATION',
        'COMPRESS',
        'CONCAT',
        'CONCAT_WS',
        'CONNECTION_ID',
        'CONV',
        'CONVERT',
        'CONVERT_TZ',
        'COS',
        'COT',
        'COUNT',
        'CRC32',
        'CREATE_ASYMMETRIC_PRIV_KEY',
        'CREATE_ASYMMETRIC_PUB_KEY',
        'CREATE_DH_PARAMETERS',
        'CREATE_DIGEST',
        'CUME_DIST',
        'CURDATE',
        'CURRENT_DATE',
        'CURRENT_ROLE',
        'CURRENT_TIME',
        'CURRENT_TIMESTAMP',
        'CURRENT_USER',
        'CURRENT_USER',
        'CURTIME',
        'DATABASE',
        'DATE',
        'DATE_ADD',
        'DATE_FORMAT',
        'DATE_SUB',
        'DATEDIFF',
        'DAY',
        'DAYNAME',
        'DAYOFMONTH',
        'DAYOFWEEK',
        'DAYOFYEAR',
        'DEFAULT',
        'DEGREES',
        'DENSE_RANK',
        'DIV',
        'ELT',
        'EXP',
        'EXPORT_SET',
        'EXTRACT',
        'ExtractValue',
        'FIELD',
        'FIND_IN_SET',
        'FIRST_VALUE',
        'FLOOR',
        'FORMAT',
        'FORMAT_BYTES',
        'FORMAT_PICO_TIME',
        'FOUND_ROWS',
        'FROM_BASE64',
        'FROM_DAYS',
        'FROM_UNIXTIME',
        'GeomCollection',
        'GeometryCollection',
        'GET_DD_COLUMN_PRIVILEGES',
        'GET_DD_CREATE_OPTIONS',
        'GET_DD_INDEX_SUB_PART_LENGTH',
        'GET_FORMAT',
        'GET_LOCK',
        'GREATEST',
        'GROUP_CONCAT',
        'GROUPING',
        'GTID_SUBSET',
        'GTID_SUBTRACT',
        'HEX',
        'HOUR',
        'ICU_VERSION',
        'IF',
        'IFNULL',
        'IN',
        'INET_ATON',
        'INET_NTOA',
        'INET6_ATON',
        'INET6_NTOA',
        'INSERT',
        'INSTR',
        'INTERNAL_AUTO_INCREMENT',
        'INTERNAL_AVG_ROW_LENGTH',
        'INTERNAL_CHECK_TIME',
        'INTERNAL_CHECKSUM',
        'INTERNAL_DATA_FREE',
        'INTERNAL_DATA_LENGTH',
        'INTERNAL_DD_CHAR_LENGTH',
        'INTERNAL_GET_COMMENT_OR_ERROR',
        'INTERNAL_GET_ENABLED_ROLE_JSON',
        'INTERNAL_GET_HOSTNAME',
        'INTERNAL_GET_USERNAME',
        'INTERNAL_GET_VIEW_WARNING_OR_ERROR',
        'INTERNAL_INDEX_COLUMN_CARDINALITY',
        'INTERNAL_INDEX_LENGTH',
        'INTERNAL_IS_ENABLED_ROLE',
        'INTERNAL_IS_MANDATORY_ROLE',
        'INTERNAL_KEYS_DISABLED',
        'INTERNAL_MAX_DATA_LENGTH',
        'INTERNAL_TABLE_ROWS',
        'INTERNAL_UPDATE_TIME',
        'INTERVAL',
        'IS',
        'IS_FREE_LOCK',
        'IS_IPV4',
        'IS_IPV4_COMPAT',
        'IS_IPV4_MAPPED',
        'IS_IPV6',
        'IS NOT',
        'IS NOT NULL',
        'IS NULL',
        'IS_USED_LOCK',
        'IS_UUID',
        'ISNULL',
        'JSON_ARRAY',
        'JSON_ARRAY_APPEND',
        'JSON_ARRAY_INSERT',
        'JSON_ARRAYAGG',
        'JSON_CONTAINS',
        'JSON_CONTAINS_PATH',
        'JSON_DEPTH',
        'JSON_EXTRACT',
        'JSON_INSERT',
        'JSON_KEYS',
        'JSON_LENGTH',
        'JSON_MERGE',
        'JSON_MERGE_PATCH',
        'JSON_MERGE_PRESERVE',
        'JSON_OBJECT',
        'JSON_OBJECTAGG',
        'JSON_OVERLAPS',
        'JSON_PRETTY',
        'JSON_QUOTE',
        'JSON_REMOVE',
        'JSON_REPLACE',
        'JSON_SCHEMA_VALID',
        'JSON_SCHEMA_VALIDATION_REPORT',
        'JSON_SEARCH',
        'JSON_SET',
        'JSON_STORAGE_FREE',
        'JSON_STORAGE_SIZE',
        'JSON_TABLE',
        'JSON_TYPE',
        'JSON_UNQUOTE',
        'JSON_VALID',
        'JSON_VALUE',
        'LAG',
        'LAST_DAY',
        'LAST_INSERT_ID',
        'LAST_VALUE',
        'LCASE',
        'LEAD',
        'LEAST',
        'LEFT',
        'LENGTH',
        'LIKE',
        'LineString',
        'LN',
        'LOAD_FILE',
        'LOCALTIME',
        'LOCALTIME',
        'LOCALTIMESTAMP',
        'LOCALTIMESTAMP',
        'LOCATE',
        'LOG',
        'LOG10',
        'LOG2',
        'LOWER',
        'LPAD',
        'LTRIM',
        'MAKE_SET',
        'MAKEDATE',
        'MAKETIME',
        'MASTER_POS_WAIT',
        'MATCH',
        'MAX',
        'MBRContains',
        'MBRCoveredBy',
        'MBRCovers',
        'MBRDisjoint',
        'MBREquals',
        'MBRIntersects',
        'MBROverlaps',
        'MBRTouches',
        'MBRWithin',
        'MD5',
        'MEMBER OF',
        'MICROSECOND',
        'MID',
        'MIN',
        'MINUTE',
        'MOD',
        'MONTH',
        'MONTHNAME',
        'MultiLineString',
        'MultiPoint',
        'MultiPolygon',
        'NAME_CONST',
        'NOT',
        '!',
        'NOT BETWEEN',
        'NOT IN',
        'NOT LIKE',
        'NOT REGEXP',
        'NOW',
        'NTH_VALUE',
        'NTILE',
        'NULLIF',
        'OCT',
        'OCTET_LENGTH',
        'OR',
        '||',
        'ORD',
        'PERCENT_RANK',
        'PERIOD_ADD',
        'PERIOD_DIFF',
        'PI',
        'Point',
        'Polygon',
        'POSITION',
        'POW',
        'POWER',
        'PS_CURRENT_THREAD_ID',
        'PS_THREAD_ID',
        'QUARTER',
        'QUOTE',
        'RADIANS',
        'RAND',
        'RANDOM_BYTES',
        'RANK',
        'REGEXP',
        'REGEXP_INSTR',
        'REGEXP_LIKE',
        'REGEXP_REPLACE',
        'REGEXP_SUBSTR',
        'RELEASE_ALL_LOCKS',
        'RELEASE_LOCK',
        'REPEAT',
        'REPLACE',
        'REVERSE',
        'RIGHT',
        'RLIKE',
        'ROLES_GRAPHML',
        'ROUND',
        'ROW_COUNT',
        'ROW_NUMBER',
        'RPAD',
        'RTRIM',
        'SCHEMA',
        'SEC_TO_TIME',
        'SECOND',
        'SESSION_USER',
        'SHA1',
        'SHA',
        'SHA2',
        'SIGN',
        'SIN',
        'SLEEP',
        'SOUNDEX',
        'SOUNDS LIKE',
        'SPACE',
        'SQRT',
        'ST_Area',
        'ST_AsBinary',
        'ST_AsWKB',
        'ST_AsGeoJSON',
        'ST_AsText',
        'ST_AsWKT',
        'ST_Buffer',
        'ST_Buffer_Strategy',
        'ST_Centroid',
        'ST_Contains',
        'ST_ConvexHull',
        'ST_Crosses',
        'ST_Difference',
        'ST_Dimension',
        'ST_Disjoint',
        'ST_Distance',
        'ST_Distance_Sphere',
        'ST_EndPoint',
        'ST_Envelope',
        'ST_Equals',
        'ST_ExteriorRing',
        'ST_GeoHash',
        'ST_GeomCollFromText',
        'ST_GeometryCollectionFromText',
        'ST_GeomCollFromTxt',
        'ST_GeomCollFromWKB',
        'ST_GeometryCollectionFromWKB',
        'ST_GeometryN',
        'ST_GeometryType',
        'ST_GeomFromGeoJSON',
        'ST_GeomFromText',
        'ST_GeometryFromText',
        'ST_GeomFromWKB',
        'ST_GeometryFromWKB',
        'ST_InteriorRingN',
        'ST_Intersection',
        'ST_Intersects',
        'ST_IsClosed',
        'ST_IsEmpty',
        'ST_IsSimple',
        'ST_IsValid',
        'ST_LatFromGeoHash',
        'ST_Latitude',
        'ST_Length',
        'ST_LineFromText',
        'ST_LineStringFromText',
        'ST_LineFromWKB',
        'ST_LineStringFromWKB',
        'ST_LongFromGeoHash',
        'ST_Longitude',
        'ST_MakeEnvelope',
        'ST_MLineFromText',
        'ST_MultiLineStringFromText',
        'ST_MLineFromWKB',
        'ST_MultiLineStringFromWKB',
        'ST_MPointFromText',
        'ST_MultiPointFromText',
        'ST_MPointFromWKB',
        'ST_MultiPointFromWKB',
        'ST_MPolyFromText',
        'ST_MultiPolygonFromText',
        'ST_MPolyFromWKB',
        'ST_MultiPolygonFromWKB',
        'ST_NumGeometries',
        'ST_NumInteriorRing',
        'ST_NumInteriorRings',
        'ST_NumPoints',
        'ST_Overlaps',
        'ST_PointFromGeoHash',
        'ST_PointFromText',
        'ST_PointFromWKB',
        'ST_PointN',
        'ST_PolyFromText',
        'ST_PolygonFromText',
        'ST_PolyFromWKB',
        'ST_PolygonFromWKB',
        'ST_Simplify',
        'ST_SRID',
        'ST_StartPoint',
        'ST_SwapXY',
        'ST_SymDifference',
        'ST_Touches',
        'ST_Transform',
        'ST_Union',
        'ST_Validate',
        'ST_Within',
        'ST_X',
        'ST_Y',
        'STATEMENT_DIGEST',
        'STATEMENT_DIGEST_TEXT',
        'STD',
        'STDDEV',
        'STDDEV_POP',
        'STDDEV_SAMP',
        'STR_TO_DATE',
        'STRCMP',
        'SUBDATE',
        'SUBSTR',
        'SUBSTRING',
        'SUBSTRING_INDEX',
        'SUBTIME',
        'SUM',
        'SYSDATE',
        'SYSTEM_USER',
        'TAN',
        'TIME',
        'TIME_FORMAT',
        'TIME_TO_SEC',
        'TIMEDIFF',
        'TIMESTAMP',
        'TIMESTAMPADD',
        'TIMESTAMPDIFF',
        'TO_BASE64',
        'TO_DAYS',
        'TO_SECONDS',
        'TRIM',
        'TRUNCATE',
        'UCASE',
        'UNCOMPRESS',
        'UNCOMPRESSED_LENGTH',
        'UNHEX',
        'UNIX_TIMESTAMP',
        'UpdateXML',
        'UPPER',
        'USER',
        'UTC_DATE',
        'UTC_TIME',
        'UTC_TIMESTAMP',
        'UUID',
        'UUID_SHORT',
        'UUID_TO_BIN',
        'VALIDATE_PASSWORD_STRENGTH',
        'VALUES',
        'VAR_POP',
        'VAR_SAMP',
        'VARIANCE',
        'VERSION',
        'WAIT_FOR_EXECUTED_GTID_SET',
        'WAIT_UNTIL_SQL_THREAD_AFTER_GTIDS',
        'WEEK',
        'WEEKDAY',
        'WEEKOFYEAR',
        'WEIGHT_STRING',
        'XOR',
        'YEAR',
        'YEARWEEK',
        '|',
        '~',
    ];


    /**
     * @param $sql
     * @param array $bind
     * @throws PDOException
     */
    public function __construct($sql = null, $bind = array())
    {
        parent::__construct();

        if (!empty($sql)) {
            try {
                $this->sql          = $sql;
                $this->bind_array   = $bind;
            } catch (PDOException $p) {
                self::handlePdoException($p);
            } catch (Error $e) {
                self::handleErrorAsWarning($e);
            }
        }
    }


    /**
     * @param array $columns
     * @param null $from
     * @param null $partition
     * @return $this
     */
    public function select(array $columns, $from = null, $partition = null)
    {
        $i              = (int)0;
        $select_clause  = [];

        foreach ($columns as $alias => $col) {
            $col = self::buildExpression($col);

            if ($i == $alias) {
                $select_clause[] = $col;
            } else {
                $select_clause[] = $col . ' AS ' . $alias;
            }

            if (is_int($alias)) {
                $i = (int)($alias+1);
            }
        }

        $this->select   = 'SELECT ' . implode(",\n", $select_clause);
        $this->from     = !empty($from) ? (' FROM ' . self::buildExpression($from)) : null;

        if (!empty($this->from) && !empty($partition)) {
            $this->from .= $partition;
        }

        return $this;
    }


    /**
     * @param $reference
     * @param null $condition
     * @param string $join_type
     * @return $this
     */
    public function join($reference, $condition = null, $join_type = 'INNER')
    {
        if (
            !in_array(strtoupper($join_type), ['NATURAL', 'NATURAL INNER', 'NATURAL LEFT', 'NATURAL RIGHT', 'NATURAL OUTER'])
            && substr(strtoupper($condition), 0, 3) != 'ON '
        ) {
            $condition = 'ON ' . $condition;
        }

        $this->join .= ' ' . $join_type . ' JOIN ' . self::buildExpression($reference) . ' ' . $condition;

        return $this;
    }


    /**
     * @param $reference
     * @param $condition
     * @return $this
     */
    public function innerJoin($reference, $condition)
    {
        return $this->join($reference, $condition);
    }


    /**
     * @param $reference
     * @param $condition
     * @return $this
     */
    public function crossJoin($reference, $condition)
    {
        return $this->join($reference, $condition, 'CROSS');
    }


    /**
     * @param $reference
     * @param $condition
     * @return $this
     */
    public function leftJoin($reference, $condition)
    {
        return $this->join($reference, $condition, 'LEFT');
    }


    /**
     * @param $reference
     * @param $condition
     * @return $this
     */
    public function rightJoin($reference, $condition)
    {
        return $this->join($reference, $condition, 'RIGHT');
    }


    /**
     * @param $reference
     * @param $condition
     * @return $this
     */
    public function naturalJoin($reference, $condition)
    {
        return $this->join($reference, $condition, 'NATURAL');
    }


    /**
     * @param $reference
     * @param $condition
     * @return $this
     */
    public function naturalLeftJoin($reference, $condition)
    {
        return $this->join($reference, $condition, 'NATURAL LEFT');
    }


    /**
     * @param $reference
     * @param $condition
     * @return $this
     */
    public function naturalRightJoin($reference, $condition)
    {
        return $this->join($reference, $condition, 'NATURAL RIGHT');
    }


    /**
     * @param array $clause
     * @return $this
     */
    public function where(array $clause)
    {
        $i              = (int)0;
        $where_clause   = [];

        foreach ($clause as $key => $val) {
            if (is_int($key)) {
                $where = $val;
            } else {
                $where              = $key;
                $bind_array         = $val;
                $this->bind_array   = array_merge($this->bind_array, $bind_array);
            }

            if ($i > (int)0) {
                $where_clause[] = 'AND ' . $where;
            } else {
                if (empty($this->where)) {
                    $where_clause[] = ' WHERE (' . $where;
                } else {
                    $where_clause[] = ' AND (' . $where;
                }
            }

            $i++;
        }

        $this->where    .= "\n" . implode("\n", $where_clause) . ')';

        return $this;
    }


    /**
     * @param array $clause
     * @return $this
     */
    public function orWhere(array $clause)
    {
        $i              = (int)0;
        $where_clause   = [];

        foreach ($clause as $key => $val) {
            if (is_int($key)) {
                $where = $val;
            } else {
                $where              = $key;
                $bind_array         = $val;
                $this->bind_array   = array_merge($this->bind_array, $bind_array);
            }

            if ($i > (int)0) {
                $where_clause[] = 'AND ' . $where;
            } else {
                if (!empty($this->where)) {
                    $where_clause[] = ' OR (' . $where;
                }
            }

            $i++;
        }

        $this->where    .= implode(' ', $where_clause) . ')';

        return $this;
    }


    /**
     * @param array $group_by
     * @return $this
     */
    public function groupBy(array $group_by)
    {
        foreach ($group_by as $key => $g) {
            $group_by[$key] = self::buildExpression(trim($g));
        }

        $group_by_clause = ' GROUP BY ' .  implode(',', $group_by);

        $this->group_by = $group_by_clause;

        return $this;
    }


    /**
     * @param $window_clause
     */
    public function window($window_clause)
    {
        $this->window = $window_clause;
    }


    /**
     * @param array $order_by
     * @return $this
     */
    public function orderBy(array $order_by)
    {
        $order_by_clause = [];

        foreach ($order_by as $key => $val) {
            if (is_int($key)) {
                $order  = self::buildExpression($val);
                $sort   = self::DEFAULT_ORDER_SORT;
            } else {
                $order  = self::buildExpression($key);
                $sort   = in_array(strtoupper($val), ['ASC', 'DESC']) ? $val : self::DEFAULT_ORDER_SORT;
            }

            $order_by_clause[]  = $order . ' ' . $sort;
        }

        $order_by_clause = ' ORDER BY ' . implode(', ', $order_by_clause);

        $this->order_by = $order_by_clause;

        return $this;
    }


    /**
     * @param int $row_count
     * @param int $offset
     * @return $this
     */
    public function limit($row_count = self::MAX_ROWS, $offset = 0)
    {
        $row_count      = number_format($row_count, 0, '', '');
        $this->limit    = ' LIMIT ' . $offset . ', ' . $row_count;

        return $this;
    }


    /**
     * @param string $for
     * @param $options
     * @return $this
     */
    public function for($for = self::FOR_DEFAULT_LOCK_TYPE, $options = self::FOR_DEFAULT_LOCK_OPTIONS)
    {
        $for        = in_array(strtoupper($for), ['UPDATE', 'SHARE']) ? $for : self::FOR_DEFAULT_LOCK_TYPE;
        $this->for  = ' FOR ' . $for . ' ' . $options;

        return $this;
    }


    /**
     * @param $var_name
     * @param null $into_option
     * @param null $charset_options
     * @return $this
     */
    public function into($var_name, $into_option = null, $charset_options = null)
    {
        $this->into     = ' INTO ' . $into_option . ' ' . $charset_options . ' ' . $var_name;

        return $this;
    }


    /**
     * @param $table_reference
     * @param array $assignment_list
     * @param null $update_options
     * @return $this
     */
    public function update($table_reference, array $assignment_list, $update_options = null)
    {
        $i              = (int)0;
        $set_clause     = [];

        foreach ($assignment_list as $key => $val) {
            if (is_int($key)) {
                $set = $val;
            } else {
                $set                = $key;
                $bind_array         = $val;
                $this->bind_array   = array_merge($this->bind_array, $bind_array);
            }

            if ($i > (int)0) {
                $set_clause[] = $set;
            } else {
                $set_clause[] = 'SET ' . $set;
            }

            $i++;
        }

        $this->update .= 'UPDATE ' . $update_options . ' ' . self::buildExpression($table_reference) . ' ' . implode(', ', $set_clause);

        return $this;
    }


    /**
     * @param $table_reference
     * @param array $assignment_list
     * @param null $insert_options
     * @param null $partition_reference
     * @return $this
     */
    public function insert($table_reference, array $assignment_list, $insert_options = null, $partition_reference = null)
    {
        $columns = $values = [];

        foreach ($assignment_list as $column => $value) {
            $columns[]          = self::buildExpression($column);
            $values[]           = '?';
            $this->bind_array[] = $value;
        }

        $insert_clause = 'INSERT ' . $insert_options . ' INTO ' . self::buildExpression($table_reference);

        if (!empty($partition_reference)) {
            $insert_clause .= ' PARTITION ' . $partition_reference;
        }

        $insert_clause .= ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $values) . ')';

        $this->insert = $insert_clause;

        return $this;
    }


    /**
     * @param array $assignment_list
     * @return $this
     */
    public function onDuplicateKeyUpdate(array $assignment_list)
    {
        if (!empty($this->insert)) {
            $set_clause     = [];

            foreach ($assignment_list as $column => $value) {
                $set_clause[]       = self::buildExpression($column) . ' = ?';
                $this->bind_array[] = $value;
            }

            $this->insert .= ' ON DUPLICATE KEY UPDATE ' . implode(', ', $set_clause);
        }

        return $this;
    }


    /**
     * @param $table_reference
     * @param null $delete_options
     * @param null $partition_reference
     * @return $this
     */
    public function delete($table_reference, $delete_options = null, $partition_reference = null)
    {
        $delete_clause = 'DELETE ' . $delete_options . ' FROM ' . self::buildExpression($table_reference);

        if (!empty($partition_reference)) {
            $delete_clause .= ' PARTITION ' . $partition_reference;
        }

        $this->delete = $delete_clause;

        return $this;
    }


    /**
     * @param $expression
     * @return string
     */
    private static function buildExpression($expression)
    {
        if (!self::checkIfFunctionInString($expression)) {
            $expression_exploded    = explode('.', $expression);
            $e                      = [];
            $expressions            = array_merge(self::$select_expressions, self::$update_expressions, self::$insert_expressions, self::$delete_expressions);

            foreach ($expression_exploded as $ee) {
                if (in_array($ee, $expressions)) {
                    $e[] = $ee;
                } else {
                    $e[] = '`' . $ee . '`';
                }
            }

            $expression = implode('.', $e);
        }

        return $expression;
    }


    /**
     * @param $string
     * @return bool
     */
    private static function checkIfFunctionInString($string)
    {
        $contains_function = false;

        foreach (self::$functions as $f) {
            if (strstr($string, $f)) {
                $contains_function = true;
                break;
            }
        }

        return $contains_function;
    }


    /**
     * @return string
     */
    public function buildQuery($debug = false)
    {
        if (!empty($this->select)) {
            $this->sql = $this->select
                . $this->from
                . $this->join
                . $this->where
                . $this->group_by
                . $this->order_by
                . $this->limit
                . $this->for
                . $this->into
            ;
        } elseif (!empty($this->update)) {
            $this->sql = $this->update
                . $this->where
                . $this->order_by
                . $this->limit
            ;
        } elseif (!empty($this->insert)) {
            $this->sql = $this->insert;
        } elseif (!empty($this->delete)) {
            $this->sql = $this->delete
                . $this->where
                . $this->order_by
                . $this->limit
            ;
        }

        if ($debug === true) {
            $debug_info = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);

            var_dump($this->sql, $this->bind_array, $debug_info);
        }

        return $this->sql;
    }


    /**
     * @return bool PDOStatement
     */
    public function run()
    {
        $query = $this->status;

        if ($this->status === true) {
            $this->buildQuery();

            try {
                $this->query = $this->prepare($this->sql);
                $this->query->execute($this->bind_array);

                $this->traceExpansionQueries();
            } catch (PDOException $p) {
                self::handlePdoException($p);
            } catch (Error $e) {
                self::handleErrorAsWarning($e);
            }
        }

        return $query;
    }

    /**
     * @return array
     * @throws PDOException
     */
    public function fetchAssoc()
    {
        $this->run();

        $fetch_assoc = [];

        try {
            $fetch_assoc = $this->query->fetch(PDO::FETCH_ASSOC);
        } catch(Error $e) {
            self::handleErrorAsWarning($e);
        }

        return $fetch_assoc;
    }


    /**
     * @return array
     * @throws PDOException
     */
    public function fetchAllAssoc()
    {
        $this->run();

        $fetch_all_assoc = [];

        try {
            $fetch_all_assoc = $this->query->fetchAll(PDO::FETCH_ASSOC);
        } catch(Error $e) {
            self::handleErrorAsWarning($e);
        }

        return $fetch_all_assoc;
    }


    /**
     * @return string
     * @throws PDOException
     */
    public function fetch()
    {
        $this->run();

        $fetch = null;

        try {
            $fetch = $this->query->fetch(PDO::FETCH_COLUMN);
        } catch(Error $e) {
            self::handleErrorAsWarning($e);
        }

        return $fetch;
    }


    /**
     * @return array
     * @throws PDOException
     */
    public function fetchAll()
    {
        $this->run();

        $fetch_all = [];

        try {
            $fetch_all = $this->query->fetchAll(PDO::FETCH_COLUMN);
        } catch(Error $e) {
            self::handleErrorAsWarning($e);
        }

        return $fetch_all;
    }


    /**
     * @return bool
     */
    public function runAndReturnInsertId()
    {
        $this->run();

        return $this->lastInsertId();
    }


    /**
     * @param PDOException $e
     * @return bool
     */
    private static function handlePdoException(PDOException $e)
    {
        if (empty($_SESSION['setup_mode'])) {
            Log::app($e->getMessage(), $e->getTraceAsString());

            throw new PDOException($e->getMessage() . ' ' . $e->getTraceAsString());
        } else {
            trigger_error($e->getMessage() . ' ' . $e->getTraceAsString(), E_USER_WARNING);
        }

        return false;
    }


    /**
     * @param Error $e
     * @return bool
     */
    private static function handleErrorAsWarning(Error $e)
    {
        if (empty($_SESSION['setup_mode'])) {
            Log::app($e->getMessage(), $e->getTraceAsString());

            trigger_error($e->getMessage() . ' ' . $e->getTraceAsString(), E_USER_WARNING);
        }

        return false;
    }


    /**
     * @return string
     */
    public static function getUuid()
    {
        $sql    = "SELECT UUID();";
        $db     = new self($sql);

        $db->run();

        return $db->fetch();
    }
}