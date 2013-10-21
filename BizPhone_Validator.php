<?php 
class BizPhone_Validator extends CValidator
{


	private $phone_pattern = '/^0\d{4,}$/';
	/**
	 * Validates the attribute of the object.
	 * If there is any error, the error message is added to the object.
	 * @param CModel $object the object being validated
	 * @param string $attribute the attribute being validated
	 */
	protected function validateAttribute($object,$attribute)
	{

		$pattern = $this->phone_pattern;
		$value=$object->$attribute;
		if(isset($value)){
			if(!preg_match($pattern, $value))
			{
				$this->addError($object,$attribute,Yii::t('client', 'Mobile Validator message'));
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

		$pattern = $this->phone_pattern;

		$condition="!value.match({$pattern})";
		$isEmpty="value || !/^\s*$/.test(value)";

		return "if(".$isEmpty."){
		if(".$condition.") {
		messages.push(".CJSON::encode(Yii::t('client', 'Mobile Validator message')).");
	}}
	";
	}
}
?>