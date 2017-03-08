<?php


  $redis  = new Redis();

  $redis->connect('127.0.0.1', 6379);

  //使用hash的一些命令 
  //$redis->hset('bb', 'age', 100); 
 // $redis->hset('bb', 'size', 10); 
  $redis->hmset('bb', array('name'=>123,'aa'=>3456)); 

