<?php
include 'Upload.php';
include 'wechatCallbackapiTest.php';
include 'mysql.php';
header("Content-type: text/html; charset=utf-8");
$weixin=new wechatCallbackapiTest();
$access_token=$weixin->getWxAccessToken();
$upload=new Upload();


//上传文件的类型
$type=$_POST['type'];
$upload->up('file');
$path=$upload->path;

if($type=='图片'){
	$url="https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$access_token."&type=image";
	$type='image';

}elseif($type=='音频'){
	$url="https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$access_token."&type=voice";
	$type='voice';

}else if($type=='视频'){
	$url="https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$access_token."&type=video";
	$type='video';
}
$path=$upload->path;
if (class_exists('\CURLFile')) {  
    $data = array('media' => new \CURLFile(realpath($path)));              
} else {  
    $data = array('media' => '@' . realpath($path));  
} 
$curl=new Curl();
$json=$curl->post($url,$data);
$arr=json_decode($json,true);
$media = $arr['media_id'];
$link=dbConnect();
if($media!=null){
	$time=time();
	$sql="insert matter(type,media_id,path,time) values('$type','$media','$path',$time)";
	$result=mysqli_query($link,$sql);
	if($result){
		exit("<script>alert('上传成功');window.location.href='admin.php'</script>");
	}else{
		exit("<script>alert('上传失败');window.location.href='admin.php'</script>");
	}
}else{
	exit("<script>alert('上传失败');window.location.href='admin.php'</script>");
}