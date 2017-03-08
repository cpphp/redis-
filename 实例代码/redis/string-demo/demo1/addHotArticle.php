<?php

	/**
	 * 因为热门文章的阅读量相对大，如果每次都从MySQL中读取，这会对MySQL
	 * 造成一定的压力。所以将热门文章存放到Redis中，之后读取直接从Redis中拿取，并且增加文章的阅读量。
	 */
	
	namespace article;

	class RedisOprator
	{
		protected $redis;

		public function __construct()
		{

			//实例化Redis类
			$this->redis = new \Redis;
			$this->redis->connect('127.0.0.1', 6379);
		}

		/**
		 * storeHotArticleData      存放热门文章到Redis
		 * @param  array  $data     从MySQL中查询出来的文章数据
		 * @param  int    $artId    文章ID
		 * @param  int    $viewNum  文章阅读量 
		 * @return boolean  存放成功返回true
		 */
		public function storeHotArticleData($data, $artId, $viewNum)
		{
			if ( !is_array($data) ) {
				throw new \Exception('第一个参数必须是数组');
				return;
			}


			//将文章阅读量存放到redis中
			//article:{$artId}:view   article:文章ID:view
			$this->redis->setex("article:{$artId}:view", 15, $viewNum);

			$tmpStr = serialize($data);

			$this->redis->setex("article:{$artId}:data", 15, $tmpStr);

			return true;
		}


		/**
		 * getHostArticleData 获取热门文章数据
		 * @param  int $artId 文章ID
		 * @return array       文章数据以及文章阅读量
		 */
		public function getHostArticleData($artId)
		{

			$postData = $this->redis->get("article:{$artId}:data");
			$viewNum  = $this->redis->get("article:{$artId}:view");

			//没有数据
			if (!$postData) {

				return array();
			}


			return array(

				'postData' => $postData,
				'viewNum'  => $viewNum
			);
		}

		//增加文章阅读量
		public function incrArticleNum($artId)
		{
			$this->redis->incr("article:{$artId}:view");
		}

	}