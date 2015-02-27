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
use ngfw\Httpclient;
/**
 * Webquery
 * @package ngfw
 * @subpackage library
 * @version 1.2.0
 * @copyright (c) 2015, Nick Gejadze
 */
class Webquery {

    /**
     * $select
     * @var string
     */
    protected $select;

    /**
     * $from
     * @var string
     */
    protected $from;

    /**
     * $where
     * @var array
     */
    protected $where;

    /**
     * $whereKey
     * @var int
     */
    protected $whereKey = 0;

    /**
     * $httpclient
     * @var object
     */
    protected $httpclient;

    /**
     * $dom
     * @var object
     */
    protected $dom;

    /**
     * __construct()
     * Sets Httpclient and DOMDocument objects
     * @return void
     */
    public function __construct() {
        $this->httpclient = new Httpclient();
        $this->dom = new \DOMDocument();
    }

    /**
     * select()
     * Sets select object
     * @param string $select
     * @return \ngfw\Webquery
     */
    public function select($select = "*") {
        $this->select = $select;
        return $this;
    }

    /**
     * from()
     * passes uri param to Httpclient::setUri()
     * @see Httpclient::setUri()
     * @param string $uri
     * @return \ngfw\Webquery
     */
    public function from($uri = null) {
        if (isset($uri) and !empty($uri)):
            $this->httpclient->setUri($uri);
        endif;
        return $this;
    }

    /**
     * where()
     * Sets where object
     * @param string $where
     * @param string $value
     * @return \ngfw\Webquery
     */
    public function where($where = null, $value = null) {
        if (is_array($this->where)):
            $this->whereKey = count($this->where) + 1;
        endif;
        $this->where[$this->whereKey]['attr'] = $where;
        $this->where[$this->whereKey]['value'] = $value;
        return $this;
    }

    /**
     * execute()
     * builds and runs query, result returned as array
     * @return array
     */
    public function execute() {
        $result = array();
        $content = $this->httpclient->request();
        $this->content = $content['content'];
        @$this->dom->loadHTML('<?xml encoding="UTF-8">' . $this->content);
        if (isset($this->select) and $this->select != "*"):
            $xpath = new \DOMXpath($this->dom);
            $nodes = $xpath->query("//" . $this->select);
            $html = '';
            foreach ($nodes as $node):
                $html.= $this->removeHeaders($this->dom->saveHTML($node));
            endforeach;
            @$this->dom->loadHTML('<?xml encoding="UTF-8">' . $html);
        endif;
        if (isset($this->where)):
            $xpath = new \DOMXpath($this->dom);
            foreach ($this->where as $where):
                $nodes = $xpath->query("//*[contains(concat(' ', @" . $where['attr'] . ", ' '), '" . $where['value'] . "')]");
                foreach ($nodes as $node):
                    $result[] = $this->removeHeaders($this->dom->saveHTML($node));
                endforeach;
            endforeach;
        endif;
        if (!isset($this->where) and empty($result)):
            $result[] = $this->removeHeaders($this->dom->saveHTML());
        endif;
        return $result;
    }

    /**
     * removeHeaders()
     * removes extra headers added by DOMDocument
     * @param string $content
     * @return string
     */
    private function removeHeaders($content) {
        $content = str_replace('<?xml encoding="UTF-8">', "", $content);
        $content = str_replace('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">', "", $content);
        $content = str_replace('<html><body>', "", $content);
        $content = str_replace('</body></html>', "", $content);
        return $content;
    }

}
