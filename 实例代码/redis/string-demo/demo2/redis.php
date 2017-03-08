<?php

	namespace demo2;

	class RedisHandler
	{
		protected $redis;

		public function __construct()
		{
			$this->redis = new \Redis;

			$this->redis->connect('127.0.0.1', 6379);
		}

		//存放数据到redis中
		public function storeData($key='article', $data)
		{
			$artId = $this->redis->incr('artId');

			$res = $this->redis->set($key.':'.$artId.':data', $data);
			
			return $res;
		}

		public function getData($key)
		{
			return $this->redis->get($key);
		}
	}