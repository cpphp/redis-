<?php

	namespace article;
	/**
	 * 文章列表功能
	 */
	class Article
	{

		//获得文章数据
		protected function articleList()
		{
			$mysqli = new \mysqli('localhost', 'root', '123', 'redis');

			$mysqli->set_charset('utf8');

			$sql = "select id,title,uid,num from article";

			$res = $mysqli->query($sql);

			if ($res && $mysqli->affected_rows > 0 ) {

				$list = [];

				while ( $row = $res->fetch_assoc() ) {
					
					$list[] = $row;
				}
			}

			return $list;
		}

		public function showArticleList()
		{
			$list = $this->articleList();

			// echo '<pre>';
			// print_r($list);
			include './view/list.html';
		}
	}

	$art = new \article\Article;
	$art->showArticleList();