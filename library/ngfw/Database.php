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
 * Database
 *
 * @package       ngfw
 * @subpackage    library
 * @version       1.3.1
 * @copyright (c) 2015, Nick Gejadze
 */
class Database extends \PDO {

    /**
     * Default CHARSET
     */
    const CHARSET = 'UTF8';

    /**
     * Default Fetch Mode
     * available values : all | row
     *
     * @var string
     */
    private $fetchmode = "all";

    /**
     * $options
     * Database Parameters
     * Database Parameters
     *
     * @var array
     */
    private $options;

    /**
     * __construct
     * sets options and Connections to Database
     *
     * @param array $options
     * @param bool  $autoConnect
     */
    public function __construct($options = null, $autoConnect = true)
    {
        $this->setOptions($options);
        if ($autoConnect && isset($this->options) && ! empty($this->options)){
            $this->connect($this->options);
        }
    }

    /**
     * Set options object
     *
     * @param array $options database connection settings
     */
    private function setOptions($options)
    {
        if (isset($options) || ! empty($options)){
            $this->options = $options;
        }
    }

    /**
     * connect
     * Connects to database
     * Connects to database
     *
     * @access private
     * @param array $options
     */
    private function connect($options)
    {
        $this->setOptions($options);
        $dsn = $this->createdsn();
        $attrs = ! isset($this->options['charset']) ? array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . self::CHARSET) : array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . $this->options['charset']);
        try {
            parent::__construct($dsn, $this->options['username'], $this->options['password'], $attrs);
        } catch (\PDOException $e) {
            if (defined('DEVELOPMENT_ENVIRONMENT') && DEVELOPMENT_ENVIRONMENT){
                echo 'Connection failed: ' . $e->getMessage();
            }
        }
    }

    /**
     * createdsn
     * Creates Data Source Name
     *
     * @return string
     */
    private function createdsn()
    {
        if ( ! isset($this->options) || empty($this->options)){
            return false;
        }

        return $this->options['dbtype'] . ':host=' . $this->options['host'] . ';port=' . $this->options['port'] . ';dbname=' . $this->options['dbname'];
    }

    /**
     * fetchAll
     * Fetches database and returns result as array
     *
     * @param string $query
     * @param array  $data
     * @return mixed
     */
    public function fetchAll($query, $data = null)
    {
        return $this->query($query, $data);
    }

    /**
     * fetchRow
     * Returns single row from database and return result as array
     *
     * @param string $query
     * @param array  $data
     * @return mixed
     */
    public function fetchRow($query, $data = null)
    {
        $this->fetchmode = "row";
        $result = $this->query($query, $data);
        $this->fetchmode = "all";

        return $result;
    }

    /**
     * run()
     * Executes Query
     *
     * @param string $query
     * @param array  $data
     * @throws Exception
     * @return mixed
     */
    public function query($query, $data = null)
    {
        try {
            if ($query instanceof Query){
                $pdostmt = $this->prepare(trim($query->query));
                if (isset($query->bind) && is_array($query->bind)){
                    foreach ($query->bind as $k => $bind){
                        switch (gettype($bind)){
                            case "boolean":
                                $data_type = \PDO::PARAM_BOOL;
                                break;
                            case "integer":
                            case "double":
                                $data_type = \PDO::PARAM_INT;
                                break;
                            case null:
                                $data_type = \PDO::PARAM_NULL;
                                break;
                            case "string":
                            default:
                                $data_type = \PDO::PARAM_STR;
                                break;
                        }
                        $pdostmt->bindValue(':' . $k, $bind, $data_type);
                    }
                }
            }else{
                $pdostmt = $this->prepare($query);
            }
            if ($pdostmt->execute($data) !== false){
                if (preg_match("/^(" . implode("|", array("SELECT", "DESCRIBE", "PRAGMA", "SHOW", "DESCRIBE")) . ") /i", is_string($query) ? $query : $query->query)){
                    if ($this->fetchmode == "all"){
                        return $pdostmt->fetchAll(\PDO::FETCH_ASSOC);
                    }elseif ($this->fetchmode == "row"){
                        return $pdostmt->fetch(\PDO::FETCH_ASSOC);
                    }else{
                        throw new Exception("Fetch mode is unidentified");
                    }
                }elseif (preg_match("/^(" . implode("|", array("DELETE", "INSERT", "UPDATE")) . ") /i", is_string($query) ? $query : $query->query)){
                    return $pdostmt->rowCount();
                }
            }
        } catch (\PDOException $e) {
            if (defined('DEVELOPMENT_ENVIRONMENT') && DEVELOPMENT_ENVIRONMENT){
                echo $e->getMessage();
            }

            return false;
        }

        return false;
    }

    /**
     * escape, quote() method alias
     *
     * @param  string  $value
     * @param  integer $parameter_type
     * @return string
     */
    public function escape($value, $parameter_type = \PDO::PARAM_STR)
    {
        return $this->quote($value, $parameter_type);
    }

    /**
     * quote via parent class
     *
     * @param  string  $value
     * @param  integer $parameter_type
     * @return string
     */
    public function quote($value, $parameter_type = \PDO::PARAM_STR)
    {
        if (is_null($value)) {
            return "NULL";
        }

        return substr(parent::quote($value, $parameter_type), 1, - 1);
    }

    /**
     * Get last insert id
     *
     * @param  string $name
     * @return mixed
     */
    public function lastInsertId($name = null)
    {
        return parent::lastInsertId($name);
    }

    /**
     * ping
     * Pings Database
     *
     * @return boolean
     */
    public function ping()
    {
        try {
            $this->query('SELECT 1');
        } catch (\PDOException $e) {
            $this->connect($this->options);
        }

        return true;
    }
}
