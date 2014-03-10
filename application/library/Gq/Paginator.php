<?php
/**
* 分页助手
*/
class Gq_Paginator {
	private $_count;
	private $_currPage = 1;
	private $_perPage = 10;
	private $_pageRange = 5;
	private $_pageCount;
	private $_pages;

	/**
     * 初始化
     * @param $count 总数
     * @return $perPage 每页条数
     */
	function __construct($count, $perPage = 10)
	{
		$this->_count = $count;
		$this->_perPage = $perPage;
	}

	/**
     * 获取总页数
     *
     * @return integer
     */
	public function count(){
		return $this->_pageCount ? $this->_pageCount : $this->_calculatePageCount();
	}

	/**
     * 设置当前页
     *
     * @return void
     */
	public function setCurrentPageNumber($pageNumber){
		$this->_currPage = $pageNumber;
	}

	/**
     * 设置当前页
     *
     * @return void
     */
	public function getPages(){
		if ($this->_pages === null) {
            $this->_pages = $this->_createPages();
        }

        return $this->_pages;
	}

	/**
     * 计算总页数
     *
     * @return integer
     */
    protected function _calculatePageCount()
    {
        return (integer) ceil($this->_count / $this->_perPage);
    }

    /**
     * 获取分页区间
     *
     * @return array pages
     */
    protected function _getPagesInRanges()
    {
    	$pageNumber = $this->_currPage;
    	$pageCount = $this->count();
    	$pageRange = $this->_pageRange;

        if ($pageRange > $pageCount) {
            $pageRange = $pageCount;
        }

        $delta = ceil($pageRange / 2);

        if ($pageNumber - $delta > $pageCount - $pageRange) {
            $lowerBound = $pageCount - $pageRange + 1;
            $upperBound = $pageCount;
        } else {
            if ($pageNumber - $delta < 0) {
                $delta = $pageNumber;
            }

            $offset     = $pageNumber - $delta;
            $lowerBound = $offset + 1;
            $upperBound = $offset + $pageRange;
        }

        $lowerBound = $this->normalizePageNumber($lowerBound);
        $upperBound = $this->normalizePageNumber($upperBound);

        $pages = array();

        for ($pageNumber = $lowerBound; $pageNumber <= $upperBound; $pageNumber++) {
            $pages[$pageNumber] = $pageNumber;
        }

        return $pages;
    }

    /**
     * Brings the page number in range of the paginator.
     *
     * @param  integer $pageNumber
     * @return integer
     */
    public function normalizePageNumber($pageNumber)
    {
        $pageNumber = (integer) $pageNumber;

        if ($pageNumber < 1) {
            $pageNumber = 1;
        }

        $pageCount = $this->count();

        if ($pageCount > 0 && $pageNumber > $pageCount) {
            $pageNumber = $pageCount;
        }

        return $pageNumber;
    }

	private function _createPages()
    {
        $pageCount         = $this->count();
        $currentPageNumber = $this->_currPage;

        $pages = new stdClass();
        $pages->pageCount        = $pageCount;
        $pages->itemCountPerPage = $this->_perPage;
        $pages->first            = 1;
        $pages->current          = $currentPageNumber;
        $pages->last             = $pageCount;

        // Previous and next
        if ($currentPageNumber - 1 > 0) {
            $pages->previous = $currentPageNumber - 1;
        }

        if ($currentPageNumber + 1 <= $pageCount) {
            $pages->next = $currentPageNumber + 1;
        }

        // Pages in range
        $pages->pagesInRange = $this->_getPagesInRanges();
        $pages->firstPageInRange = min($pages->pagesInRange);
        $pages->lastPageInRange  = max($pages->pagesInRange);

        return $pages;
    }

}