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
 * Bootstrap
 *
 * @package       ngfw
 * @subpackage    library
 * @version       1.3.1
 * @copyright (c) 2015, Nick Gejadze
 */
class Bootstrap {

    /**
     * $_controllerLoaded
     * Holds path to template directory
     *
     * @var mixed
     */
    protected $_viewTemplate = false;

    /**
     * $_controllerLoaded
     * Holds boolean value of controller loaded status
     *
     * @var boolean
     */
    protected $_controllerLoaded = false;

    /**
     * $_controllerObject
     * Holds controller instance
     *
     * @var object
     */
    protected $_controllerObject;

    /**
     * __construct()
     * Instantiates new auto loader and all methods
     *
     * @see initMethods()
     */
    public function __construct()
    {
        $this->initMethods();
    }

    /**
     * initMethods()
     * Calls Every Class method with starts with "_" OR "__"
     *
     * @return void
     */
    private function initMethods()
    {
        foreach (get_class_methods($this) as $method){
            if (substr($method, 0, 1) == "_" && substr($method, 0, 2) !== "__"){
                call_user_func(array($this, $method));
            }
        }
        $this->loadController();
    }

    /**
     * Set Template path object
     *
     * @param string $templatePath
     */
    protected function setTemplate($templatePath)
    {
        $this->_viewTemplate = $templatePath;
    }

    /**
     * _loadController()
     * Loads application controller
     *
     * @see \ngfw\Route
     * @throws \ngfw\Exception
     * @return void
     */
    private function loadController()
    {
        if ( ! $this->_controllerLoaded){
            $controllerTitle = Route::getController() . "Controller";
            if (class_exists($controllerTitle)){
                $this->_controllerObject = new $controllerTitle;
            }else{
                throw new Exception(sprintf('The requested Controller "%s" does not exist.', $controllerTitle));
            }
            if ($this->_viewTemplate){
                $this->_controllerObject->setViewObject("template", $this->_viewTemplate);
            }
            $this->_controllerLoaded = true;
            $method = Route::getAction() . "Action";
            if (is_callable(array($this->_controllerObject, $method))){
                call_user_func(array($this->_controllerObject, $method));
                $this->_controllerObject->startRander();
            }else{
                throw new Exception(sprintf('The requested method "%s" does not exist in %s.', $method, $controllerTitle));
            }
        }
    }
}
