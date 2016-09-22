<?php
/**
 * Enter description here...
 *
 * example：
 * <pre>
 *
 * </pre>
 *
 * @name QLib_Paging
 * @version 
 * @package 
 * @author peter.zyliu peter.zyliu@gmail.com
 * @since 1.0
 */
// // require_once 'Q/Paging.php';
class QLib_Paging extends Q_Paging {
	
	/**
	 * 路径
	 *
	 * @var String
	 */
	private $path = '';
	
	/**
	 * 查询参数
	 * String or Array
	 * @var mixed
	 */
	private $query;
	
         /**
	 * url格式
	 * String
	 * @var mixed
	 */
	private $urlformat;
        
	/**
	 * 分页集尺寸
	 *
	 * @var Integer
	 */
	private $pageSetSize = 5;
	
	/**
	 * 子串
	 *
	 * @var String
	 */
	private $substring = '?';
	
	/**
	 * 连接符号
	 *
	 * @var String
	 */
	private $sign = '=';
	
	/**
	 * 对符
	 *
	 * @var String
	 */
	private $pairs = '&';
	
	/**
	 * 模板路径
	 * 
	 * @var String
	 */
	private $templatePath = '';
	
	/**
	 * jsName 
	 * @var String
	 */
	private $jsFName = '';
	
	/**
	 * js函数参数
	 *
	 * @var String
	 */
	private $jsFParam = null;
	
	/**
	 * 废弃参数
	 * @var String
	 */
	private $disuse = null;
	
	/**
	 * 设置分页链接中的关键字
	 *
	 * @var String
	 */
	private $keyword = 'page';
	
	/**
	 * 是否开启rewrite url
	 *
	 * @var bool
	 */
	private $rewrite = false;
	
	/**
	 * 分页中代码当前页码的常量
	 *
	 */
	const PAGER_VARIABLE_STRING = "%{PAGE_NO}";
	
	/**
	 * 新的获取当前偏移
	 * @return Integer
	 */
	public function getNewCurrent() {
		if ($this->currentPage > 1) {
			return min($this->currentPage, $this->getPageNum());
		}
		$pageNo = !isset($_GET[$this->keyword]) ? 0 : (int) (intval($_GET[$this->keyword]) * $this->getSize()) - $this->getSize();
		return $pageNo < 0 ? 0 : $pageNo;
	}
	/**
	 * 重载当前页方法
	 * @return Integer
	 */
	public function getCurrent() {
		if ($this->currentPage > 1) {
			return min($this->currentPage, $this->getPageNum());
		}
		$pageNo = isset($_GET[$this->keyword]) ? (int) intval($_GET[$this->keyword]) : 1;
		if ($pageNo <= 0) {
			$pageNo = 1;
		}
		$this->currentPage = min($pageNo, $this->getPageNum());
		return $this->currentPage;
	}
	/**
	 * 设置当前页
	 * @param Integer $pageNo
	 * @return Q_Paging
	 */
	public function setCurrent( $pageNo ) {
		$cur = (int) intval($pageNo);
		if ($cur <= 0) {
			$cur = 1;
		}
		$this->currentPage = $cur;
		return $this;
	}
	/**
	 * 获取关键词
	 * @return String
	 */
	public function getKeyword() {
		return $this->keyword;
	}
	
	/**
	 * 设置关键词
	 *
	 * @param String $keyword
	 * @return QLib_Paging
	 */
	public function setKeyword($keyword) {
		if (!empty($keyword)) {
			$this->keyword = $keyword;
		}
		return $this;
	}
	
	/**
	 * 设置js名字
	 * @param String $name
	 * @return Q_Page_Abstract
	 */
	public function setFJs($name) {
		if (!empty($name)) {
			$this->jsFName = $name;
		}
		return $this;
	}
	
	/**
	 * 设置js函数参数
	 * @param int $param
	 * @return Q_Page_Abstract
	 */
	public function setFParam($param) {
		if (!empty($param)) {
			$this->jsFParam = $param;
		}
		return $this;
	}
	
	/**	
	 * 获取分页js名字
	 * @return String
	 *
	 */
	public function getFJs() {
		return $this->jsFName;
	}
	
	/**
	 * 获取分页js函数的参数
	 * @return String
	 *
	 */
	public function getFParam() {
		return $this->jsFParam;
	}
	
	/**
	 *
	 * 设置链接的路径
	 *
	 * @param String $path
	 * @return QLib_Paging
	 */
	public function setPath($path) {
		if (!empty($path)) {
			$this->path = trim($path);
		}
		return $this;
	}
	
	/**
	 * 设置连续Url
	 *
	 * @param bool $seo
	 * @return QLib_Paging
	 */
	public function rewrite($rw = true) {
		if ($rw == true) {
			$this->substring = '';
			$this->sign = '/';
			$this->pairs = '/';
			$this->rewrite = true;
		}
		return $this;
	}
	
	/**
	 * 取得程序路径
	 * @return String
	 */
	public function getPath() {
		return $this->path;
	}
	
	/**
	 * 设置分页集尺寸
	 *
	 * @param integer $num 大于1
	 * @return QLib_Paging
	 */
	public function setPageSetSize($num) {
		$this->pageSetSize = (int) intval($num);
		return $this;
	}
	
	/**
	 * 取得分页集尺寸
	 *
	 * @return integer
	 */
	public function getPageSetSize() {
		return $this->pageSetSize;
	}
	
	/**
	 * 获取查询参数
	 * @return String
	 */
	public function getQuery() {
		if (empty($this->query)) {
			$this->query = $this->autoUrl();
		}
		if (is_array($this->query) && count($this->query) > 0) {
			$_query = array();
			foreach ($this->query as $key => $value) {
				if ($key == $this->getKeyword()) {
					continue;
				}
				if (is_array($value)) {
					foreach ($value as $k => $val) {
						$_query[] = "{$key}[]" . $this->sign . $val;
					}
				}
				else {
					$_query[] = "{$key}" . $this->sign . $value;
				}
			}
			$this->query = $this->pairs . implode($this->pairs, $_query);
		}
		return $this->query ? $this->query : '';
	}
	
	/**
	 * 获取URL
	 * 
	 * @param Integer $pageNo
	 * @return String
	 */
	public function getUrl($pageNo) {
            if(!empty($this->urlformat)){
            return sprintf($this->urlformat,$pageNo);
        }
        
		$query = $this->getQuery();
		if (strstr($query, self::PAGER_VARIABLE_STRING) && is_string($query)) {
			$query = str_replace(self::PAGER_VARIABLE_STRING, $pageNo, $query);
		}
		else {
			if (empty($query)) {
				$query = $this->getKeyword() . $this->sign . $pageNo;
			}
			else {
				$query = $this->getKeyword() . $this->sign . $pageNo . $query;
			}
		}
		$url = $this->getPath() . $this->substring . $query;
		if ($this->getFJs()) {
			$url = $query;
		}
		return $url;
	}
	/**
     *
     * 设置链接的路径
     *
     * @param String $format
     * @return Paging
     */
    public function setUrlFormat($format) {
        if (!empty($format)) {
            $this->urlformat = trim($format);
        }
        return $this;
    }

    /**
     * 设置链接的路径
     * @return String
     */
    public function getUrlFormat() {
        return $this->urlformat;
    }

    /**
	 * 设置查询参数
	 *
	 * @param mixed $query String or Array
	 * @return QLib_Paging
	 */
	public function setQuery($query) {
		$this->query = $query;
		return $this;
	}
	
	/**
	 * 设置模板路径
	 * 
	 * @param String $path
	 * @return QLib_Paging
	 */
	public function setTemplate($path) {
		$this->templatePath = $path;
		return $this;
	}
	
	/**
	 * 输出模板
	 */
	public function view() {
		if ($this->getTotal() > 0) {
			include ($this->templatePath);
		}
	}
	
	/**
	 * 自动组织 URL
	 * @return Array
	 */
	private function autoUrl() {
		$queryOpt = Q_Common_Request::serverParam('REQUEST_URI');
		$queryArg = parse_url($queryOpt);
		$queryOpt = isset($queryArg['query']) ? explode('&', $queryArg['query']) : array();
		$query = array();
		foreach ($queryOpt as $key => $val) {
			$strTmp = explode('=', $val);
			if (empty($strTmp[0]) || $strTmp[1] == '' || $strTmp[0] == $this->getKeyword()) {
				continue;
			}
			if (is_array($this->disuse)) {
				if (in_array($strTmp[1], $this->disuse) || in_array($strTmp[0], $this->disuse)) {
					continue;
				}
			}
			else {
				if ($strTmp[1] == $this->disuse || $strTmp[0] == $this->disuse) {
					continue;
				}
			}
			$query[$strTmp[0]] = $strTmp[1];
		}
		return $query;
	}
	
	/**
	 * set废弃参数
	 * @param mixed $disuse
	 * @return QLib_Paging
	 */
	public function setDisuse($disuse = null) {
		$this->disuse = $disuse;
		return $this;
	}
}