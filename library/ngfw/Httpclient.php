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
 * Httpclient
 * @package ngfw
 * @version 1.2.3
 * @copyright (c) 2015, Nick Gejadze
 */
class Httpclient {

    /**
     * $uri
     * @var string
     */
    protected $uri;

    /**
     * $maxredirects;
     * @var int
     */
    protected $maxredirects = 0;

    /**
     * $timeout
     * @var int
     */
    protected $timeout = 30;

    /**
     * $userAgent
     * @var string
     */
    protected $userAgent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";

    /**
     * $cookie
     * @var string
     */
    protected $cookie;

    /**
     * $postData
     * @var string
     */
    protected $postData;

    /**
     * $postDataArray
     * @var array
     */
    protected $postDataArray;

    /**
     * $requestHeadersArray
     * stores requests headers used in CURLOPT_HTTPHEADER
     */
    protected $requestHeadersArray = array();

    /**
     * __construct()
     * Sets $uri, $maxredirects and $timeout objects if passed
     * @param string $uri
     * @param int $maxredirects
     * @param int $timeout
     */
    public function __construct($uri = null, $maxredirects = null, $timeout = null) {
        if (isset($uri) && !empty($uri)){
            $this->setUri($uri);
        }
        if (isset($maxredirects) && !empty($maxredirects)){
            $this->setMaxredirects($maxredirects);
        }
        if (isset($timeout) && !empty($timeout)){
            $this->setTimeout($timeout);
        }
        if (isset($_SERVER['HTTP_USER_AGENT']) && !empty($_SERVER['HTTP_USER_AGENT'])){
            $this->setUserAgent($_SERVER['HTTP_USER_AGENT']);
        }
    }

    /**
     * setUserAgent
     * Sets User Agent
     * @param string $userAgent
     * @return object Httpclient()
     */
    public function setUserAgent($userAgent) {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * addRequestHeader
     * Adds a header to the request
     * header_str is a fully qualified header, like "Content-type: text/plain"
     *
     * @param $header_str
     */
    public function addRequestHeader($header_str) {
      $this->requestHeadersArray[] = $header_str;
    }

    /**
     * setUri()
     * Sets URI object
     * @param string $uri
     * @return object Httpclient()
     */
    public function setUri($uri = null) {
        $this->uri = str_replace("&amp;", "&", trim($uri));
        return $this;
    }

    /**
     * setMaxredirects()
     * sets Max Redirects object, default 0
     * @param int $maxredirects
     * @return object Httpclient()
     */
    public function setMaxredirects($maxredirects = 0) {
        $this->maxredirects = $maxredirects;
        return $this;
    }

    /**
     * setTimeout()
     * Sets timeout object
     * @param int $timeout
     * @return object Httpclient()
     */
    public function setTimeout($timeout = 30) {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * post()
     * sets post object
     * @param array $array
     * @return object Httpclient()
     */
    public function post($array) {
        if (isset($array) && is_array($array)){
            $fields = "";
            foreach ($array as $key => $value){
                $fields.= $key . '=' . $value . '&';
            }
            $this->postData = rtrim($fields, '&');
            $this->postDataArray = $array;
        }
        return $this;
    }

    /**
     * setupCookie()
     * Create TMP directory Under root dir if does not exsist and sames cookie as temporary file
     * @return void
     */
    private function setupCookie() {
        if (!is_dir($_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "TMP" . DIRECTORY_SEPARATOR . "COOKIES")){
            if (!is_dir($_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "TMP")){
                @mkdir($_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "TMP", 0777);
            }
            @mkdir($_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "TMP" . DIRECTORY_SEPARATOR . "COOKIES", 0777);
        }
        $this->cookie = tempnam($_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "TMP" . DIRECTORY_SEPARATOR . "COOKIES", "CURLCOOKIE");
    }

    /**
     * cleanUpCookie()
     * removes Created cookie
     * Removes Created cookie file
     * @return  void
     */
    private function cleanUpCookie() {
        if(isset($this->cookie)){
            unlink($this->cookie);
        }
    }

    /**
     * request()
     * Requests uri via Curl, if 301 or 302 found, follows the link
     * @return array
     */
    public function request() {
        $this->setupCookie();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_URL, $this->uri);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
        if (isset($this->postData) && !empty($this->postData)){
            curl_setopt($ch, CURLOPT_POST, count($this->postDataArray));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->postData);
        }
        if(count($this->requestHeadersArray) > 0 ) {
               curl_setopt($ch, CURLOPT_HTTPHEADER, $this->requestHeadersArray);
        }
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_MAXREDIRS, $this->maxredirects);
        $response['content'] = curl_exec($ch);
        $response['info'] = curl_getinfo($ch);
        curl_close($ch);
        unset($ch);
        $this->cleanUpCookie();
        if ($response['info']['http_code'] == 301 || $response['info']['http_code'] == 302){
            $headers = get_headers($response['info']['url']);
            foreach ($headers as $value){
                if (substr(strtolower($value), 0, 9) == "location:"){
                    $this->uri = trim(substr($value, 9, strlen($value)));
                    return $this->request();
                }
            }
        }
        if (preg_match("/window\.location\.replace\('(.*)'\)/i", $response['content'], $value) || preg_match("/window\.location\=\"(.*)\"/i", $response['content'], $value)){
            $this->uri = $value[1];
            return $this->request();
        }else{
            return $response;
        }
    }
}
