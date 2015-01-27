<?php
namespace ngfw;

/**
 * Database
 * @package ngfw
 * @version 1.0
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
     * @access private
     * @var array
     */
    private $options;
    
    /**
     * __construct()
     * sets Opetions and Connections to Database
     * @access public
     * @param type $options
     */
    public function __construct($options, $autoConnect = true) {
        $this->options = $options;
        if($autoConnect):
            $this->connect($this->options);
        endif;
    }
    
    /**
     * connect()
     * Connects to database
     * @access private
     * @param array $options
     */
    private function connect($options) {
        if(!isset($options) or empty($options)):
            $options = $this->options;
        endif;
        $dsn = $this->createdsn($options);
        $attrs = !isset($options['charset']) ? array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . self::CHARSET) : array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . $options['charset']);
        try {
            parent::__construct($dsn, $options['username'], $options['password'], $attrs);
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
     * @param array $options
     * @access private
     * @return string
     */
    private function createdsn($options) {
        return $options['dbtype'] . ':host=' . $options['host'] . ';port=' . $options['port'] . ';dbname=' . $options['dbname'];
    }
    
    /**
     * fetchAll()
     * Fetches database and returns result as array
     * @param string $sql
     * @access public
     * @return array|boolean
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
     * fetchRow()
     * Retuns single row from database and return result as array
     * @param string $sql
     * @access public
     * @return array|boolean
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
     * run()
     * Executes Query
     * @param string $sql
     * @access public
     * @return array|int|boolean
     */
    public function query($sql) {
        try {
            $pdostmt = $this->prepare($sql);
            if ($pdostmt->execute() !== false):
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
     * ping()
     * Pings Database
     * @access public
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
