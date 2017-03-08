<?php


  //实现注册功能，数据库是redis
  

  //这两个数据都是用户提交的
  $name = 'jack';

  $pass = md5('123456');

  $redis = new Redis();

  $redis->connect('localhost', 6379);
 
  //模拟自增ID
  $uid = $redis->incr('user:id');//incr num

  $arr = array(

    'id'   => $uid,
    'name' => $name,
    'pass' => $pass     
  );

  /*$redis->set("user:{$uid}:data", json_encode($arr));
  $redis->set("user:{$name}", json_encode($arr));
  */

  //redis难点不在语法，命名多。难点在于如何设计键，如何存放
  $redis->set("user:{$uid}:data", json_encode($arr));
  $redis->hset("user:name", "{$name}", "{$uid}");
