<?php

/**
 * Encapsulates the utility functions not required by user.
 *
 * @package default
 * @author Timothy S Sabat
 */
class WufooApiWrapperBase {

	protected function getHelper($url, $type, $iterator, $index = 'Hash') {
		$this->curl = new WufooCurl();
		$response = $this->curl->getAuthenticated($url, $this->apiKey);
		$response = json_decode($response);
		$className = 'Wufoo'.$type;
		foreach ($response->$iterator as $obj) {
			$arr[$obj->$index] = new $className($obj);
		}
		return $arr;
	}
	
	protected function getFullUrl($url) {
		return 'https://'.$this->subdomain.'.'.$this->domain.'/api/v3/'.$url.'.json';
	}
}


class WufooCurl {
	
	public function __construct() {
		//TIMTODO: set timout
	}
	
	public function getAuthenticated($url, $apiKey) {
		$this->curl = curl_init($url); 
		$this->setBasicCurlOptions();
		
		curl_setopt($this->curl, CURLOPT_USERPWD, $apiKey.':footastical');

		$response = curl_exec($this->curl);
		$this->setResultCodes();
		$this->checkForCurlErrors();
		$this->checkForGetErrors($response);
		curl_close($this->curl);
		return $response;
	}
	
	public function postAuthenticated($postParams, $url, $apiKey) {
		$this->curl = curl_init($url); 
		$this->setBasicCurlOptions();
		
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-type: multipart/form-data', 'Expect:'));
		curl_setopt($this->curl, CURLOPT_POST, true);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $postParams);		
		curl_setopt($this->curl, CURLOPT_USERPWD, $apiKey.':footastical');

		$response = curl_exec($this->curl);
		$this->setResultCodes();
		$this->checkForCurlErrors();
		$this->checkForPostErrors($response);
		curl_close($this->curl);
		return $response;
	}
	
	public function setBasicCurlOptions() {
		//http://bugs.php.net/bug.php?id=47030
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->curl, CURLOPT_USERAGENT, 'Wufoo API Wrapper');
		curl_setopt($this->curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	}
	
	private function setResultCodes() {
		$this->ResultStatus = curl_getinfo($this->curl);		
	}
	
	private function checkForCurlErrors() {
		if(curl_errno($this->curl)) {
			if ($closeConnection) curl_close($this->curl);
			throw new WufooException(curl_error($this->curl), curl_errno($this->curl));
		}
	}
	
	private function checkForGetErrors($response) {
		switch ($this->ResultStatus['http_code']) {
			case 200:
				//ignore, this is good.
				break;
			case 401:
				throw new WufooException('(401) Forbidden.  Check your API key.', 401);
				break;
			default:
				$this->throwResponseError($response);
				break;
		}
	}
	
	private function checkForPostErrors($response) {
		switch ($this->ResultStatus['http_code']) {
			case 200:
			case 201:
				//ignore, this is good.
				break;
			case 401:
				throw new WufooException('(401) Forbidden. Check your API key.', 401);
				break;
			default:
				$this->throwResponseError($response);
				break;
		}
	}
	
	private function throwResponseError($response) {
		if ($response) {
			$obj = json_decode($response);
			throw new WufooException('('.$obj->HTTPCode.') '.$obj->Text, $this->ResultStatus['HTTP_CODE']);
		} else {
			throw new WufooException('(500) This is embarrassing... We did not anticipate this error type.  Please contact support here: support@wufoo.com', 500);
		}
		return $response;
	}
	
}

/**
 * Thrown by WufooCurl calls.
 *
 * @package default
 * @author Timothy S Sabat
 */
class WufooException extends Exception {
	
	public function __construct($message, $code) {
		parent::__construct($message, $code);
	}
	
};

?>