<?php
include 'wechatCallbackapiTest.php';

/**
  * wechat php
  */
// //my token
define("TOKEN", "wodeyirennizainali");
$wechatObj = new wechatCallbackapiTest();//微信写的类
//$wechatObj->valid();
//var_dump($wechatObj->getWxAccessToken());
//$wechatObj->createMenu();
$wechatObj->responseMsg();
