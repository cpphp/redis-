<?php

	namespace demo2;
	/**
	 * 文章发布功能
	 */
	
	class Post
	{

		//显示发表文章页面
		public function showPublishView()
		{
			include './view/publish.html';
		}

		public function storeDataToRedis()
		{
			require_once('./redis.php');

			$redis = new \demo2\RedisHandler();

			$redis->storeData('article', json_encode($_POST) );
		}

		//文章列表功能
		public function articleList()
		{
			require_once('./redis.php');

			$redis = new \demo2\RedisHandler();

			$id = $redis->getData('artId');

			for ($i=1; $i <= $id; $i++) { 
				$list[] = json_decode( $redis->getData("article:$i:data"), true);
			}
			
			include './view/list.html';
		}
	}

	$post = new \demo2\Post;	

	if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
		
		if ( @$_GET['a'] =='list' ) {

			$post->articleList();
		} else {

			$post->showPublishView();
		}

		

	} else if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

		$post->storeDataToRedis();

	} else {
		echo '支持GET/POST方式';
	}

