<?php
session_start();
include 'Curl.php';
define("TOKEN", "wodeyirennizainali");
class wechatCallbackapiTest
{
   /*
    * 判断是否验证成功
    * 验证成功就将验证信息返回
    */
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
    {
    	//获取access_token的值
    	$access_token=$this->getWxAccessToken();

		//get post data, May be due to the different environments
		//$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		
    	$postStr = file_get_contents('php://input');
      	//extract post data
		if (!empty($postStr)){
                libxml_disable_entity_loader(true);
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;

                $keyword = trim($postObj->Content);
                //事件
                $event=$postObj->Event;
                //发送内容的类型
                $type=$postObj->MsgType;
                $time = time();
                //准备回复消息的xml文档模版
               
				//发送的是图片事件标示

				if($type=='image'){
					
					$msgType = 'image';
					$imageTpl=" <xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <Image>
                    <MediaId><![CDATA[%s]]></MediaId>
                    </Image>
                    </xml>";

             		$media_id=$this->addImg();
					$resultStr = sprintf($imageTpl, $fromUsername, $toUsername, $time, $msgType, $media_id);
					//$resultStr = sprintf($imageTpl, $fromUsername, $toUsername, $time, $msgType,$id);
					echo $resultStr;
				}
				//处理语音
				if($type=='voice'){
					$msgType='voice';
					$voiceTpl=" <xml>
	                    <ToUserName><![CDATA[%s]]></ToUserName>
	                    <FromUserName><![CDATA[%s]]></FromUserName>
	                    <CreateTime>%s</CreateTime>
	                    <MsgType><![CDATA[%s]]></MsgType>
						<Voice>
						<MediaId><![CDATA[%s]]></MediaId>
						</Voice>
						</xml>";
					$media_id=$this->addVoice();
					$resultStr = sprintf($voiceTpl, $fromUsername, $toUsername, $time, $msgType,$media_id);
              		echo $resultStr;
				}
				//处理视频
				if($type=='video'){
					$msgType = 'video';
					$videoTpl='<xml>
								<ToUserName><![CDATA[%s]]></ToUserName>
								<FromUserName><![CDATA[%s]]></FromUserName>
								<CreateTime>%s</CreateTime>
								<MsgType><![CDATA[%s]]></MsgType>
								<Video>
								<MediaId><![CDATA[%s]]></MediaId>
								<Title><![CDATA[%s]]></Title>
								<Description><![CDATA[%s]]></Description>
								</Video> 
								</xml>';
					$title='weixin视频回复demo';
					$description='自拍的视频测试使用';

					//$media_id=$this->addVideo();
					$resultStr = sprintf($videoTpl, $fromUsername, $toUsername, $time, $msgType,'2kC8PDz3_MvdTZbgZlPe5YQPh131IyxHxbqvsQilfXPPI8VytWylJE0QQAIUYQdL',$title,$description);
              		echo $resultStr;
				}

				//用户的关注事件
				if($event=='subscribe'){

					$openid=$postObj->FromUserName;
					 $url='https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->getWxAccessToken().'&openid='.$openid.'&lang=zh_CN';
		            $json=$this->request($url,true,'get');
		            $userInfo=json_decode($json);
					$textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
					$msgType = 'text';
					$contentStr='嗨 '.$userInfo->nickname.' 欢迎关注服务号:酒逢知己饮,知己能几人';
					//$contentStr=$openid;
					
					//加入数据库
					$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
              		echo $resultStr;
              		$this->add($userInfo);
              		

				}
				//用户户取消关注事件
				if($event=='unsubscribe'){
					$openid=$postObj->FromUserName;
					//数据数据库删除openid 的用户
					$this->delete($openid);
				}
				if($event=='CLICK'){
					$eventKey=$postObj->EventKey;
					switch($eventKey){
						case 'image':
							$msgType = 'image';
							$imageTpl=" <xml>
		                    <ToUserName><![CDATA[%s]]></ToUserName>
		                    <FromUserName><![CDATA[%s]]></FromUserName>
		                    <CreateTime>%s</CreateTime>
		                    <MsgType><![CDATA[%s]]></MsgType>
		                    <Image>
		                    <MediaId><![CDATA[%s]]></MediaId>
		                    </Image>
		                    </xml>";

		             		$media_id=$this->addImg();
							$resultStr = sprintf($imageTpl, $fromUsername, $toUsername, $time, $msgType, $media_id);
							//$resultStr = sprintf($imageTpl, $fromUsername, $toUsername, $time, $msgType,$id);
							echo $resultStr;

							break;
						case 'voice':
							$msgType='voice';
							$voiceTpl=" <xml>
			                    <ToUserName><![CDATA[%s]]></ToUserName>
			                    <FromUserName><![CDATA[%s]]></FromUserName>
			                    <CreateTime>%s</CreateTime>
			                    <MsgType><![CDATA[%s]]></MsgType>
								<Voice>
								<MediaId><![CDATA[%s]]></MediaId>
								</Voice>
								</xml>";
							$media_id=$this->addVoice();
							$resultStr = sprintf($voiceTpl, $fromUsername, $toUsername, $time, $msgType,$media_id);
		              		echo $resultStr;
							break;
						case 'weather':
							$msgType = 'text';
							$textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
							$contentStr = '输入格式:天气+城市 例如:天气+北京';
							$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
             				echo $resultStr;
							break;
						case 'video':
							$msgType = 'video';
							$videoTpl='<xml>
										<ToUserName><![CDATA[%s]]></ToUserName>
										<FromUserName><![CDATA[%s]]></FromUserName>
										<CreateTime>%s</CreateTime>
										<MsgType><![CDATA[%s]]></MsgType>
										<Video>
										<MediaId><![CDATA[%s]]></MediaId>
										<Title><![CDATA[%s]]></Title>
										<Description><![CDATA[%s]]></Description>
										</Video> 
										</xml>';
							$title='weixin视频回复demo';
							$description='自拍的视频测试使用';

							//$media_id=$this->addVideo();
							$resultStr = sprintf($videoTpl, $fromUsername, $toUsername, $time, $msgType,'2kC8PDz3_MvdTZbgZlPe5YQPh131IyxHxbqvsQilfXPPI8VytWylJE0QQAIUYQdL',$title,$description);
		              		echo $resultStr;
							break;
						case 'joke':
							 $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
							$msgType = 'text';
							$contentStr=$this->getJoke();
							$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
             				echo $resultStr;
             				//$this->joke();
							break;
						case 'guide':

							$textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
							$msgType = 'text';
							$contentStr = '欢迎使用，回复文字、图片、语音、笑话以及视频都有相应的应答';
							 echo $resultStr;
							break;

					}
				}

				if (!empty($keyword)) {
					 $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
					$msgType = 'text';
					if(strpos($keyword, "+")){
						$arr=explode('+',$keyword);
						$city=$arr[1];
						$url="http://www.sojson.com/open/api/weather/json.shtml?city=".$city;
						$json=file_get_contents($url);
						$weather=json_decode($json,true);
						//获取一周天气的数组
						$arrWeather=$weather['data']['forecast'][0];
						$contentStr='今天最高温度: '.$arrWeather['high'].' 最低气温: '.$arrWeather['low'].' 风向:'.$arrWeather['fx'].'风力:'.$arrWeather['fl'].'  类别:'.$arrWeather['type'].' 提醒: '.$arrWeather['notice'];
						 $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
            			 echo $resultStr;
					}
					switch ($keyword) {
						case '你好':
							$contentStr = '小伙伴，你好啊';
							break;
						case '新手指南':
							$contentStr = '欢迎使用，回复文字、图片、语音、笑话以及视频都有相应的应答';
							break;
						case '天气':
							$contentStr = '格式:天气+城市 例如:天气+北京';
							break;
						case '笑话':
							$contentStr=$this->getJoke();
							break;
						default:
							$contentStr = $keyword;
							break;

					}
				}
              $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
              echo $resultStr;
        }else {
        	echo "";
        	exit;
        }
    }

    //处理用户获取curl
    public function request($curl,$https=true,$method='get',$data=null){
    
    
	    $ch=curl_init();//初始化
	    curl_setopt($ch,CURLOPT_URL,$curl);//设置访问的url
	    curl_setopt($ch,CURLOPT_HEADER, false);//设置不需要头信息
	    curl_setopt($ch,CURLOPT_RETURNTRANSFER , true);//只获取页面内容，但不输出
	    if($https){
	      curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);//不做服务器端的认证
	      curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);//不做客户端认证
	    }


	    if($method=='post'){


	      curl_setopt($ch, CURLOPT_POST, true);//设置请求是post方式
	      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//设置post请求的数据
	    }


	    $str=curl_exec($ch);//执行访问，返回结果


	    curl_close($ch);//关闭curl，释放资源


	    return $str;
 	 }
    public function getJoke(){
    	$url='http://japi.juhe.cn/joke/content/list.from?key=3abc0ee7ed5e4de087bf0e51d255b469&page=1&pagesize=30&sort=desc&time=1418745237';
		$json=file_get_contents($url);
		$string=substr($json, 55,-2);
		//var_dump($json);
		$arr=json_decode($string,true);
		$k=mt_rand(0,count($arr));
		return $arr[$k]['content'];

    }
    //插入数据库
    public function add($user){
    	include 'mysql.php';
        insert($user);
    }
    //删除
    public function delete($openid){
    	  $link=mysqli_connect('localhost','root','root');
		    if(!$link){
		        exit('数据库连接失败');
		    }
		    mysqli_set_charset($link,'utf8');
		    mysqli_select_db($link,'weixin');
	      $sql='delete from user where openid='."'$openid'";
	      mysqli_query($link,$sql);
    }
    /*
     * 验证token
     */
	private function checkSignature()
	{
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];  		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}


	//上传图片
	public function addImg()                          
	{
		$access_token=$this->getWxAccessToken();
		$url="https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$access_token."&type=image";
		if (class_exists('\CURLFile')) {  
		    $data = array('media' => new \CURLFile(realpath("demo.jpg")));              
		} else {  
		    $data = array('media' => '@' . realpath("demo.jpg"));  
		} 
        $img=new Curl();
        $newimg=$img->post($url,$data);
        $newimg=json_decode($newimg,true);
        $media = $newimg['media_id'];
        return $media;
	}
	//语音上传
	public function addVoice()                          
	{
		$access_token=$this->getWxAccessToken();
		$url="https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$access_token."&type=voice";
		if (class_exists('\CURLFile')) {  
		    $data = array('media' => new \CURLFile(realpath("demo.mp3")));                
		} else {  
		    $data = array('media' => '@' . realpath("demo.mp3"));  
		} 
        $mp3=new Curl();
        $newimg=$mp3->post($url,$data);
        $newimg=json_decode($newimg,true);
        $media = $newimg['media_id'];
        return $media;
	}
	//视频上传
	public function addVideo()                          
	{
		$access_token=$this->getWxAccessToken();
		$url="https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$access_token."&type=video";
		if (class_exists('\CURLFile')) {  
		    $data = array('media' => new \CURLFile(realpath("3.mp4")));               
		} else {  
		    $data = array('media' => '@' . realpath("3.mp4"));  
		} 
        $mp4=new Curl();
        $newimg=$mp4->post($url,$data);
        $newimg=json_decode($newimg,true);
        $media = $newimg['media_id'];
        return $media;
	}
	public function  getWxAccessToken(){

        if($_SESSION['access_token'] && $_SESSION['expire_time']>time())
        {
            return $_SESSION['access_token'];
        }
        else
        {
            $ch = curl_init();
            curl_setopt($ch , CURLOPT_URL , 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx2c25493bafc3b3d2&secret=0cc89f911e0559e370674a27ed866d16');
            curl_setopt($ch , CURLOPT_RETURNTRANSFER,1);
            
            $json = curl_exec($ch);
            $arr=json_decode($json,true);

            $access_token=$arr['access_token'];
            $_SESSION['access_token']=$access_token;
            $_SESSION['expire_time']=time()+7200;
            return $access_token;
        } 
    }

   public function createMenu(){
    	 //$access_token=$this->getWxAccessToken();
    	 $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=WTwikmsKivyxfRLgi9qVZ1DB5Tg2qCfbbDmThU9c8Bknax2O4tWRWubo14G2xOQ1ySGvzJeHZeENRsFLFfepaqRc6dbuWAtVpmh7qi-QhXFqj8__H6H8tHvH7oqAmqD1UVRjAIAGHD";

      $jsonmenu = '{
      "button":[
      {
            "name":"指南",
           "sub_button":[
            {
               "type":"click",
               "name":"新手指南",
               "key":"guide"
            },
            {
               "type":"view",
               "name":"博客",
               "url":"http://www.yanyonglei.top/blog/index.php"
            },
            {
               "type":"click",
               "name":"天气",
               "key":"weather"
            },
            {
               "type":"click",
               "name":"笑话",
               "key":"joke"
            },
            {
                "type":"view",
                "name":"本地天气",
                "url":"http://m.hao123.com/a/tianqi"
            }]
       },
       {
           "name":"君临天下",
           "sub_button":[
            {
               "type":"click",
               "name":"图片",
               "key":"image"
            },
            {
               "type":"click",
               "name":"音频",
               "key":"voice"
            },
            {
                "type":"click",
                "name":"视频",
                "key":"video"
            }]
       }]
 }';


       $this->https($url, $jsonmenu);
       

    }


    function https($url,$data = null){
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	    if (!empty($data)){
	        curl_setopt($curl, CURLOPT_POST, 1);
	        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	    }
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($curl);
	    curl_close($curl);
	    return $output;
	}
}

