<?php

class IBAN_Validator extends CValidator
{
	private $errorMessage;
	protected function validateAttribute($object,$attribute)
	{
		$this->errorMessage=Yii::t('client', 'IBAN Validator message');
		$value=$object->attributes[$attribute];
		if(isset($value) && $value != ''){ 
			if(!($this->isIbanValid(str_replace(" ", "", $value)))){ 
				$this->addError($object,$attribute,$this->errorMessage);
			}
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
		$condition="!CheckIBAN(value)";
		$isEmpty="value || !/^\s*$/.test(value)";
		return "if(".$isEmpty."){
		if(".$condition.") {
		messages.push(".CJSON::encode(Yii::t('client', 'IBAN Validator message')).");
	}}
	";
	}

	public function isIbanValid($iban){
		require "Validate/Finance/IBAN.php";
		$iban=new Validate_Finance_IBAN($iban);
		return $iban->validate();
	}

}
?>