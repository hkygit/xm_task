<?php
namespace Home\Service;

class PageService {
	public $currentPage;
	public $controller;
	public $method;
	public $query;
	public $nums;
	public $firsturl;
	public $preurl;
	public $pageCount;
	public $nexturl;
	public $lasturl;
	public $pageSize;
	public $formContent;
	public $formCondition;
	public $pagename="page";

	function __construct() {}

	/**
     +----------------------------------------------------------
     * 设置分页基本数据
     +----------------------------------------------------------
     * @param array $currentPage 当前页码
     +----------------------------------------------------------
	 * @param array $controller 分页所在控制器名称
     +----------------------------------------------------------
	 * @param array $method 分页所在方法名
     +----------------------------------------------------------
	 * @param array $nums 要分页的数据总条数
     +----------------------------------------------------------
	 * @param array $formContent 列表显示数据的容器ID
     +----------------------------------------------------------
	 * @param array $formCondition 列表搜索条件form表单id
     +----------------------------------------------------------
	 * @param array||string $params 分页要传入url参数
     +----------------------------------------------------------
	 * @param array $pageSize 分页每页数量
     +----------------------------------------------------------
     */
	public function setPageConfig($currentPage, $controller, $method, $nums, $formContent, $formCondition, $params = array(), $pageSize = 10, $pagename="page") {
		$this->controller = $controller;
		$this->method = $method;

		$query = array();
		if(!empty($params)) {
			!is_array($params) ? parse_str($params, $query) : $query = $params;
		}

		$session = session(C('USER_AUTH_KEY'));
		$uid = isset($session['uid']) ? $session['uid'] : 0;
		$guid = to_guid_string($uid.'_'.$controller.'_'.$method);
		$setPerPageNum = I('get.setPerPageNum', 0, 'intval');
		$cookieSetPernum = cookie(C('PAGE_SET_PERNUM_COOKIE'));
		if($setPerPageNum > 0) {
			$pageSize = $setPerPageNum;
			$cookieSetPernum[$guid] = $pageSize;
			cookie(C('PAGE_SET_PERNUM_COOKIE'), $cookieSetPernum, 3600*24*365);
		} else {
			$setPerPageNum = isset($cookieSetPernum[$guid]) ? (int)$cookieSetPernum[$guid] : 0;
			if($setPerPageNum > 0) {
				$pageSize = $setPerPageNum;
			}
		}

		$this->nums = $nums;
		$this->query = $query;
		$this->pageSize = $pageSize;
		$this->pageCount = ceil ( $nums / $pageSize );

		if($currentPage > $this->pageCount) {
			$currentPage = $this->pageCount;
		}
		if($currentPage < 1) {
			$currentPage = 1;
		}

		$this->currentPage = $currentPage;
		$this->formContent = $formContent;
		$this->formCondition = $formCondition;
		$this->getFirstUrl();
		$this->getPreUrl();
		$this->getNextUrl();
		$this->getLastUrl();
	}

	public function getFirstUrl() {
		$this->firsturl = $this->createUrl();
	}

	public function getPreUrl() {
		$currentPage = $this->currentPage;
		if ($currentPage == 1) {
			$this->preurl = $this->createUrl();
		} else {
			$prepage = ($currentPage - 1);
			$this->preurl = $this->createUrl(array($this->pagename => $prepage));
		}
	}

	public function getNextUrl() {
		$currentPage = $this->currentPage;
		$pagenum = $this->pageCount;
		if($currentPage == $pagenum) {
			$this->nexturl = $this->createUrl(array($this->pagename => $currentPage));
		} else {
			$nextpage = ($currentPage + 1);
			$this->nexturl = $this->createUrl(array($this->pagename => $nextpage));
		}
	}

	public function getLastUrl() {
		$pagenum = $this->pageCount;
		$this->lasturl = $this->createUrl(array($this->pagename => $pagenum));
	}

	public function setPages() {
		return array (
			'currentPage'		=>	$this->currentPage,
			'formContent'		=>	$this->formContent,
			'formCondition'		=>	$this->formCondition,
			'firsturl'			=>	$this->firsturl,
			'preurl'			=>	$this->preurl,
			'pageCount'			=>	$this->pageCount,
			'pageCount'			=>	$this->pageCount,
			'nexturl'			=>	$this->nexturl,
			'lasturl'			=>	$this->lasturl,
			'currentPageUrl'	=>	$this->createUrl(array($this->pagename => $this->currentPage)),
			'locationNumUrl'	=>	$this->createUrl(array($this->pagename => 'replacelocationNum')),
			'setPerPageNumUrl'	=>	$this->createUrl(array('setPerPageNum' => 'perPageNumValue')),
			'pageSize'			=>	$this->pageSize,
			'startNum'			=>	($this->currentPage-1)*$this->pageSize,
			'nums'				=>	$this->nums
		);
	}

	private function createUrl($params = array()) {
		$params = !empty($params) ? array_merge($this->query, $params) : $this->query;

		return U($this->controller.'/'.$this->method, $params);
	}
}
?>