<?php
class ID_Number_Validator extends CValidator
{
	private $errorMessage;
	private $proxyList;

	protected function validateAttribute($object,$attribute)
	{
		$this->proxyList=explode(',',Config::getValue('proxyList'));
		$this->errorMessage=Yii::t('client', 'ID Validator message');
		$value=$object->$attribute;
		if(!($this->isIdValid($value))){
			Yii::app()->cache->set('icIdCounter', 0);
			$this->addError($object,$attribute,$this->errorMessage);
		}
	}

	public function isIdValid($idNumber, $increment = false){
		if($idNumber == null || strlen($idNumber)!=9 ){
			return false;
		}
		Yii::app()->cache->set('icIdCounter', Yii::app()->cache->get('icIdCounter')+1);
		$key='id_number_requests';
		$date =time();
		$cache=Yii::app()->cache;
		$value=$cache->get($key);
		$next_proxy_number=0;
		if ($value == '') {
			$cache->set($key,'1?'.$date.'?'.$next_proxy_number);
		}elseif($increment){
			$cached_values = explode("?", $value);
			$next_proxy_number=$cached_values[2]+1;
			if($next_proxy_number>=count($this->proxyList)-1){
				$next_proxy_number=0;
			}
			$cache->set($key,'1?'.$date.'?'.$next_proxy_number);
		}
		else{
			$cached_values = explode("?", $value);
			$oldDate=$cached_values[1];
			$diff = ($date  - $oldDate);
			$next_proxy_number=$cached_values[2];
			if(date('i',$diff)<15 && $cached_values[0]>6){
				//get next proxy
				$next_proxy_number=$cached_values[2]+1;
				if($next_proxy_number>=count($this->proxyList)-1){
					$next_proxy_number=0;
				}
				$cache->set($key,'1?'.$date.'?'.$next_proxy_number);
			}else{
				//use same proxy and update the counter
				$number_of_requests=$cached_values[0]+1;
				$cache->set($key,$number_of_requests.'?'.$date.'?'.$cached_values[2]);
			}
		}
		$data=array('TypeDoc'=>'6729','__Click'=>'C22578700047C6B1.fb37713347cbdef5c225786a00493131/$Body/0.148C','%%Surrogate_TypeDoc'=>'1','Number'=>$idNumber);
		$response=Yii::app()->CURL->run('http://nbds.mvr.bg/bds7/web.nsf/fEnVerification?',false,$data,$this->proxyList[$next_proxy_number]);
		Yii::app()->session['isIdCardValidated']=true;
		if(strpos($response['body'], "Document N: [$idNumber] is valid.") !== false){
			Yii::log($response['body'].'Result is: valid idNumber. Proxy: '.$this->proxyList[$next_proxy_number].' Time:'.$response['total_time'], 'error', 'IDValidation');
			return true;
		}elseif (strpos($response['body'], "Document N: [$idNumber] is not on the list of valid identity documents.") !== false){
			Yii::log($response['body'].'Result is: invalid idNumber. Proxy: '.$this->proxyList[$next_proxy_number].' Time:'.$response['total_time'], 'error', 'IDValidation');
			return false;
		}elseif (strpos($response['body'], "Access denied.") !== false && $cache->get('icIdCounter')<=count($this->proxyList)){

			Yii::log($response['body'].'Result is: Access denied to MVR site. Proxy: '.$this->proxyList[$next_proxy_number].' Time:'.$response['total_time'], 'error', 'IDValidation');
			return  $this->isIdValid($idNumber,true);
		}elseif ($cache->get('icIdCounter')<=count($this->proxyList)){
			if(strpos($response['body'], "Access denied.") === false ){
				$body="Неработещо прокси: ".$this->proxyList[$next_proxy_number];
				$mail=new Mailer();
				$mail->idCardMail($body);
				Yii::log($response['body'].'Invalid proxy: '.$this->proxyList[$next_proxy_number].' Time:'.$response['total_time'], 'error', 'IDValidation');
			}
			return  $this->isIdValid($idNumber,true);
		}else{
			$cache->set($idNumber, 'invalid', 1500);
			$body="Лична карта №$idNumber неуспешна валидация!";
			$mail=new Mailer();
			$mail->idCardMail($body);
			Yii::log($response['body'].'Result is: Id card could not be validated. Proxy: '.$this->proxyList[$next_proxy_number].' Time:'.$response['total_time'], 'error', 'IDValidation');
			return true;
		}
	}
}
?>