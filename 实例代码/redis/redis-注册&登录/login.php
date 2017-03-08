<?php



 //这个文件研究如何登录的
  $redis = new Redis();

  $redis->connect('127.0.0.1', 6379);

  //登录的时候：用户是传递一个用户名过来的,所以我们可以根据用户名得到用户注册时添加的数据
  $name = 'jack';

  //这个也是用户提交过来的密码
  $pass = md5('123456');

  $userData = $redis->get("user:{$name}");

  echo '<pre>';

  print_r($userData);

  $arr = json_decode($userData, true);

  if ($arr['pass'] == $pass) {

     echo 'login success';
  } else {
     echo 'login fail';
  } 
