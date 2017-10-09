<?php
function dbConnect(){
    $link=mysqli_connect('localhost','root','root');
    if(!$link){
        exit('数据库连接失败');
    }else{
    
    }
    mysqli_set_charset($link,'utf8');
    mysqli_select_db($link,'weixin');

    return $link;
}
function insert($user){
        $link=dbConnect();
        $openid=$user->openid;
        $nickname=$user->nickname;
        $subscribe=$user->subscribe;
        $subscribetime=$user->subscribe_time;
        $sex=$user->sex;
        $city=$user->city;
        $province=$user->province;
        $headimageurl=$user->headimgurl;
        $country=$user->country;
        $groupid=0;
          $sql='insert into user(openid,groupid,nickname,subscribe,subscribetime,sex,country,city,province,headimageurl) values('."'$openid'".','."'$groupid'".','."'$nickname'".','."'$subscribe'".','."'$subscribetime'".','."'$sex'".','."'$country'".','."'$city'".','."'$province'".','."'$headimageurl'".')';
        //echo $sql;
        $res= mysqli_query($link,$sql);
}