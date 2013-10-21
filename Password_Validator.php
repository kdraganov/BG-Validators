<?php 
class Password_Validator extends CValidator
{

	private $weak_pattern = '/^(.*(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).*){6,}$/';
	private $strong_pattern = '/^(?=.*\d(?=.*\d))(?=.*[a-zA-Z](?=.*[a-zA-Z])).{6,}$/';
	
	/**
	 * Validates the attribute of the object.
	 * If there is any error, the error message is added to the object.
	 * @param CModel $object the object being validated
	 * @param string $attribute the attribute being validated
	 */
	protected function validateAttribute($object,$attribute)
	{	
		$pattern = $this->weak_pattern;
		$value=$object->$attribute;
		if(!preg_match($pattern, $value))
		{
			$this->addError($object,$attribute,Yii::t('client', 'Weak Password'));
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

		$pattern = $this->weak_pattern;

		$condition="!value.match({$pattern})";
		$isEmpty="value || !/^\s*$/.test(value)";
		return "if(".$isEmpty."){
		if(".$condition.") {
		messages.push(".CJSON::encode(Yii::t('client', 'Weak Password')).");
	}}
	";
	}
}
?>