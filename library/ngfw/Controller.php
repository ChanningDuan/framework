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
 * Controller
 * @package ngfw
 * @subpackage library
 * @version 1.2.3
 * @copyright (c) 2015, Nick Gejadze
 */
class Controller {

    /**
     * $view
     * Holds View Instance
     * @var object
     */
    protected $view;

    /**
     * __construct()
     * check if init() method is declared and runs
     * @see \ngfw\Route
     * @see \ngfw\View
     */
    public function __construct() {
        $className = Route::getController();
        $method = Route::getAction();
        $this->view = new View($className, $method);
        if (method_exists($this, 'init')):
            $this->init();
        endif;
    }

    /**
     * setViewObject()
     * Sets View object
     * @param string $name
     * @param string $value
     * @return void
     */
    public function setViewObject($name, $value) {
        $this->view->set($name, $value);
    }

    /**
     * __destruct()
     * loads layout from view
     * @return void
     */
    public function startRander() {
        $this->view->loadLayout();
    }

}
