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
 * Authentication
 *
 * @package       ngfw
 * @subpackage    library
 * @version       1.3.4
 * @copyright (c) 2015, Nick Gejadze
 */
class Authentication {

    /**
     * $dbAdapter
     * @var object
     */
    protected $dbAdapter;

    /**
     * @table
     * @var string
     */
    protected $table;

    /**
     * $identityColumn
     * @var string
     */
    protected $identityColumn;

    /**
     * $identity
     * @var string
     */
    protected $identity;

    /**
     * $credentialColumn
     * @var string
     */
    protected $credentialColumn;

    /**
     * $credential
     * @var string
     */
    protected $credential;

    /**
     * $sessionName
     * @var string
     */
    protected $sessionName = "NG_AUTH";

    /**
     * __construct()
     * Sets dbAdapter, Table Identity Column and Credential Column if passed
     *
     * @param object $dbAdapter
     * @param string $table
     * @param string $identityColumn
     * @param string $credentialColumn
     * @see setDBAdapter()
     * @see setDBTable()
     * @see setIdentityColum()
     * @see setCredentialColumn()
     * @return \ngfw\Authentication
     */
    public function __construct($dbAdapter = null, $table = null, $identityColumn = null, $credentialColumn = null)
    {
        if (isset($dbAdapter)){
            $this->setDBAdapter($dbAdapter);
        }
        if (isset($table)){
            $this->setDBTable($table);
        }
        if (isset($identityColumn)){
            $this->setIdentityColumn($identityColumn);
        }
        if (isset($credentialColumn)){
            $this->setCredentialColumn($credentialColumn);
        }

        return $this;
    }

    /**
     * setDBAdapter()
     * if isset $dbAdapter sets dbAdapter object otherwise returns false
     *
     * @param object $dbAdapter
     * @return boolean|\ngfw\Authentication
     */
    public function setDBAdapter($dbAdapter)
    {
        if ( ! isset($dbAdapter)){
            return false;
        }
        $this->dbAdapter = $dbAdapter;

        return $this;
    }

    /**
     * setDBTable()
     * if isset $table sets Table object otherwise returns false
     *
     * @param string $table
     * @return boolean|\ngfw\Authentication
     */
    public function setDBTable($table)
    {
        if ( ! isset($table)){
            return false;
        }
        $this->table = $table;
        $this->sessionName = $this->sessionName . $this->table;

        return $this;
    }

    /**
     * setIdentityColumn()
     * if isset $identityColumn sets identityColumn object otherwise returns false
     *
     * @param string $identityColumn
     * @return boolean|\ngfw\Authentication
     */
    public function setIdentityColumn($identityColumn)
    {
        if ( ! isset($identityColumn)){
            return false;
        }
        $this->identityColumn = $identityColumn;

        return $this;
    }

    /**
     * setIdentity()
     * if isset $identity sets identity object otherwise returns false
     *
     * @param string $identity
     * @return boolean|\ngfw\Authentication
     */
    public function setIdentity($identity)
    {
        if ( ! isset($identity)){
            return false;
        }
        $this->identity = $identity;

        return $this;
    }

    /**
     * setCredentialColumn()
     * sets credential column object
     *
     * @param string $credentialColumn
     * @return boolean|\ngfw\Authentication
     */
    public function setCredentialColumn($credentialColumn)
    {
        if ( ! isset($credentialColumn)){
            return false;
        }
        $this->credentialColumn = $credentialColumn;

        return $this;
    }

    /**
     * setCredential
     * sets credential object
     *
     * @param string $credential
     * @return boolean|\ngfw\Authentication
     */
    public function setCredential($credential)
    {
        if ( ! isset($credential)){
            return false;
        }
        $this->credential = $credential;

        return $this;
    }

    /**
     * isValid()
     * Checks if user is authenticated
     *
     * @return boolean
     */
    public function isValid()
    {
        $auth = Session::get($this->sessionName);
        if ($auth){
            return true;
        }
        if (isset($this->dbAdapter) && isset($this->table) && isset($this->identityColumn) && isset($this->identity) && isset($this->credentialColumn) && isset($this->credential)){
            $user = $this->checkUserInDB();
            if (isset($user) && is_array($user)){
                $this->setSessionIdentity($user);

                return true;
            }
        }

        return false;
    }

    /**
     * checkUserInDB()
     * Builds select query to check user in DB and returns result as an array
     *
     * @return array|false
     */
    private function checkUserInDB()
    {
        $query = new Query();
        $query->select()->from($this->table)->where($this->identityColumn . " = ?", $this->identity)->andWhere($this->credentialColumn . " = ?", $this->credential)->limit(1);

        return $this->dbAdapter->fetchRow($query);
    }

    /**
     * setSessionIdentity
     * sets identity in the session
     *
     * @param array $identity
     */
    private function setSessionIdentity($identity)
    {
        Session::set($this->sessionName, serialize($identity));
    }

    /**
     * getIdentity()
     * checks if user is authenticated and return user data from session
     *
     * @see isValid()
     * @return array|boolean
     */
    public function getIdentity()
    {
        if ($this->isValid()){
            return unserialize(Session::get($this->sessionName));
        }

        return false;
    }

    /**
     * clearIdentity()
     * sets auth session to null
     *
     * @return void
     */
    public function clearIdentity()
    {
        Session::set($this->sessionName, null);
    }
}
