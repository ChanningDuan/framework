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
 * View
 *
 * @package       ngfw
 * @subpackage    library
 * @version       1.3.2
 * @copyright (c) 2015, Nick Gejadze
 */
class View {

    /**
     * $controller
     * Holds controller name
     *
     * @var string
     */
    protected $controller;

    /**
     * $action
     * View file filename without extension
     *
     * @var string
     */
    protected $action;

    /**
     * $layout
     * Enable or disable layout
     *
     * @var boolean
     */
    protected $layout = true;

    /**
     * $render
     * Enable or disable view (View Component)
     *
     * @var boolean
     */
    protected $render = true;

    /**
     * $layoutFile
     * Layout file filename without extension
     *
     * @var string
     */
    protected $layoutFilename = 'Layout';

    /**
     * $template
     * Template directory, Must be under '~Application' Directory and should contain Layout and View Directories
     *
     * @var mixed
     */
    protected $template = null;

    /**
     * $extension
     * View and Layout files extension
     *
     * @var string
     */
    protected $extension = ".phtml";

    /**
     * $layoutFilePath
     * Full path of layout file
     *
     * @var string
     */
    protected $layoutFilePath;

    /**
     * $viewFilePath
     * Full path of view file
     *
     * @var string
     */
    protected $viewFilePath;


    /**
     * __construct
     * Sets controller and action object
     *
     * @param string $controller
     * @param string $action
     */
    public function __construct($controller, $action)
    {
        $this->controller = $controller;
        $this->action = strtolower($action);
    }

    /**
     * enableLayout
     * Sets layout object
     *
     * @param boolean $bool
     */
    public function enableLayout($bool = true)
    {
        $this->layout = $bool;
    }

    /**
     * enableView
     * Sets render object
     *
     * @param boolean $bool
     */
    public function enableView($bool = true)
    {
        $this->render = $bool;
    }

    /**
     * setLayoutFile
     * sets layout filename object
     *
     * @param string $filename
     */
    public function setLayoutFile($filename)
    {
        $this->layoutFilename = $filename;
    }

    /**
     * setFileExtension
     * override default file extension
     *
     * @param string $extension file extension
     */
    public function setFileExtension($extension)
    {
        $this->extension = $extension;
    }

    /**
     * set
     * Set object to be used from view
     *
     * @param string $name
     * @param string $value
     */
    public function set($name, $value)
    {
        $this->{$name} = $value;
    }

    /**
     * Check if Layout file exists
     *
     * @return bool
     */
    public function setLayoutPath()
    {
        if( defined('ROOT') ){
            $this->layoutFilePath = ROOT;
        }else{
            $this->layoutFilePath = $_SERVER["DOCUMENT_ROOT"];
        }
        $this->layoutFilePath .= DIRECTORY_SEPARATOR;
        if( defined('APPDIR') ){
            $this->layoutFilePath .= APPDIR;
        }else{
            $this->layoutFilePath .= "Application";
        }
        $this->layoutFilePath .= DIRECTORY_SEPARATOR;
        if(! empty($this->template) ){
            $this->layoutFilePath .= trim($this->template, "/") . DIRECTORY_SEPARATOR;
        }
        $this->layoutFilePath .= 'Layout' . DIRECTORY_SEPARATOR . $this->layoutFilename . $this->extension; 
        if (file_exists($this->layoutFilePath)){
            return true;
        }

        return false;
    }

    /**
     * Check if View file exists
     *
     * @return bool
     */
    public function setViewPath()
    {
        if( defined('ROOT') ){
            $this->viewFilePath =  ROOT;
        }else{
            $this->viewFilePath = $_SERVER["DOCUMENT_ROOT"];
        } 
        $this->viewFilePath .= DIRECTORY_SEPARATOR;
        if( defined('APPDIR') ){
            $this->viewFilePath .= APPDIR;
        }else{
            $this->viewFilePath .= "Application";
        }
        $this->viewFilePath .= DIRECTORY_SEPARATOR;
        if(! empty($this->template) ){
            $this->viewFilePath .= trim($this->template, "/") . DIRECTORY_SEPARATOR;
        }
        $this->viewFilePath .= 'View' . DIRECTORY_SEPARATOR . $this->controller . DIRECTORY_SEPARATOR . ucfirst(strtolower($this->action)) . $this->extension;
        if (file_exists($this->viewFilePath)){
            return true;
        }

        return false;
    }

    /**
     * loadLayout
     * Includes layout file
     *
     * @throws \ngfw\Exception If View or layout file is enable but does not exists
     */
    public function loadLayout()
    {
        if ($this->render && ! $this->setViewPath()){
            throw new Exception(sprintf('View file "%s" does not exist.', $this->viewFilePath));
        }
        if ($this->layout){
            if ($this->setLayoutPath()){
                include_once($this->layoutFilePath);
            }else{
                throw new Exception(sprintf('Layout file "%s" does not exist.', $this->layoutFilePath));
            }
        }else{
            $this->render();
        }
    }

    /**
     * render
     * Check is render is enabled and includes view file
     */
    public function render()
    {
        if ($this->render){
            include_once($this->viewFilePath);
        }
    }
}
