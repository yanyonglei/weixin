<?php

include 'tpl.php';
$title='后台信息列表';
$link=mysqli_connect('localhost','root','root');
    if(!$link){
        exit('数据库连接失败');
    }
mysqli_set_charset($link,'utf8');
mysqli_select_db($link,'weixin');

$sql='select * from user';
$res=mysqli_query($link,$sql);
if ($res && mysqli_affected_rows($link)) {
		while($rows=mysqli_fetch_assoc($res)){
			$user[]=$rows;
		}	
}


$sql='select * from matter';
$res=mysqli_query($link,$sql);
$matter=[];
if ($res && mysqli_affected_rows($link)) {
    while($rows=mysqli_fetch_assoc($res)){
         $matter[]=$rows;
    }   
}
display('admin.html',compact('title','user','matter'));


