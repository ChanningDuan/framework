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
 * Route
 *
 * @package       ngfw
 * @subpackage    library
 * @version       1.3.1
 * @copyright (c) 2015, Nick Gejadze
 */
class Route {

    /**
     * $instance
     * Holds Class Instance
     *
     * @var object
     */
    private static $instance;

    /**
     * $controller
     * Holds controller name
     *
     * @var string
     */
    protected $controller;

    /**
     * $action
     * Holds action name
     *
     * @var string
     */
    protected $action;

    /**
     * $routes
     * Holds routes
     *
     * @var array
     */
    protected $routes;

    /**
     * $routeSelected
     * identifies if route is selected
     *
     * @var bool
     */
    protected $routeSelected;

    /**
     * $defaultController
     * Default Controller, Default value "Index"
     *
     * @var string
     */
    protected $defaultController = "Index";

    /**
     * $defaultAction
     * Default Action, Default value "Index"
     *
     * @var string
     */
    protected $defaultAction = "Index";

    /**
     * $request
     * Holds all requests
     *
     * @var array
     */
    public $request = array();

    /**
     * init()
     * if $instance is not set starts new \ngfw\Route and return instance
     *
     * @return object
     */
    public static function init()
    {
        if (self::$instance === null){
            self::$instance = new Route;
        }

        return self::$instance;
    }

    /**
     * setController()
     * Sets Controller Object
     *
     * @param string $controller
     * @return void
     */
    private function setController($controller)
    {
        if (isset($controller) && ! empty($controller)){
            self::init()->controller = ucfirst(strtolower($controller));
        }else{
            self::init()->controller = self::init()->defaultController;
        }
    }

    /**
     * setAction()
     * Sets Action Object
     *
     * @param string $action
     * @return void
     */
    private function setAction($action)
    {
        if (isset($action) && ! empty($action)){
            self::init()->action = ucfirst(strtolower($action));
        }else{
            self::init()->action = self::init()->defaultAction;
        }
    }

    /**
     * setRequest()
     * Sets Request Object
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    private function setRequest($key, $value)
    {
        self::init()->request[$key] = $value;
    }

    /**
     * addRoute()
     * Adds Route to Application
     *
     * @param array $route
     * @return boolean
     */
    public static function addRoute($route)
    {
        if (is_array($route) && isset($route['route'])){
            self::init()->routes[] = $route;

            return true;
        }elseif (is_array($route)){
            foreach ($route as $singleRoute){
                if (isset($singleRoute['route'])){
                    self::init()->routes[] = $singleRoute;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * determineRoute()
     * is route is not selected and route is added, determines route
     *
     * @return bool
     */
    private static function determineRoute()
    {
        if ( ! isset(self::init()->routeSelected)){
            $routes = self::init()->routes;
            if (isset($routes) && is_array($routes)){
                $pathArray = Uri::init()->getPathChunks();
                foreach ($routes as $route){
                    if ( ! isset(self::init()->routeSelected)){
                        $routeArray = explode('/', trim($route['route'], '/'));
                        if (is_array($pathArray) && ! empty($routeArray[0]) && count($routeArray) == count($pathArray)){
                            if (isset($route['defaults']['controller'])){
                                self::init()->setController($route['defaults']['controller']);
                                self::init()->routeSelected = true;
                            }
                            if (isset($route['defaults']['action'])){
                                self::init()->setAction($route['defaults']['action']);
                                self::init()->routeSelected = true;
                            }
                            foreach ($routeArray as $routeKey => $routeSegment){
                                if (preg_match('/^:[\w]{1,}$/', $routeSegment)){
                                    switch ($routeSegment){
                                        case":controller":
                                            self::init()->setController($pathArray[$routeKey]);
                                            self::init()->routeSelected = true;
                                            break;
                                        case ":action":
                                            self::init()->setAction($pathArray[$routeKey]);
                                            self::init()->routeSelected = true;
                                            break;
                                        default:
                                            self::init()->setRequest(substr($routeSegment, 1), $pathArray[$routeKey]);
                                            self::init()->routeSelected = true;
                                            break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return self::init()->routeSelected;
    }

    /**
     * getController()
     * Returns Controller
     *
     * @return string
     */
    public static function getController()
    {
        self::determineRoute();
        if ( ! isset(self::init()->controller)){
            $path = Uri::init()->getPathArray();
            $controller = @key($path);
            self::init()->setController($controller);
        }

        return self::init()->controller;
    }

    /**
     * getAction()
     * Returns Action
     *
     * @return string
     */
    public static function getAction()
    {
        self::determineRoute();
        if ( ! isset(self::init()->action)){
            $path = Uri::init()->getPathArray();
            $action = @reset($path);
            self::init()->setAction($action);
        }

        return self::init()->action;
    }

    /**
     * getRequests()
     * Returns requests
     *
     * @return array
     */
    public static function getRequests()
    {
        self::determineRoute();
        $uri = new Uri();
        if ( ! self::init()->request) {
            $path = $uri->getPathArray();
            if (is_array($path) && ! empty($path)){
                foreach (array_slice($path, 1) as $key => $value){
                    self::init()->setRequest($key, $value);
                }
            }
        }
        if (is_array($uri->getQueryString())) {
            $tmp = self::init()->request;
            foreach ($uri->getQueryString() as $key => $value) {
                if ( ! isset($tmp[$key])){
                    self::init()->setRequest($key, $value);
                }
            }
        }

        return self::init()->request;
    }

    /**
     * redirect
     * If headers is not sent add status header and redirects
     *
     * @param string $url
     * @param int    $status
     */
    public static function redirect($url = '/', $status = 302)
    {
        Header::redirect($url, $status);
    }

}
