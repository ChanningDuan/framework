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
 * Uri
 *
 * @package       ngfw
 * @subpackage    library
 * @version       1.3.9
 * @copyright (c) 2015, Nick Gejadze
 */
class Uri {

    /**
     * $instance
     * Holds Class Instance
     *
     * @var object
     */
    protected static $instance = null;

    /**
     * $requestedPath
     * Holds $_SERVER['REQUEST_URI']
     *
     * @var string
     */
    protected $requestedPath;

    /**
     * $rootPath
     * Holds ROOT path
     *
     * @var string
     */
    protected $rootPath;

    /**
     * $subdirectories
     * Holds Subdirectories if any..
     *
     * @var array
     */
    protected $subdirectories;

    /**
     * $baseURL
     * Holds base URL of application
     *
     * @var string
     */
    protected $baseURL;

    /**
     * $query_string
     * holds teh array of query string from get requests
     *
     * @var array
     *
     */
    protected $query_string;

    /**
     * __construct
     * Sets reuqestedPath, query_string and rootPath objects, if PUBLIC_PATH is not defined, rootPath will fallback to
     * $_SERVER["DOCUMENT_ROOT"]
     */
    public function __construct()
    {
        $this->requestedPath = $_SERVER['REQUEST_URI'];
        $this->requestedPath = (strstr($this->requestedPath, '?') ? substr($this->requestedPath, 0, strpos($this->requestedPath, '?')) : $this->requestedPath);
        $this->query_string = $_GET;
        if (defined('PUBLIC_PATH')){
            $this->rootPath = PUBLIC_PATH;
        }else{
            $this->rootPath = $_SERVER["DOCUMENT_ROOT"];
        }
    }

    /**
     * init()
     * if $instance is not set starts new \ngfw\Uri and return instance
     *
     * @return object
     */
    public static function init()
    {
        if (self::$instance === null){
            self::$instance = new Uri;
        }

        return self::$instance;
    }

    /**
     * baseUrl()
     * Checks if baseURL was set, if not returns $_SERVER['HTTP_HOST']
     *
     * @return string
     */
    public static function baseUrl()
    {
        if ( ! isset(self::init()->baseURL) || empty(self::init()->baseURL)){
            $subdirectories = null;
            if (isset(self::init()->subdirectories) && is_array(self::init()->subdirectories) && ! empty(self::init()->subdirectories)){
                $subdirectories = implode("/", self::init()->subdirectories) . "/";
            }
            if ( ! isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == ""){
                self::setBaseUrl('http://' . $_SERVER['HTTP_HOST'] . "/" . $subdirectories);
            }else{
                self::setBaseUrl('https://' . $_SERVER['HTTP_HOST'] . "/" . $subdirectories);
            }
        }

        return self::init()->baseURL;
    }

    /**
     * setBaseUrl()
     * sets application baseURL
     *
     * @param string $url
     * @return void
     */
    public static function setBaseUrl($url)
    {
        self::init()->baseURL = $url;
    }

    /**
     * getQueryString()
     * return the query string
     *
     * @return array
     */
    public function getQueryString()
    {
        return $this->query_string;
    }

    /**
     * getPath()
     * return requestedPath object if set, otherwise false is returned
     *
     * @return mixed
     */
    public function getPath()
    {
        if (isset($this->requestedPath)){
            return $this->requestedPath;
        }

        return false;
    }

    /**
     * getPathArray()
     * Returns path as array
     * e.g.:  /category/music/page/123 will be translated to array("category" => "music", "page" => "123")
     *
     * @see pathToArray()
     * @return mixed
     */
    public function getPathArray()
    {
        return $this->pathToArray();
    }

    /**
     * pathToArray()
     * Translates path to array, sets array as $key => $value
     *
     * @see getPathChunks()
     * @return mixed
     */
    public function pathToArray()
    {
        $pathChunks = $this->getPathChunks();
        if ($pathChunks){
            $result = array();
            $sizeOfPathChunks = sizeof($pathChunks);
            for ($i = 0; $i < $sizeOfPathChunks; $i += 2){
                $result[preg_replace("/\\.[^.\\s]{2,4}$/", "", $pathChunks[$i])] = isset($pathChunks[$i + 1]) ? preg_replace("/\\.[^.\\s]{2,4}$/", "", $pathChunks[$i + 1]) : false;
            }

            return $result;
        }

        return false;
    }

    /**
     * getPathChunks()
     * explodes requestedPath and rootPath, determines parameters and returns as array, false is returned if no segment
     * is found in the requestedPath
     *
     * @return mixed
     */
    public function getPathChunks()
    {
        if (isset($this->requestedPath)){
            $pathChunks = explode('/', trim($this->requestedPath, '/'));
            $rootChunks = explode('/', trim($this->rootPath, '/'));
            self::init()->subdirectories = array_intersect($pathChunks, $rootChunks);
            foreach (self::init()->subdirectories as $key => $directory){
                unset($pathChunks[$key]);
            }
            $pathChunks = array_values($pathChunks);
            if ( ! empty($pathChunks[0])){
                return $pathChunks;
            }
        }

        return false;
    }
}

