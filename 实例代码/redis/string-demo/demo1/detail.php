<?php

	namespace article;
	/**
	 * 文章列表功能
	 */
	class Detail
	{

		//查询文章详情数据
		public function articleDetail($artId)
		{
			
			include './addHotArticle.php';
	
			//先查询Redis中有无该文章数据,如果没有就查询MySQL
			$redisOp = new RedisOprator();

			$list = $redisOp->getHostArticleData($artId);

			//Redis中没有数据，查询数据得到文章内容
			if ( !$list ) {

						
				$mysqli = new \mysqli('localhost', 'root', '123', 'redis');

				$mysqli->set_charset('utf8');

				$sql = "select a.id,aid,content,num from article_detail d,article a where a.id=d.aid and d.aid={$artId}";
				
				$res = $mysqli->query($sql);

				$content = $res->fetch_assoc();

				//增加文章阅读量
				$this->addArticleNum($artId);

				//判断文章是否达到1000,1000就是热门文章
				$articleNum = $this->getArticleNum($artId);

				if ($articleNum >= 1000) {

					//热门文章存放到redis中
					$redisOp->storeHotArticleData($content, $artId, $articleNum);
				}


				return $content;

			} else {

				$redisOp->incrArticleNum($artId);

				
				return $list;
			} 

		}

        //文章详情显示页
		public function showDetail($artId)
		{
			$content = $this->articleDetail($artId);

			if ( $content['postData'] ) {

				$tmp = unserialize($content['postData']);

				$content['id'] = $tmp['id'];
				$content['content'] = $tmp['content'];
				$content['aid'] = $tmp['aid'];
				$content['num'] = $content['viewNum'];
				unset($content['viewNum'], $content['postData']);
			}

			include './view/detail.html';
		}

		//增加文章的阅读量
		public function addArticleNum($artId)
		{
			$mysqli = new \mysqli('localhost', 'root', '123', 'redis');

			$mysqli->set_charset('utf8');

			$sql = "update article set num=num+1 where id = {$artId}";

			$res = $mysqli->query($sql);

			return $mysqli->affected_rows;
		}

		//获取文章阅读量
		protected function getArticleNum($artId)
		{
			$mysqli = new \mysqli('localhost', 'root', '123', 'redis');

			$mysqli->set_charset('utf8');

			$sql = "select num from article where id = {$artId}";

			$res = $mysqli->query($sql);	

			$num = $res->fetch_assoc();

			return $num['num'];
		}
		
	}

	// error_reporting(0);
	$detail = new \article\Detail;

	$detail->showDetail($_GET['id']);
