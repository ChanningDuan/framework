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
 * Header
 * 
 * @package       ngfw
 * @subpackage    library
 * @version       1.3.5
 * @copyright (c) 2015, Nick Gejadze
 */
class Header
{

    /**
     * response code
     * Get or Set the HTTP response code.
     * Method checks if http_response_code function is available (PHP 5.4+) if not fails over to Header::set() method
     * @see  set()
     * @param  int $code HTTP response code
     * @return int response code
     */
    public static function responseCode($code = null) {
        if (!function_exists('http_response_code')){
            if (isset($code) && is_numeric($code)){
                switch ($code) {
                    case 100:
                        $text = 'Continue';
                        break;

                    case 101:
                        $text = 'Switching Protocols';
                        break;

                    case 200:
                        $text = 'OK';
                        break;

                    case 201:
                        $text = 'Created';
                        break;

                    case 202:
                        $text = 'Accepted';
                        break;

                    case 203:
                        $text = 'Non-Authoritative Information';
                        break;

                    case 204:
                        $text = 'No Content';
                        break;

                    case 205:
                        $text = 'Reset Content';
                        break;

                    case 206:
                        $text = 'Partial Content';
                        break;

                    case 300:
                        $text = 'Multiple Choices';
                        break;

                    case 301:
                        $text = 'Moved Permanently';
                        break;

                    case 302:
                        $text = 'Moved Temporarily';
                        break;

                    case 303:
                        $text = 'See Other';
                        break;

                    case 304:
                        $text = 'Not Modified';
                        break;

                    case 305:
                        $text = 'Use Proxy';
                        break;

                    case 400:
                        $text = 'Bad Request';
                        break;

                    case 401:
                        $text = 'Unauthorized';
                        break;

                    case 402:
                        $text = 'Payment Required';
                        break;

                    case 403:
                        $text = 'Forbidden';
                        break;

                    case 404:
                        $text = 'Not Found';
                        break;

                    case 405:
                        $text = 'Method Not Allowed';
                        break;

                    case 406:
                        $text = 'Not Acceptable';
                        break;

                    case 407:
                        $text = 'Proxy Authentication Required';
                        break;

                    case 408:
                        $text = 'Request Time-out';
                        break;

                    case 409:
                        $text = 'Conflict';
                        break;

                    case 410:
                        $text = 'Gone';
                        break;

                    case 411:
                        $text = 'Length Required';
                        break;

                    case 412:
                        $text = 'Precondition Failed';
                        break;

                    case 413:
                        $text = 'Request Entity Too Large';
                        break;

                    case 414:
                        $text = 'Request-URI Too Large';
                        break;

                    case 415:
                        $text = 'Unsupported Media Type';
                        break;

                    case 500:
                        $text = 'Internal Server Error';
                        break;

                    case 501:
                        $text = 'Not Implemented';
                        break;

                    case 502:
                        $text = 'Bad Gateway';
                        break;

                    case 503:
                        $text = 'Service Unavailable';
                        break;

                    case 504:
                        $text = 'Gateway Time-out';
                        break;

                    case 505:
                        $text = 'HTTP Version not supported';
                        break;

                    default:
                        exit('Unknown http status code "' . htmlentities($code) . '"');
                        break;
                }

                $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                self::set($protocol . ' ' . $code . ' ' . $text);
                Registry::set('http_response_code', $code);
                return $code;
            }else{
                $code = Registry::get('http_response_code');
                if (!isset($code) || empty($code)){
                    $code = 200;
                }
            }

            return $code;
        }else{
            if (!isset($code) || !is_numeric($code)){
                $code = null;
            }
            return http_response_code($code);
        }
    }

    /**
     * Redirect
     * Redirect to URL & Returns a REDIRECT (302) status code to the browser unless the 201 or a 3xx status code has already been set.
     *
     * @param string $url
     * @param int $http_response_code http response code
     * @return mixed
     * @internal param bool $exit exit after redirect or continue executing the code
     */
    public static function redirect($url = '/', $http_response_code) {
        if (isset($http_response_code) && is_numeric($http_response_code)){
            self::set("Location: " . $url, true, $http_response_code);
        }else{
            self::set("Location: " . $url);
        }
        exit();
    }

    /**
     * Set Header
     * Send a raw HTTP header
     *
     * @param string $string Header to set
     * @param bool $replace Overwrite current header?
     * @param string $http_response_code http_response_code to set
     */
    public static function set($string = "", $replace = true, $http_response_code = null) {
        header($string, $replace, $http_response_code);
    }

    /**
     * Powered By
     * Set new output source
     * @param string $string X-Powered-By source
     */
    public static function poweredBy($string) {
        self::set('X-Powered-By: ' . $string);
    }

    /**
     * MIME type
     * set custom mime type
     * @param string $mimeType default = "text/html"
     */
    public static function mimeType($mimeType = "text/html") {
        self::set('Content-Type: ' . $mimeType);
    }

    /**
     * Set Content length
     * @param int $length size of the file in bytes
     */
    public static function contentLength($length = 0) {
        self::set('Content-Length: ' . $length);
    }

    /**
     * Download Name
     * set new download name
     * @param  string $name Download Name
     */
    public static function downloadName($name = '') {
        self::set('Expires: 0');
        self::set('Cache-Control: private');
        self::set('Pragma: cache');
        self::set('Content-Disposition: attachment; filename="' . $name . '"');
    }

    /**
     * Expires
     * Set expiration date for cached data
     * @param int $time time in seconds
     */
    public static function expires($time = 0) {
        self::set('Expires: ' . gmdate('D, d M Y H:i:s', $time) . ' GMT');
        self::set('Cache-Control: maxage=' . ($time - time()));
        self::set('Pragma: public');
    }

    /**
     * disable cache
     * Force proxies and clients to disable caching
     */
    public static function disableCache() {
        self::set('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
        self::set('Cache-Control: no-cache, must-revalidate');
    }
}
