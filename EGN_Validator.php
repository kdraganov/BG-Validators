<?php

class EGN_Validator extends CValidator
{
	private $errorMessage;
	
	protected function validateAttribute($object,$attribute)
	{
		$this->errorMessage=Yii::t('client', 'EGN Validator message');

		$value=$object->$attribute;

		if(!($this->IsEgnValid($value))){
			$this->addError($object,$attribute,$this->errorMessage);
		}

	}

	
	/**
	 * Returns the JavaScript needed for performing client-side validation.
	 * @param CModel $object the data object being validated
	 * @param string $attribute the name of the attribute to be validated.
	 * @return string the client-side validation script.
	 * @see CActiveForm::enableClientValidation
	 */
	public function clientValidateAttribute($object,$attribute)
	{
		$condition="!isValidEGN(value)";
		$isEmpty="value || !/^\s*$/.test(value)";
		return "if(".$isEmpty."){
		if(".$condition.") {
		messages.push(".CJSON::encode(Yii::t('client', 'EGN Validator message')).");
	}}
	";
	}
	
	private function IsEgnValid($egn) {
		if(!is_numeric($egn)){
			return false;
		}
		$EGN_WEIGHTS = array(2,4,8,5,10,9,7,3,6);

		$year = substr($egn,0,2);
		$mon  = substr($egn,2,2);
		$day  = substr($egn,4,2);
		if ($mon > 40) {
			if (!checkdate($mon-40, $day, $year+2000)) return false;
		} else
			if ($mon > 20) {
			if (!checkdate($mon-20, $day, $year+1800)) return false;
		} else {
			if (!checkdate($mon, $day, $year+1900)) return false;
		}
		$checksum = substr($egn,9,1);
		$egnsum = 0;
		for ($i=0;$i<9;$i++)
			$egnsum += substr($egn,$i,1) * $EGN_WEIGHTS[$i];
		$valid_checksum = $egnsum % 11;
		if ($valid_checksum == 10)
			$valid_checksum = 0;
		if ($checksum == $valid_checksum)
			return true;
	}

}
?>