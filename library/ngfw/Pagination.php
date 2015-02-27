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
use ngfw\Query;

/**
 * Pagination
 * @package ngfw
 * @subpackage library
 * @version 1.2.0
 * @copyright (c) 2015, Nick Gejadze
 */
class Pagination {

    /**
     * $instance
     * Holds Class instance
     * @var object
     */
    protected static $instance = null;

    /**
     * $db
     * Holds Datanase connection
     * @var object
     */
    protected $db;

    /**
     * $table
     * Holds Table Name
     * @var string
     */
    protected $table;

    /**
     * $select
     * Holds Select Fields
     * @var string|array
     */
    protected $select;

    /**
     * $where
     * Holds Where Statement
     * @var string
     */
    protected $where;

    /**
     * $itemsPerPage
     * Holds number of iterm per page, default value 10
     * @var int 
     */
    protected $itemsPerPage = 10;

    /**
     * $currentPage
     * Holds Current Page Number, Default value 1
     * @var int
     */
    protected $currentPage = 1;

    /**
     * $totalPages
     * Holds number of total pages
     * @var int
     */
    protected $totalPages;

    /**
     * $totalCount
     * Holds number of total row count
     * @var type 
     */
    protected $totalCount;

    /**
     * $orderByField
     * Holds order field
     * @var string 
     */
    protected $orderByField;

    /**
     * $defaultOrder
     * Holds orderby, Default is DESC
     * @var type 
     */
    protected $defaultOrder = "DESC";

    /**
     * $defaultPageNumberName
     * Holds Default Page Number Name, Default value 'pagenumber'
     * @var type 
     */
    protected $defaultPageNumberName = "pagenumber";

    /**
     * $defaultPaginationSegmentName
     * Holds Default Pagination Segment Name, Default value 'name'
     * @var type 
     */
    protected $defaultPaginationSegmentName = "name";

    /**
     * $defaultPaginationSegmentStatus
     * Holds default pagination segment status, Default value 'status'
     * @var type 
     */
    protected $defaultPaginationSegmentStatus = "status";

    /**
     * $defaultPaginationSegmentNameFirst
     * Holds default pagination segment name for first page, default value 'First'
     * @var type 
     */
    protected $defaultPaginationSegmentNameFirst = "First";

    /**
     * $defaultPaginationSegmentNamePrevious
     * Holds default pagination segment name for Previous page, default value 'Previous'
     * @var type 
     */
    protected $defaultPaginationSegmentNamePrevious = "Previous";

    /**
     * $defaultPaginationSegmentNameNext
     * Holds default pagination segment name for Next page, default value 'Next'
     * @var type 
     */
    protected $defaultPaginationSegmentNameNext = "Next";

    /**
     * $defaultPaginationSegmentNameLast
     * Holds default pagination segment name for Last page, default value 'Last'
     * @var type 
     */
    protected $defaultPaginationSegmentNameLast = "Last";

    /**
     * init()
     * if $instance is not set starts new \ngfw\Pagination and return instance
     * @return object
     */
    public static function init() {
        if (self::$instance === null):
            self::$instance = new Pagination;
        endif;
        return self::$instance;
    }

    /**
     * setAdapter()
     * Sets Database Adapter
     * @param object $db
     * @return \ngfw\Pagination
     */
    public function setAdapter($db) {
        $this->db = $db;
        return $this;
    }

    /**
     * setSelect()
     * Sets Select 
     * @param string|array $select
     * @return \ngfw\Pagination
     */
    public function setSelect($select) {
        $this->select = $select;
        return $this;
    }

    /**
     * setTable()
     * Sets Database Table
     * @param string $table
     * @return \ngfw\Pagination
     */
    public function setTable($table) {
        $this->table = $table;
        return $this;
    }

    /**
     * setItemsPerPage()
     * Set number of items per page
     * @param int $int
     * @return \ngfw\Pagination
     */
    public function setItemsPerPage($int) {
        $this->itemsPerPage = $int;
        return $this;
    }

    /**
     * setCurrentPage()
     * Set current page
     * @param int $page
     * @return \ngfw\Pagination
     */
    public function setCurrentPage($page) {
        $this->currentPage = $page;
        return $this;
    }

    /**
     * setWhereClause()
     * Set where clause
     * @param string $where
     * @return \ngfw\Pagination
     */
    public function setWhereClause($where) {
        $this->where = $where;
        return $this;
    }

    /**
     * setOrderBy()
     * Set order by 
     * @param string $field
     * @return \ngfw\Pagination
     */
    public function setOrderBy($field) {
        $this->orderByField = $field;
        return $this;
    }

    /**
     * setOrderClause()
     * Set order clause
     * @param string $clause
     * @return \ngfw\Pagination
     */
    public function setOrderClause($clause) {
        $this->defaultOrder = $clause;
        return $this;
    }

    /**
     * getResult()
     * Gets Result data
     * @return array
     */
    public function getResult() {
        $this->calculateTotal();
        $this->query = new Query;
        if (!isset($this->select)):
            $this->select = "*";
        endif;
        $this->query->select($this->select)->from($this->table);
        if (isset($this->where)):
            $this->query->where($this->where);
        endif;
        if (!isset($this->orderByField)):
            $this->determineAutoIncrement();
        endif;
        if (isset($this->orderByField)):
            $this->query->order($this->orderByField, $this->defaultOrder);
        endif;
        $limitFrom = (($this->currentPage - 1) * $this->itemsPerPage);
        if ($limitFrom >= $this->totalCount):
            $limitFrom = 0;
        endif;
        $this->query->limit($limitFrom . ", " . $this->itemsPerPage);        
        return $this->db->fetchAll($this->query->__toString());
    }

    /**
     * getPagination()
     * gets pagination
     * @param int $range
     * @param bool $nextAndPreviousButtons
     * @param bool $additionalButtons
     * @return array
     */
    public function getPagination($range = 5, $nextAndPreviousButtons = true, $additionalButtons = true) {
        $this->calculateTotal();        
        if ($additionalButtons):
            $paginator[] = array($this->defaultPageNumberName => 1,
                $this->defaultPaginationSegmentName => $this->defaultPaginationSegmentNameFirst,
                $this->defaultPaginationSegmentStatus => ($this->currentPage > 1 ? true : false)
            );
        endif;
        if ($nextAndPreviousButtons):
            $paginator[] = array($this->defaultPageNumberName => ($this->currentPage > 1 ? ($this->currentPage - 1) : $this->currentPage),
                $this->defaultPaginationSegmentName => $this->defaultPaginationSegmentNamePrevious,
                $this->defaultPaginationSegmentStatus => ($this->currentPage > 1 ? true : false)
            );
        endif;
        for ($i = 1; $i <= $this->totalPages; $i++):
            if ($i >= ($this->currentPage - $range) AND $i <= ($this->currentPage + $range)):
                $paginator[] = array($this->defaultPageNumberName => $i,
                    $this->defaultPaginationSegmentName => $i,
                    $this->defaultPaginationSegmentStatus => ($this->currentPage == $i ? false : true)
                );
            endif;
        endfor;
        if ($nextAndPreviousButtons):
            $paginator[] = array($this->defaultPageNumberName => ($this->currentPage < $this->totalPages ? ($this->currentPage + 1) : $this->totalPages),
                $this->defaultPaginationSegmentName => $this->defaultPaginationSegmentNameNext,
                $this->defaultPaginationSegmentStatus => ($this->currentPage < $this->totalPages ? true : false)
            );
        endif;
        if ($additionalButtons):
            $paginator[] = array($this->defaultPageNumberName => $this->totalPages,
                $this->defaultPaginationSegmentName => $this->defaultPaginationSegmentNameLast,
                $this->defaultPaginationSegmentStatus => ($this->currentPage < $this->totalPages ? true : false)
            );
        endif;
        return $paginator;
    }

    /**
     * determineAutoIncrement()
     * Determines Autoincrement Field if not set
     * @return void
     */
    private function determineAutoIncrement() {
        if (!$this->orderByField):
            $query = "DESCRIBE " . $this->table;
            $result = $this->db->query($query);
            foreach ($result as $row):
                if ($row['Extra'] == 'auto_increment'):
                    $this->orderByField = $row['Field'];
                endif;
            endforeach;
        endif;
    }

    /**
     * calculateTotal()
     * Calculate totlas
     * @return type
     */
    private function calculateTotal() {
        if (!$this->totalPages):
            $this->query = new Query;
            $this->query->select("COUNT(*) as total")
                    ->from($this->table);
            if (isset($this->where)):
                $this->query->where($this->where);
            endif;
            $result = $this->db->fetchRow($this->query->__toString());
            $this->totalCount = $result['total'];
            $this->totalPages = ceil($this->totalCount / $this->itemsPerPage);
        endif;
        return $this->totalPages;
    }

}

