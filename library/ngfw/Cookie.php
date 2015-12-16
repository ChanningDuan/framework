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
 * Cookie
 *
 * @package       ngfw
 * @subpackage    library
 * @version       1.3.8
 * @copyright (c) 2015, Nick Gejadze
 */
class Cookie {

    /**
     * $instance
     * Holds Class instance
     *
     * @var object
     */
    protected static $instance = null;

    /**
     * $name
     * Holds Cookie Name
     *
     * @var string
     */
    protected $name;

    /**
     * $value
     * Holds Cookie Value
     *
     * @var string
     */
    protected $value;

    /**
     * $expire
     * Holds expiration date, Default value 0
     *
     * @var string
     */
    protected $expire = 0;

    /**
     * $path
     * Holds Cookie path, Default "/"
     *
     * @var string
     */
    protected $path = "/";

    /**
     * $domain
     * Holds domain value, default null
     *
     * @var string
     */
    protected $domain = null;

    /**
     * $secure
     * coolie should only be transmitter over a secure HTTPS
     *
     * @var bool
     */
    protected $secure = false;

    /**
     * $httponly
     * When True the cookie will be made accessible only through the HTTP protocol
     *
     * @var bool
     */
    protected $httponly = false;

    /**
     * init()
     * if $instance is not set starts new \ngfw\Cookie and return instance
     *
     * @return object
     */
    public static function init()
    {
        if (self::$instance === null){
            self::$instance = new Cookie;
        }

        return self::$instance;
    }

    /**
     * set()
     * Sets Cookie
     *
     * @see setName()
     * @see setValue()
     * @see setExpire()
     * @see setPath()
     * @see setDomain()
     * @see setSecure()
     * @see setHttponly()
     * @see save()
     * @param string $name
     * @param string $value
     * @param string $expire
     * @param string $path
     * @param string $domain
     * @param bool   $secure
     * @param bool   $httponly
     * @throws Exception
     * @return void
     */
    public static function set($name, $value, $expire = null, $path = null, $domain = null, $secure = null, $httponly = null)
    {
        if (isset($name)){
            self::init()->setName($name);
        }else{
            throw new Exception('Name is Required to set Cookie');
        }
        if (isset($value)){
            self::init()->setValue($value);
        }else{
            throw new Exception('Value is Required to set Cookie');
        }
        if (isset($expire)){
            self::init()->setExpire($expire);
        }
        if (isset($path)){
            self::init()->setPath($path);
        }
        if (isset($domain)){
            self::init()->setDomain($domain);
        }
        if (isset($secure)){
            self::init()->setSecure($secure);
        }
        if (isset($httponly)){
            self::init()->setHttponly($httponly);
        }
        self::init()->save();
    }

    /**
     * get()
     * Gets Cookie
     *
     * @param string $name
     * @return boolean|string
     */
    public static function get($name)
    {
        if (isset($_COOKIE[$name])){
            return $_COOKIE[$name];
        }

        return false;
    }

    /**
     * setName()
     * sets name object
     *
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * setValue()
     * sets value object
     *
     * @param string $value
     * @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * setExpire()
     * sets expire object
     *
     * @param string $expire
     * @return void
     */
    public function setExpire($expire)
    {
        $this->expire = $expire;
    }

    /**
     * setPath()
     * sets path object
     *
     * @param string $path
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * setDomain
     * sets domain object
     *
     * @param string $domain
     * @return void
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * setSecure
     * sets secure object
     *
     * @param bool $secure
     * @return void
     */
    public function setSecure($secure)
    {
        $this->secure = $secure;
    }

    /**
     * setHttponly
     * set httponly object
     *
     * @param bool $httponly
     * @return void
     */
    public function setHttponly($httponly)
    {
        $this->httponly = $httponly;
    }

    /**
     * save()
     * Sets Cookie
     *
     * @throws Exception
     * @return void
     */
    public function save()
    {
        if ( ! isset($this->name) ){
            throw new Exception('Name required to save cookie');
        }
        if ( ! isset($this->value) ){
            throw new Exception('Name required to save cookie');
        }
        setcookie($this->name, $this->value, $this->expire, $this->path, $this->domain, $this->secure, $this->httponly);
    }

}

