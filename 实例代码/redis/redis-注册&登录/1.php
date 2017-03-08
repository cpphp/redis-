<?php


   $redis = new Redis();

   $redis->connect('127.0.0.1', 6379);

   //将rose值存放到name这个键中
  $redis->set('name', 'rose');


   echo $redis->get('name');
