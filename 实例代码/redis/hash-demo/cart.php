<?php

	namespace hash;

	/**
	 * Redis实现购物车
	 */
	class Cart
	{
		protected $redis;

		public function __construct()
		{
			$this->redis = new \Redis;
			$this->redis->connect('127.0.0.1', 6379);
		}

		//显示购物车页面
		public function showCartView()
		{
			session_start();

			$gIds = $this->redis->sMembers( 'cart:set:'.session_id() );
			

			for ($i=0; $i < count($gIds) ; $i++) { 
				

				$key = "cart:".session_id().':'.$gIds[$i];

				$cartData[] = $this->redis->hgetall($key);

			}


			include './view/cart.html';
		}

		/**
		 * addToCart 添加商品到购物车
		 * @param integer  $gId    商品ID
		 * @param integer $buyNum  购买数量
		 */
		public function addToCart($gId, $buyNum=1)
		{
			session_start();

			//根据商品ID查询数据库中的商品信息
			$goodData = $this->getGoodDataById($gId);

			$key = "cart:".session_id().":{$goodData['id']}";

			//判断Redis是否已经有商品,如果有则增加数量
			if ( $this->redis->hget($key, 'id') == false ) {

				//购买数量
				$goodData['num'] = $buyNum;
				$this->redis->hmset("cart:".session_id().":{$goodData['id']}", $goodData);

				//将商品ID存放到集合中
				$this->redis->sAdd('cart:set:'.session_id(), $gId);

            //有商品，改变数量
			} else {

				//先从redis中获取到数量
				$originNum = $this->redis->hget($key, 'num');

				$finalNum = $originNum + $buyNum;

				$this->redis->hset($key, 'num', $finalNum);

			}

			
		}

		/**
		* addToCart 添加商品到购物车
		* @param integer  $gId    商品ID
		* @param integer $buyNum  购买数量
		*/
/*		public function addToCart($gId, $buyNum=1)
		{
			session_start();
			//根据商品ID查询数据库中的商品信息
			$goodData = $this->getGoodDataById($gId);

			$key = "cart:".session_id();

			//判断Redis是否已经有商品,如果有则增加数量
			if ( $this->redis->hget($key, 'id-'.$gId) == false ) {

				//购买数量   
				$newGoodData['num-'.$gId] = $buyNum;
				$newGoodData['id-'.$gId] = $goodData['id'];
				// $newGoodData['goodData-'.$gId] = 
				$newGoodData['gname-'.$gId] = $goodData['gname'];
				$newGoodData['price-'.$gId] = $goodData['price'];
				$newGoodData['pic-'.$gId] = $goodData['pic'];
				
				$this->redis->hmset($key, $newGoodData);

			} else {

				//先从redis中获取到数量
				$originNum = $this->redis->hget($key, 'num-'.$gId);

				$finalNum = $originNum + $buyNum;

				$this->redis->hset($key, 'num-'.$gId, $finalNum);

			}

			
		}*/

		//根据商品ID获取商品信息
		protected function getGoodDataById($gId)
		{
			//模拟数据
			$goods = array(

				1 => array(
					'id' => 1,
					'gname' => '商品1',
					'price' => 12.2,
					'pic' => 'public/uploads/afds.jpg'
				),
				2 => array(
					'id' => 2,
					'gname' => '商品222',
					'price' => 122.2,
					'pic' => 'public/uploads/afds2.jpg'
				),
				3 => array(
					'id' => 3,
					'gname' => '商品33',
					'price' => 1233.2,
					'pic' => 'public/uploads/afds33.jpg'
				),
			);

			return $goods[$gId];

		}
	}


	$cart = new \hash\Cart;
	if ( @$_GET['gid'] ) {

		//从商品详情页，跳转过来会带着一个商品ID
		$cart->addToCart($_GET['gid'], $_GET['num']);
	} else {
		
		$cart->showCartView();
	}

