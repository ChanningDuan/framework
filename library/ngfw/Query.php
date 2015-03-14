<?php

/**
 * ngfw
 * ---
 * copyright (c) 2015, Nick Gejadze
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace ngfw;

/**
 * Query
 *
 * @package       ngfw
 * @subpackage    library
 * @version       1.2.3
 * @copyright (c) 2015, Nick Gejadze
 */
class Query {

    /**
     * $query
     * Holds full query string
     *
     * @var string
     */
    public $query = '';

    /**
     * $bind
     * Holds bind array
     *
     * @var array
     */
    public $bind;

    /**
     * $fields
     * Holds field names
     *
     * @var array
     */
    public $fields;

    /**
     * $orderBy
     * Holds order by value
     *
     * @var string
     */
    protected $orderBy;

    /**
     * $limit
     * Holds limit value
     *
     * @var mixed
     */
    protected $limit;

    /**
     * $select
     * Holds select variables, can be string or array
     *
     * @var mixed
     */
    private $select;

    /**
     * $table
     * Holds table name
     *
     * @var string
     */
    private $table;

    /**
     * $deleteTable
     * Holds boolean value if delete query is requested or not
     *
     * @var boolean
     */
    private $deleteTable;

    /**
     * $from
     * Holds from string or array
     *
     * @var mixed
     */
    private $from;

    /**
     * $joinCondition
     * @var string
     */
    private $joinCondition = "ON";

    /**
     * $availableJoinConditions
     * @var array
     */
    private $availableJoinConditions = array("ON", "USING");

    /**
     * $join
     * Holds join data as an array
     *
     * @var array
     */
    private $join;

    /**
     * $innerJoin
     * Holds innerJoin data as an array
     *
     * @var array
     */
    private $innerJoin;

    /**
     * $leftJoin
     * Holds leftJoin data as an array
     *
     * @var array
     */
    private $leftJoin;

    /**
     * $rightJoin
     * Holds rightJoin data as an array
     *
     * @var array
     */
    private $rightJoin;

    /**
     * $where
     * Holds where clause
     *
     * @var string
     */
    private $where;

    /**
     * $andWhere
     * Holds where clause
     *
     * @var array
     */
    private $andWhere;

    /**
     * $orWhere
     * Holds where clause
     *
     * @var array
     */
    private $orWhere;

    /**
     * $groupBy
     * Holds groupdBy clause
     *
     * @var string
     */
    private $groupBy;

    /**
     * $having
     * Holds having clause
     *
     * @var string
     */
    private $having;

    /**
     * MySQL Available functions
     *
     * @var array
     */
    private $mysqlFunctions = array("ABS", "ACOS", "ADDDATE", "ADDTIME", "AES_DECRYPT", "AES_ENCRYPT", "Area", "AsBinary", "AsWKB", "ASCII", "ASIN", "AsText", "AsWKT", "ATAN2", "ATAN", "ATAN", "AVG", "BENCHMARK", "BIN", "BINARY", "BIT_AND", "BIT_COUNT", "BIT_LENGTH", "BIT_OR", "BIT_XOR", "CASE", "CAST", "CEIL", "CEILING", "Centroid", "CHAR_LENGTH", "CHAR", "CHARACTER_LENGTH", "CHARSET", "COALESCE", "COERCIBILITY", "COLLATION", "COMPRESS", "CONCAT_WS", "CONCAT", "CONNECTION_ID", "Contains", "CONV", "CONVERT_TZ", "CONVERT", "COS", "COT", "COUNT", "CRC32", "Crosses", "CURDATE", "CURRENT_DATE", "CURRENT_DATE", "CURRENT_TIME", "CURRENT_TIME", "CURRENT_TIMESTAMP", "CURRENT_TIMESTAMP", "CURRENT_USER", "CURRENT_USER", "CURTIME", "DATABASE", "DATE_ADD", "DATE_FORMAT", "DATE_SUB", "DATE", "DATEDIFF", "DAY", "DAYNAME", "DAYOFMONTH", "DAYOFWEEK", "DAYOFYEAR", "DECODE", "DEFAULT", "DEGREES", "DES_DECRYPT", "DES_ENCRYPT", "Dimension", "Disjoint", "DIV", "ELT", "ENCODE", "ENCRYPT", "EndPoint", "Envelope", "Equals", "EXP", "EXPORT_SET", "ExteriorRing", "EXTRACT", "FIELD", "FIND_IN_SET", "FLOOR", "FORMAT", "FOUND_ROWS", "FROM_DAYS", "FROM_UNIXTIME", "GeomCollFromText", "GeometryCollectionFromText", "GeomCollFromWKB", "GeometryCollectionFromWKB", "GeometryCollection", "GeometryN", "GeometryType", "GeomFromText", "GeometryFromText", "GeomFromWKB", "GET_FORMAT", "GET_LOCK", "GLength", "GREATEST", "GROUP_CONCAT", "HEX", "HOUR", "IF", "IFNULL", "IN", "INET_ATON", "INET_NTOA", "INSERT", "INSTR", "InteriorRingN", "Intersects", "INTERVAL", "IS_FREE_LOCK", "IS", "IsClosed", "IsEmpty", "ISNULL", "IsSimple", "LAST_DAY", "LAST_INSERT_ID", "LCASE", "LEAST", "LEFT", "LENGTH", "LIKE", "LineFromText", "LineFromWKB", "LineStringFromWKB", "LineString", "LN", "LOAD_FILE", "LOCALTIME", "LOCALTIME", "LOCALTIMESTAMP", "LOCALTIMESTAMP", "LOCATE", "LOG10", "LOG2", "LOG", "LOWER", "LPAD", "LTRIM", "MAKE_SET", "MAKEDATE", "MAKETIME", "MASTER_POS_WAIT", "MATCH", "MAX", "MBRContains", "MBRDisjoint", "MBREqual", "MBRIntersects", "MBROverlaps", "MBRTouches", "MBRWithin", "MD5", "MICROSECOND", "MID", "MIN", "MINUTE", "MLineFromText", "MultiLineStringFromText", "MLineFromWKB", "MultiLineStringFromWKB", "MOD", "%", "MOD", "MONTH", "MONTHNAME", "MPointFromText", "MultiPointFromText", "MPointFromWKB", "MultiPointFromWKB", "MPolyFromText", "MultiPolygonFromText", "MPolyFromWKB", "MultiPolygonFromWKB", "MultiLineString", "MultiPoint", "MultiPolygon", "NAME_CONST", "NOT", "NOW", "NULLIF", "NumGeometries", "NumInteriorRings", "NumPoints", "OCT", "OCTET_LENGTH", "OLD_PASSWORD", "ORD", "Overlaps", "PASSWORD", "PERIOD_ADD", "PERIOD_DIFF", "PI", "Point", "PointFromText", "PointFromWKB", "PointN", "PolyFromText", "PolygonFromText", "PolyFromWKB", "PolygonFromWKB", "Polygon", "POSITION", "POW", "POWER", "PROCEDURE", "QUARTER", "QUOTE", "RADIANS", "RAND", "REGEXP", "RELEASE_LOCK", "REPEAT", "REPLACE", "REVERSE", "RIGHT", "RLIKE", "ROUND", "ROW_COUNT", "RPAD", "RTRIM", "SCHEMA", "SEC_TO_TIME", "SECOND", "SESSION_USER", "SHA1", "SHA", "SIGN", "SIN", "SLEEP", "SOUNDEX", "SOUNDS", "SPACE", "SQRT", "SRID", "StartPoint", "STD", "STDDEV_POP", "STDDEV_SAMP", "STDDEV", "STR_TO_DATE", "STRCMP", "SUBDATE", "SUBSTR", "SUBSTRING_INDEX", "SUBSTRING", "SUBTIME", "SUM", "SYSDATE", "SYSTEM_USER", "TAN", "TIME_FORMAT", "TIME_TO_SEC", "TIME", "TIMEDIFF", "TIMESTAMP", "TIMESTAMPADD", "TIMESTAMPDIFF", "TO_DAYS", "Touches", "TRIM", "TRUNCATE", "UCASE", "UNCOMPRESS", "UNCOMPRESSED_LENGTH", "UNHEX", "UNIX_TIMESTAMP", "UPPER", "USER", "UTC_DATE", "UTC_TIME", "UTC_TIMESTAMP", "UUID", "VALUES", "VAR_POP", "VAR_SAMP", "VARIANCE", "VERSION", "WEEK", "WEEKDAY", "WEEKOFYEAR", "Within", "X", "XOR", "Y", "YEAR", "YEARWEEK");

    /**
     * Glue for functions in bind
     *
     * @var string
     */
    private $glueForFunctionsSuffix = "--";

    /**
     * Generate uniq id
     *
     * @return string
     */
    private function generateKey()
    {
        return uniqid();
    }

    /**
     * select()
     * Starts select statement
     *
     * @param string $select Default '*' , The array of strings to select from database
     * @return object Query()
     */
    public function select($select = "*")
    {
        $this->select = $select;
        if (isset($this->select)):
            if (is_array($this->select)):
                foreach ($this->select as $key => $value):
                    $this->select[$key] = $this->escapeField($value);
                endforeach;
                $this->select = implode(", ", $this->select);
            else:
                $this->select = ($this->select !== "*") ? $this->escapeField($this->select) : $this->select;
            endif;
            $this->query = "SELECT " . $this->select . " ";
        endif;

        return $this;
    }

    /**
     * insert()
     * Builds insert statement
     *
     * @param string $table Table name you want to insert into
     * @param array  $data  Array of strings, example array("fieldname" => "value")
     * @return object Query()
     * @throws Exception
     */
    public function insert($table, $data)
    {
        $this->table = $this->escapeField($table);
        if ( ! isset($this->table)):
            throw new Exception("Table name is required for insert method to build query");
        endif;
        if ( ! isset($data) || ! is_array($data)):
            throw new Exception("Insert Values are required to build query");
        endif;
        $this->query = "INSERT INTO " . $this->escapeField($this->table) . " ";
        $this->buildBindAndFieldObjectsFromArray($data);
        $this->query .= "(" . implode(", ", $this->fields) . ") VALUES (" . $this->implodeBindValues($this->bind) . ") ";
        $this->cleanFunctionsFromBind();

        return $this;
    }

    /**
     * update()
     * Builds update statement
     *
     * @param string $table Table name you want to update
     * @param array  $data  Array of strings that needs to be updated, example array("fieldname" => "value");
     * @throws Exception
     * @return object Query()
     */
    public function update($table, $data)
    {
        $this->table = $table;
        if ( ! isset($this->table)):
            throw new Exception("Table name is required for update method to build query");
        endif;
        if ( ! isset($data) || ! is_array($data)):
            throw new Exception("Update Values are required to build query");
        endif;
        $this->query = "UPDATE " . $this->escapeField($this->table) . " SET ";
        $this->buildBindAndFieldObjectsFromArray($data);
        $multipleIterator = new \MultipleIterator();
        $multipleIterator->attachIterator(new \ArrayIterator($this->fields));
        $multipleIterator->attachIterator(new \ArrayIterator($this->bind));
        $multipleIterator->attachIterator(new \ArrayIterator(array_keys($this->bind)));
        foreach ($multipleIterator as $data):
            list($field, $bindValues, $bindKeys) = $data;
            $this->query .= $this->escapeField($field) . " = " . $this->implodeBindValues(array($bindKeys => $bindValues)) . ", ";
        endforeach;
        $this->query = substr($this->query, 0, - 2) . " ";
        $this->cleanFunctionsFromBind();

        return $this;
    }

    /**
     * delete()
     * Starts delete statement
     *
     * @return object Query()
     */
    public function delete()
    {
        $this->deleteTable = true;
        if (isset($this->deleteTable)):
            $this->query = "DELETE ";
        endif;

        return $this;
    }

    /**
     * from()
     * Sets from object
     *
     * @param mixed $from Table name as a string or Array of strings, example: array("table1 a", "table2 b", "table3 c")
     * @throws Exception
     * @return object Query()
     */
    public function from($from)
    {
        if ( ! isset($from)):
            throw new Exception("FROM is Required to build query");
        endif;
        $this->from = $from;
        if (isset($this->from)):
            if (is_array($this->from)):
                foreach ($this->from as $key => $value):
                    $this->from[$key] = $this->escapeField($value);
                endforeach;
                $this->from = implode(", ", $this->from);
            else:
                $this->from = $this->escapeField($this->from);
            endif;
            $this->query .= "FROM " . $this->from . " ";
        endif;

        return $this;
    }

    /**
     * join()
     * Sets join object
     *
     * @param string $table         Table name as a string
     * @param string $clause        Clause as a string, example "a.fieldname = b.fieldname"
     * @param string $joinCondition ON or USING
     * @return object Query()
     */
    public function join($table, $clause, $joinCondition = null)
    {
        if ( ! empty($joinCondition) && in_array(strtoupper($joinCondition), $this->availableJoinConditions)):
            $this->joinCondition = strtoupper($joinCondition);
        endif;
        $k = (count($this->join) > 0) ? count($this->join) + 1 : 0;
        $this->join[$k]['table'] = $this->escapeField($table);
        if ($this->joinCondition == "USING"):
            if (is_array($clause)):
                foreach ($clause as $i => $v):
                    $clause[$i] = $this->escapeField($v);
                endforeach;
                $this->join[$k]['clause'] = "(" . implode(", ", $clause) . ")";
            else:
                $this->join[$k]['clause'] = "(" . $this->escapeField($clause) . ")";
            endif;
        else:
            $this->join[$k]['clause'] = $this->escapeField($clause);
        endif;
        $this->query .= "JOIN " . $this->join[$k]['table'] . " " . $this->joinCondition . " " . $this->join[$k]['clause'] . " ";

        return $this;
    }

    /**
     * innerJoin()
     * Sets inner join object
     *
     * @param string $table         Table name as a string
     * @param string $clause        Clause as a string, example "a.fieldname = b.fieldname"
     * @param string $joinCondition ON or USING
     * @return object Query()
     */
    public function innerJoin($table, $clause, $joinCondition = null)
    {
        if ( ! empty($joinCondition) && in_array(strtoupper($joinCondition), $this->availableJoinConditions)):
            $this->joinCondition = strtoupper($joinCondition);
        endif;
        $k = (count($this->innerJoin) > 0) ? count($this->innerJoin) + 1 : 0;
        $this->innerJoin[$k]['table'] = $this->escapeField($table);
        if ($this->joinCondition == "USING"):
            if (is_array($clause)):
                foreach ($clause as $i => $v):
                    $clause[$i] = $this->escapeField($v);
                endforeach;
                $this->innerJoin[$k]['clause'] = "(" . implode(", ", $clause) . ")";
            else:
                $this->innerJoin[$k]['clause'] = "(" . $this->escapeField($clause) . ")";
            endif;
        else:
            $this->innerJoin[$k]['clause'] = $this->escapeField($clause);
        endif;
        $this->query .= "INNER JOIN " . $this->innerJoin[$k]['table'] . " " . $this->joinCondition . " " . $this->innerJoin[$k]['clause'] . " ";

        return $this;
    }

    /**
     * leftJoin()
     * Sets left join object
     *
     * @param string $table         Table name as a string
     * @param string $clause        Clause as a string, example "a.fieldname = b.fieldname"
     * @param string $joinCondition ON or USING, default null
     * @return object Query()
     */
    public function leftJoin($table, $clause, $joinCondition = null)
    {
        if ( ! empty($joinCondition) && in_array(strtoupper($joinCondition), $this->availableJoinConditions)):
            $this->joinCondition = strtoupper($joinCondition);
        endif;
        $k = (count($this->leftJoin) > 0) ? count($this->leftJoin) + 1 : 0;
        $this->leftJoin[$k]['table'] = $this->escapeField($table);
        if ($this->joinCondition == "USING"):
            if (is_array($clause)):
                foreach ($clause as $i => $v):
                    $clause[$i] = $this->escapeField($v);
                endforeach;
                $this->leftJoin[$k]['clause'] = "(" . implode(", ", $clause) . ")";
            else:
                $this->leftJoin[$k]['clause'] = "(" . $this->escapeField($clause) . ")";
            endif;
        else:
            $this->leftJoin[$k]['clause'] = $this->escapeField($clause);
        endif;
        $this->query .= "LEFT JOIN " . $this->leftJoin[$k]['table'] . " " . $this->joinCondition . " " . $this->leftJoin[$k]['clause'] . " ";

        return $this;
    }

    /**
     * rightJoin()
     * Sets right join object
     *
     * @param string $table         Table name as a string
     * @param string $clause        Clause as a string, example "a.fieldname = b.fieldname"
     * @param string $joinCondition ON or USING, default null
     * @return object Query()
     */
    public function rightJoin($table, $clause, $joinCondition = null)
    {
        if ( ! empty($joinCondition) && in_array(strtoupper($joinCondition), $this->availableJoinConditions)):
            $this->joinCondition = strtoupper($joinCondition);
        endif;
        $k = (count($this->leftJoin) > 0) ? count($this->leftJoin) + 1 : 0;
        $this->rightJoin[$k]['table'] = $this->escapeField($table);
        if ($this->joinCondition == "USING"):
            if (is_array($clause)):
                foreach ($clause as $i => $v):
                    $clause[$i] = $this->escapeField($v);
                endforeach;
                $this->rightJoin[$k]['clause'] = "(" . implode(", ", $clause) . ")";
            else:
                $this->rightJoin[$k]['clause'] = "(" . $this->escapeField($clause) . ")";
            endif;
        else:
            $this->rightJoin[$k]['clause'] = $this->escapeField($clause);
        endif;
        $this->query .= "RIGHT JOIN " . $this->rightJoin[$k]['table'] . " " . $this->joinCondition . " " . $this->rightJoin[$k]['clause'] . " ";

        return $this;
    }

    /**
     * where()
     * Sets where object
     *
     * @param string $where where statement, example: ("fieldname = ?")
     * @param mixed  $value string value to be replaced in where statement
     * @return object Query()
     */
    public function where($where, $value = false)
    {
        $where = $this->escapeField($where);
        if ($value):
            $key = $this->buildBindAndFieldObjects($value);
        endif;
        if (isset($key) && ! empty($key)):
            $this->where = str_replace("?", ":" . $key, $where);
        else:
            $this->where = $this->escapeValue($where);
        endif;
        $this->query .= "WHERE " . $this->where . " ";

        return $this;
    }

    /**
     * andWhere()
     * Sets and where object
     *
     * @param string $where where statement, example: ("fieldname = ?")
     * @param mixed  $value string value to be replaced in where statement
     * @return object Query()
     */
    public function andWhere($where, $value = false)
    {
        $where = $this->escapeField($where);
        if ($value):
            $key = $this->buildBindAndFieldObjects($value);
        endif;
        if (isset($key) && ! empty($key)):
            $this->andWhere[] = str_replace("?", ":" . $key, $where);
        else:
            $this->andWhere[] = $this->escapeValue($where);
        endif;
        $this->query .= "AND " . end($this->andWhere) . " ";

        return $this;
    }

    /**
     * orWhere()
     * Sets or where object
     *
     * @param string $where where statement, example: ("fieldname = ?")
     * @param string $value string value to be replaced in where statement
     * @return object Query()
     */
    public function orWhere($where, $value = null)
    {
        $where = $this->escapeField($where);
        if ($value):
            $key = $this->buildBindAndFieldObjects($value);
        endif;
        if (isset($key) && ! empty($key)):
            $this->orWhere[] = str_replace("?", ":" . $key, $where);
        else:
            $this->orWhere[] = $this->escapeValue($where);
        endif;
        $this->query .= "OR " . end($this->orWhere) . " ";

        return $this;
    }

    /**
     * having()
     * Set having object
     *
     * @param string $condition having statement, example: ("fieldname = ?")
     * @param string $value     string value to be replaced in having statement
     * @return object Query()
     */
    public function having($condition, $value = null)
    {
        $condition = $this->escapeField($condition);
        if ($value):
            $key = $this->buildBindAndFieldObjects($value);
        endif;
        if (isset($key) && ! empty($key)):
            $this->having = str_replace("?", ":" . $key, $condition);
        else:
            $this->having = $this->escapeValue($condition);
        endif;
        if (count($this->having) > 1):
            $this->query .= "AND " . $this->having . " ";
        else:
            $this->query .= "HAVING " . $this->having . " ";
        endif;

        return $this;
    }

    /**
     * group()
     * Sets groupBy object
     *
     * @param string $field Name of field to group by
     * @return object Query()
     */
    public function group($field)
    {
        $this->groupBy = $field;
        if (isset($this->groupBy)):
            if (is_array($this->groupBy)):
                $this->groupBy = implode("`, `", $this->groupBy);
            endif;
            $this->query .= "GROUP BY `" . $this->groupBy . "` ";
        endif;

        return $this;
    }

    /**
     * order()
     * Sets orderBy Object
     *
     * @param string $field  field name to order by, example "Fieldname" or "RAND(" . date("Ymd") . ")"
     * @param string $clause order clause, example: "DESC" or "ASC"
     * @return object Query()
     */
    public function order($field, $clause = null)
    {
        if (strpos($field, "(") === false):
            $field = $this->escapeField($field);
        endif;
        $this->orderBy[] = $field . " " . $clause;
        if (count($this->orderBy) > 1):
            $this->query = trim($this->query) . ", " . end($this->orderBy) . " ";
        else:
            $this->query .= "ORDER BY " . end($this->orderBy) . " ";
        endif;

        return $this;
    }

    /**
     * limit()
     * Sets limit object
     *
     * @param int $int Must be numeric
     * @return object Query()
     */
    public function limit($int)
    {
        $this->limit = $int;
        if (isset($this->limit)):
            $this->query .= "LIMIT " . $this->escapeValue($this->limit);
        endif;

        return $this;
    }

    /**
     * Build bind and fields object from array
     *
     * @param  array $data data to bind
     * @return array generated keys
     */
    private function buildBindAndFieldObjectsFromArray(array $data)
    {
        $keys = array();
        foreach ($data as $k => $v):
            $keys[] = $this->buildBindAndFieldObjects($v, $k);
        endforeach;

        return $keys;
    }

    /**
     * Build bind and fields object
     *
     * @param  string $value
     * @param  string $key
     * @return string generated Key
     */
    private function buildBindAndFieldObjects($value, $key = null)
    {
        $generatedKey = $this->generateKey();
        if (isset($key) && ! empty($key)):
            $this->fields[$generatedKey] = $this->escapeField($key);
        endif;
        $isFunction = strpos($value, "(");
        if ($isFunction !== false && $isFunction > 0):
            $functionNameArray = explode("(", $value);
            $functionName = $functionNameArray[0];
            if (in_array($functionName, $this->mysqlFunctions)):
                $this->bind[$generatedKey . $this->glueForFunctionsSuffix . $value] = false;
            else:
                $this->bind[$generatedKey] = $this->escapeValue($value);
            endif;
        elseif ($value == null):
            $this->bind[$generatedKey . $this->glueForFunctionsSuffix . "NULL"] = null;
        else:
            $this->bind[$generatedKey] = $this->escapeValue($value);
        endif;

        return $generatedKey;
    }

    /**
     * Implode Bind values
     *
     * @param  array $array
     * @return string
     */
    private function implodeBindValues(array $array)
    {
        $string = "";
        foreach ($array as $key => $value):
            if ($value === false):
                if (strpos($key, $this->glueForFunctionsSuffix) !== false):
                    $function = end(explode($this->glueForFunctionsSuffix, $key));
                    $string .= $function . ", ";
                endif;
            elseif ($value == null):
                $string .= "NULL, ";
            else:
                $string .= ":" . $key . ", ";
            endif;
        endforeach;

        return substr($string, 0, - 2);
    }

    /**
     * Remove all keys from $this->bind where value is false
     *
     * @return void
     */
    private function cleanFunctionsFromBind()
    {
        array_map(function ($v, $k) {

            // unset functions from $this->bind
            if ($v === false):
                if (strpos($k, $this->glueForFunctionsSuffix) !== false):
                    unset($this->bind[$k]);
                endif;
            endif;
        }, $this->bind, array_keys($this->bind));
    }

    /**
     * escapeField
     * will identify and escape first field
     *
     * @param string $str example a.fieldname
     * @return string
     */
    private function escapeField($str)
    {
        if (strpos($str, '`') === false):
            if (strpos($str, ".") === false):
                if (strpos($str, " ") === false):
                    $str = "`" . $str . "`";
                else:
                    $strD = explode(" ", $str, 2);
                    $str = "`" . $strD[0] . "` " . $strD[1];
                endif;
            else:
                $str = preg_replace_callback('/[a-zA-Z0-9]+[.][a-zA-Z0-9]+/', function ($matches) {
                    $strD = explode(".", $matches[0]);

                    return "`" . $strD[0] . "`.`" . $strD[1] . "`";
                }, $str);
            endif;
        endif;

        return $str;
    }

    /**
     * escapeValue
     * will escape string or array
     *
     * @param mixed $value
     * @return mixed
     */
    private function escapeValue($value)
    {
        if (is_array($value)):
            foreach ($value as $key => $val):
                $value[$key] = $this->escapeValue($val);
            endforeach;

            return $value;
        else:
            if ( ! is_numeric($value)):
                $search = array("\\", "\0", "\n", "\r", "\x1a", "'", '"', ";");
                $replace = array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"', "");

                return str_replace($search, $replace, $value);
            else:
                return $value;
            endif;
        endif;
    }

    /**
     * getQuery()
     * Aliases __toString() Function if $compileQuery param is set to true
     * Returns query as a string
     *
     * @param $compileQuery boolean Default false
     * @see __toString()
     * @return mixed
     */
    public function getQuery($compileQuery = false)
    {
        if ($compileQuery):
            return $this->__toString();
        else:
            return $this;
        endif;
    }

    /**
     * __toString()
     * Returns query as a string
     *
     * @return string
     */
    public function __toString()
    {
        $keys = array();
        $values = array();
        if (isset($this->bind) && is_array($this->bind)):
            foreach ($this->bind as $k => $v):
                $values[] = "'" . $this->escapeValue($v) . "'";
                $keys[] = '/:' . $k . '/';
            endforeach;
        endif;
        if (isset($keys) && ! empty($keys) && isset($values) && ! empty($values)):
            $query = preg_replace($keys, $values, $this->query, 1);
        endif;

        return trim(isset($query) ? $query : $this->query);
    }
}
