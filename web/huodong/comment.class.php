<?php
class comment {
	var $db = '';
	var $_comment_table = '';
	function comment()
    {
		global $db;
		$this->db = $db;
		$this->_comment_table = DB_PRE.'comment';
		$this->count_table = DB_PRE.'content_count';
	}

	/**
	 *
	 *	@params
	 *	@return
	 */
	 function get_list($keyid, $page = 1, $pagesize, $t=0)
     {
        global $MODULE,$db;
		$keyid = trim($keyid);
		$page = max(intval($page), 1);
        $offset = $pagesize*($page-1);
		$comments = array();
        //$count = cache_count("SELECT COUNT(*) AS `count` FROM `$this->_comment_table` WHERE `keyid`='{$keyid}' AND `status`='1'");
        $r = $db->get_one("SELECT COUNT(*) AS `count` FROM `$this->_comment_table` WHERE `keyid`='{$keyid}' AND `status`='1'");
        $count = $r[count];
		$comments['pages'] = pages($count, $page, $pagesize);
		$ip_area = load('ip_area.class.php');
		if(!$t)
			$limit = " limit $offset,$pagesize";
		$result = $this->db->query("SELECT `commentid`,`username`,`support`,`against`,`ip`,`addtime`,`status`,`content`, `userid` FROM `$this->_comment_table` WHERE `keyid` = '{$keyid}' AND `status` = '1' ORDER BY `addtime` DESC $limit");
		while($r = $this->db->fetch_array($result))
        {
			//$r['content'] = preg_replace_callback("/\[smile_[0-9]{1,3}\]/", 'smilecallback', $r['content']);
			$r['content'] = str_replace( '[quote]', '<div class="reply">', $r['content']);
			$r['content'] = str_replace( '[blue]', '<div href="#" class ="blue"><p>', $r['content']);
			$r['content'] = str_replace( '[/quote]', '</div>', $r['content']);
			$r['content'] = str_replace( '[/blue]', '</p></div>', $r['content']);
			$r['addtime'] = date('Y-m-d H:m:s',$r['addtime']);
            list($r['ip_area'], ) = explode(' ', $ip_area->get($r['ip']));
            $r['ip_area'] = $r['ip_area'].'网友';
			$r['ip'] = preg_replace("/^([0-9]{1,3}\.[0-9]{1,3})\.[0-9]{1,3}\.[0-9]{1,3}$/", "\\1.*.*", $r['ip']);
            //$r['ip'] = $r['ip'].'：';
            $userid = $r['userid'];
            if(!$r['userid'])
            {
                $r['url'] = '：'.$r['username'];
            }
            else
            {
                $r['url'] = '：'.'<a href='.$MODULE['member']['url'].'view.php?userid='.$r['userid'].'>'.$r['username'].'</a>';
            }
            $comments['info'][] = $r;
		}

		return $comments;
	 }

	 /**
	  *
	  *	@params
	  *	@return
	  */
	  function ajaxupdate( $field, $id )
      {

          $sql = "UPDATE $this->_comment_table SET {$field} = {$field} +1 WHERE `commentid` = '$id' ";
		  $this->db->query($sql);
		  $sql2 = "SELECT {$field} FROM $this->_comment_table WHERE `commentid` = '$id' ";
		  return $this->db->get_one($sql2);
	  }

	  function ajaxpost()
      {
           global $_userid, $_username ;
            $contentid = $contentid;
            $module = $module;
            $userid = trim($_userid);
            $username = trim($_username);
            $content = new_htmlspecialchars($content);
            $ip = IP;
            $addtime = TIME;
            $status = '1';
            $sql = "INSERT INTO `$this->_comment_table` (`contentid`, `module`, `userid` , `username`, `content`, `ip`, `addtime`, `status`) VALUES ('$contentid', '$module', '$userid' , '$username', '$content', '$ip', '$addtime', '$status')";

            $sql2 = "SELECT `commentid`, `username`, `support`, `against`, `ip`, `addtime`, `status`, `content` FROM `$this->_comment_table` WHERE `contentid` = '1' AND `status` = '1' ORDER BY `addtime` DESC";
            $result = $this->db->query($sql2);
            $comments = array();
            $ip_area = load('ip_area.class.php');
            $txt = '';
            while($r = $this->db->fetch_array($result))
            {
                $r['content'] = preg_replace_callback("/\[smile_[0-9]{1,3}\]/", 'smilecallback', $r['content']);
                $r['content'] = str_replace( '[quote]', '<div class="reply">', $r['content']);
                $r['content'] = str_replace( '[blue]', '<a href="#" class ="blue">', $r['content']);
                $r['content'] = str_replace( '[/quote]', '</div>', $r['content']);
                $r['content'] = str_replace( '[/blue]', '</a>', $r['content']);

                $r['addtime'] = date('Y-m-d H-m-s',$r['addtime']);
                $r['ip_area'] =  $ip_area->get($r['ip']);

                $txt .= "<div >\n";
                $txt .= $r['content'];
                $txt .= "</div>\n";
            }
            return $txt;
	  }

	  function addpost($content, $keyid, $niming='')
	  {
		  global $_userid, $_username ;
		  $addtime = TIME;
		  if(empty($_username)) $username = '游客'; else $username = $_username;
		  if($niming){$username='匿名游客';$_userid=0;}
		  $ip = IP;
		  $setting = cache_read('module_comment.php');
		  if($setting['ischeckcomment'])
		  {
			  $status = '0';
		  }
		  else
		  {
			  $status = '1';
		  }
		  if(strpos($keyid,'18')) $status = 0; //这个是温暖中国行专用
		  
		  // 李军锋 修改 为了不用更新 phpcms_content_count 表
		  $sql = "INSERT INTO $this->_comment_table (`keyid`, `userid` , `username`,`content`,`ip`,`addtime`,`status`) VALUES ('$keyid', '$_userid' , '$username','$content','$ip','$addtime','$status')";
		  return $this->db->query($sql);
			  
		  /*if($this->updatecounter($keyid, $status))
          {
			  $sql = "INSERT INTO $this->_comment_table (`keyid`, `userid` , `username`,`content`,`ip`,`addtime`,`status`) VALUES ('$keyid', '$_userid' , '$username','$content','$ip','$addtime','$status')";
			  return $this->db->query($sql);
		  }*/
	  }
	  /**
	   *	回复网友的帖子
	   *	@params
	   *	@return
	   */

	  function add($commentid,$content, $keyid)
      {
		  global $_username, $_userid;
		  $commentid = trim($commentid);
		  $content = new_htmlspecialchars($content);
		  if (empty($_username)) $data['username'] = '游客'; else $data['username'] = $_username;
		  $r = $this->db->get_one("SELECT `content` FROM $this->_comment_table WHERE `commentid` = '$commentid' ");
		  $data['content'] = '[quote][blue]引用网友'.'('.$data['username'].')'.'的帖子[/blue]'.$r['content'].'[/quote]'.$content;
		  $data['userid'] = $_userid;
		  $data['ip'] = IP;
		  $data['addtime'] = TIME;
		  $data['keyid'] = $keyid;
		  $setting = cache_read('module_comment.php');
		  if($setting['ischeckcomment'])
		  {
			  $data['status'] = '0';
		  }
		  else
		  {
			  $data['status'] = '1';
		  }
		  if($this->updatecounter($keyid, $status))
		  {
			return $this->db->insert($this->_comment_table, $data);
		  }
	  }
	  //  0 不需要
	  function updatecounter($keyid, $mark=1)
	  {
		  list($module, $tablename, $titlefield, $contentid) = explode('-', $keyid);
		  if('special' != $module)
          {
              if ($mark)
              {
                  if ($this->db->query("UPDATE `$this->count_table` SET `comments` = comments + '1' WHERE `contentid` = '$contentid'"))
                  {
                      return $this->db->query("UPDATE `$this->count_table` SET `comments_checked` = comments_checked + '1' WHERE `contentid` = '$contentid'");
                  }
              }
              else
              {
                  return $this->db->query("UPDATE `$this->count_table` SET `comments` = comments + '1' WHERE `contentid` = '$contentid'");
              }
          }
          return true;
	  }
	  
	  
	  function create_right_comment()
	  {
	  		$db = $this->db;
	  		$rightComent = "../templates/9939/comment/right_ifram_comment.html";
			//$filename = "../templates/9939/comment/right_ifram_time.html";
			$sTime = file_exists($rightComent) ? filemtime($rightComent) : 0 ;
			if(date("Ymd",$sTime)!=date("Ymd"))
			{
				$str = '
					<div class="w238 wrap">
						<div class="t_01">每周热评</div>
						<div class="listbox">
						<ul>';
				
				$RES = $db->query("select count(*)as count,keyid from phpcms_comment group by `keyid` order by count desc limit 0,10");
				while($R = $db->fetch_array($RES))
				{
					$newcontentid = explode('-',$R[keyid]);
					$newcontentid=$newcontentid[3];
					$res = $db->query("select title, url from Article where articleid='$newcontentid' and title!='' order by articleid desc");
					while($r = $db->fetch_array($res))
					{
						$str .= '<li><a href="'.$r[url].'" target="_blank" >'.str_cut($r[title], 24).'</a><span>['.$R[count].']</span></li>';
					}
				}
				$str .= '</ul>
						</div>
					</div>
					<div class="w238 wrap fix02">
						<div class="t_01">每月热评</div>
						<div class="listbox">
						<ul>';
				$RES = $db->query("select count(*)as count,keyid from phpcms_comment group by `keyid` order by count desc limit 10,10");
				while($R = $db->fetch_array($RES))
				{
					$newcontentid = explode('-',$R[keyid]);
					$newcontentid=$newcontentid[3];
					$res = $db->query("select title, url from Article where articleid='$newcontentid' and title!='' order by articleid desc");
					while($r = $db->fetch_array($res))
					{
						$str .= '<li><a href="'.$r[url].'" target="_blank" >'.str_cut($r[title], 24).'</a><span>['.$R[count].']</span></li>';
					}
				}
				$str .= '</ul>
						</div>
					</div>
					<div class="w238 wrap fix02">
						<div class="t_01">热点导读</div>
						<div class="listbox">
						<ul>';
				$res = $db->query("select a.title, a.url, b.hits_week from Article a, article_count b where a.articleid=b.articleid and a.title!='' order by b.hits_week desc limit 0,10");
				while($r = $db->fetch_array($res))
				{
					$str .= '<li><a href="'.$r[url].'" target="_blank" >'.str_cut($r[title], 24).'</a><span>['.$r[hits_week].']</span></li>';
				}
				$str .='</ul>
						</div>
					</div>';
				$str .= '<style>*{background:#fff;}.w240{width:240px;}.w238{width:238px;}.right{float:right;}.wrap{border:1px solid #B0D4EC;}.t_01{background:#3B78AE; height:25px; line-height:25px;text-align:center; font-weight:bold; color:#FFF;}body{font-family:"宋体",Arial; font-size:12px;}a{text-decoration:none; color:#333;}
a:hover{text-decoration:none; color:#F60;}li{margin-top:7px;}</style>';
				/*
				$b = array(		
						'href="jf/'=>'href="http://fitness.9939.com/',
						'href="drug/'=>'href="http://drug.9939.com/',
						'href="tijian/'=>'href="http://tijian.9939.com/',
						'href="zy/'=>'href="http://zhongyi.9939.com/',
						'href="pf/'=>'href="http://pianfang.9939.com/',
						'href="jijiu/'=>'href="http://jijiu.9939.com/',
						'href="baby/'=>'href="http://baby.9939.com/',
						'href="xa/'=>'href="http://sex.9939.com/',
						'href="xinli/'=>'href="http://xinli.9939.com/',
						'href="bj/'=>'href="http://baojian.9939.com/',
						'href="ys/'=>'href="http://food.9939.com/',
						'href="jktp/'=>'href="http://picture.9939.com/',
						'href="male/'=>'href="http://man.9939.com/',
						'href="female/'=>'href="http://lady.9939.com/',
						'href="meirong/'=>'href="http://beauty.9939.com/',
						'href="js/'=>'href="http://js.9939.com/',
						'href="video/'=>'href="http://video.9939.com/',
						'href="news/'=>'href="http://news.9939.com/',
						'href="huli/'=>'href="http://nurse.9939.com/'
					);*/
					$b = array(		
						'href="jf/'=>'href="http://fitness.9939.com/',
						'href="drug/'=>'href="http://drug.9939.com/',
						'href="tijian/'=>'href="http://tijian.9939.com/',
						'href="zy/'=>'href="http://zhongyi.9939.com/',
						'href="pf/'=>'href="http://pianfang.9939.com/',
						'href="jijiu/'=>'href="http://jijiu.9939.com/',
						'href="baby/'=>'href="http://baby.9939.com/',
						'href="xa/'=>'href="http://sex.9939.com/',
						'href="xinli/'=>'href="http://xinli.9939.com/',
						'href="bj/'=>'href="http://baojian.9939.com/',
						'href="ys/'=>'href="http://food.9939.com/',
						'href="jktp/'=>'href="http://picture.9939.com/',
						'href="male/'=>'href="http://man.9939.com/',
						'href="female/'=>'href="http://lady.9939.com/',
						'href="meirong/'=>'href="http://beauty.9939.com/',
						'href="js/'=>'href="http://js.9939.com/',
						'href="video/'=>'href="http://video.9939.com/',
						'href="news/'=>'href="http://news.9939.com/',
						'href="huli/'=>'href="http://nurse.9939.com/', 
						'href="bdfzt/'=>'href="http://bdf.9939.com/',
						'href="shenbingzt/'=>'href="http://shcare.9939.com/',
						'href="tnbzt/'=>'href="http://tnb.9939.com/',
						'href="fukezt/'=>'href="http://fk.9939.com/',
						'href="ganbingzt/'=>'href="http://gb.9939.com/',
						'href="jingzbzt/'=>'href="http://jzb.9939.com/',
						'href="smyyzt/'=>'href="http://smyy.9939.com/',
						'href="weibingzt/'=>'href="http://wb.9939.com/',
						'href="xinzangbingzt/'=>'href="http://xzb.9939.com/',
						'href="dianxianzt/'=>'href="http://dx.9939.com/',
						'href="yaojanpanzt/'=>'href="http://yjp.9939.com',
						'href="bybyzt/'=>'href="http://byby.9939.com/'
					);
				foreach($b as $k=>$v)
				{
					if(strpos($str,$k) !== false)
					{
						$str = str_replace($k,$v,$str);
					}
				}
				
				@file_put_contents($rightComent, $str);
				//file_put_contents($filename, date("Ymd"));
				
			}
	  }
	  
	  
}
?>