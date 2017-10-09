<?php


class Curl
{
	private $ch;
	
	public function __construct()
	{
		$this->ch = curl_init();
		$this->ready();
	}
	
	public function get($url , $params = [])
	{
		if (count($params) > 0) {
			$url .= '&'.http_build_query($params); 
		}
		
		$this->setUrl($url);  
		return $this->exec();
	}
	// public function get2($url , $params = [])
	// {
	// 	if (count($params) > 0) {
	// 		$url .= '?'.http_build_query($params); 
	// 	}
		
	// 	$this->setUrl($url);  
	// 	return $this->exec();
	// }
	
	public function post($url , $params = [])
	{
		$this->setOpt(CURLOPT_POST , 1);
		$this->setOpt(CURLOPT_POSTFIELDS , $params);
		$this->setUrl($url);  
		return $this->exec();
		
	}
	public function put($url , $params = [])
	{
		$this->setOpt(CURLOPT_CUSTOMREQUEST , 'PUT');
		$this->setOpt(CURLOPT_POSTFIELDS , $params);
		$this->setUrl($url);  
		return $this->exec();
		
	}
	public function delete($url , $params = [])
	{
		$this->setOpt(CURLOPT_CUSTOMREQUEST , 'DELETE');
		$this->setOpt(CURLOPT_POSTFIELDS , $params);
		$this->setUrl($url);  
		return $this->exec();
		
	}
	
	
	public function setUrl($url)
	{
		$this->setOpt(CURLOPT_URL , $url);
	}
	
	private function exec()
	{
		$result = curl_exec($this->ch);
		
		if ($result) {
			
			return $result;
		} else {
			return [
				'errno' => curl_errno($this->ch),
				'error' => curl_error($this->ch)
			];
		}
		
	}
	private function setOpt($option , $url)
	{
	
		curl_setOpt($this->ch , $option , $url);
	}
	
	public function ready()
	{
		$this->setOpt(CURLOPT_RETURNTRANSFER , 1);
		$this->setOpt(CURLOPT_HEADER , 0);
	}
} 