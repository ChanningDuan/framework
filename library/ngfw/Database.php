<?php

/**
 * ngfw
 * ---
 * Copyright (c) 2014, Nick Gejadze
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
 * @package ngfw
 * @version 1.1
 * @author Nick Gejadze
 */
class Database extends \PDO
{
    
    /**
     * Default CHARSET
     */
    
    const CHARSET = 'UTF8';
    
    /**
     * $options
     * Database Parameters
     * Database Parameters
     * @var array
     */
    private $options;
    
    /**
     * __construct
     * sets options and Connections to Database
     * sets options and Connections to Database
     * @param type $options
     */
    public function __construct($options = null, $autoConnect = true) {
        $this->setOptions( $options );
        if($autoConnect and isset($this->options) and !empty($this->options)):
            $this->connect($this->options);
        endif;
    }

    /**
     * Set options object
     * @param array $options database connection settings
     */
    private function setOptions($options){
        if(isset($options) or !empty($options)):
            $this->options = $options;
        endif;
    }
    
    /**
     * connect
     * Connects to database
     * Connects to database
     * @param array $options
     */
    private function connect($options) {
        $this->setOptions( $options );
        $dsn = $this->createdsn();
        $attrs = !isset($this->options['charset']) ? array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . self::CHARSET) : array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . $this->options['charset']);
        try {
            parent::__construct($dsn, $this->options['username'], $this->options['password'], $attrs);
        }
        catch(PDOException $e) {
            if (defined('DEVELOPMENT_ENVIRONMENT') and DEVELOPMENT_ENVIRONMENT):
                echo 'Connection failed: ' . $e->getMessage();
            endif;
        }
    }
    
    /**
     * createdsn
     * Creates Data Source Name
     * @return string
     */
    private function createdsn() {
        if(!isset($this->options) or empty($this->options)):
            return false;
        endif;
        return $this->options['dbtype'] . ':host=' . $this->options['host'] . ';port=' . $this->options['port'] . ';dbname=' . $this->options['dbname'];
    }
    
    /**
     * fetchAll
     * Fetches database and returns result as array
     * @param string $sql
     * @param string $sql
     * @return mixed
     */
    public function fetchAll($sql) {
        try {
            $pdostmt = $this->prepare($sql);
            if ($pdostmt->execute() !== false):
                if (preg_match("/^(" . implode("|", array("SELECT", "DESCRIBE", "PRAGMA", "SHOW")) . ") /i", $sql)):
                    return $pdostmt->fetchAll(\PDO::FETCH_ASSOC);
                endif;
            endif;
        }
        catch(PDOException $e) {
            if (defined('DEVELOPMENT_ENVIRONMENT') and DEVELOPMENT_ENVIRONMENT):
                echo $e->getMessage();
            endif;
            return false;
        }
    }
    
    /**
     * fetchRow
     * Retuns single row from database and return result as array
     * @param string $sql
     * @param string $sql
     * @return mixed
     */
    public function fetchRow($sql) {
        try {
            $pdostmt = $this->prepare($sql);
            if ($pdostmt->execute() !== false):
                if (preg_match("/^(" . implode("|", array("SELECT", "DESCRIBE", "PRAGMA", "SHOW")) . ") /i", $sql)):
                    return $pdostmt->fetch(\PDO::FETCH_ASSOC);
                endif;
            endif;
        }
        catch(PDOException $e) {
            if (defined('DEVELOPMENT_ENVIRONMENT') and DEVELOPMENT_ENVIRONMENT):
                echo $e->getMessage();
            endif;
            return false;
        }
    }
    
    /**
     * run
     * Executes Query
     * @param string $sql
     * @param array $data
     * @return mixed
     */
    public function query($sql, $data=null) {
        try {
            $pdostmt = $this->prepare($sql);
            if ($pdostmt->execute($data) !== false):
                if (preg_match("/^(" . implode("|", array("SELECT", "DESCRIBE", "PRAGMA", "SHOW", "DESCRIBE")) . ") /i", $sql)):
                    return $pdostmt->fetchAll(\PDO::FETCH_ASSOC);
                elseif (preg_match("/^(" . implode("|", array("DELETE", "INSERT", "UPDATE")) . ") /i", $sql)):
                    return $pdostmt->rowCount();
                endif;
            endif;
        }
        catch(PDOException $e) {
            if (defined('DEVELOPMENT_ENVIRONMENT') and DEVELOPMENT_ENVIRONMENT):
                echo $e->getMessage();
            endif;
            return false;
        }
    }
    
    /**
     * escape, quote() method alias
     * @param  string $value
     * @param  object $parameter_type
     * @return string
     */
    public function escape($value, $parameter_type = \PDO::PARAM_STR) {
        return $this->quote($value, $parameter_type);
    }
    
    /**
     * quote via parent class
     * @param  string $value
     * @param  object $parameter_type
     * @return string
     */
    public function quote($value, $parameter_type = \PDO::PARAM_STR) {
        if (is_null($value)) {
            return "NULL";
        }
        return substr(parent::quote($value, $parameter_type), 1, -1);
    }
    
    /**
     * Get last insert id
     * @param  string $name
     * @return mixed
     */
    public function lastInsertId($name = null) {
        return parent::lastInsertId($name);
    }
    
    /**
     * ping
     * Pings Database
     * Pings Database
     * @return boolean
     */
    public function ping() {
        try {
            $this->query('SELECT 1');
        }
        catch(PDOException $e) {
            $this->connect($this->options);
        }
        return true;
    }
}
