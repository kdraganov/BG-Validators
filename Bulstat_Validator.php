<?php 
class Bulstat_Validator extends CValidator
{


	private $pattern ='/^(\d{9}|\d{13}|\d{0})$/';
	/**
	 * Validates the attribute of the object.
	 * If there is any error, the error message is added to the object.
	 * @param CModel $object the object being validated
	 * @param string $attribute the attribute being validated
	 */
	protected function validateAttribute($object,$attribute)
	{

		$pattern = $this->pattern;
		$value=$object->$attribute;
		if(isset($value)){
			if(!preg_match($pattern, $value))
			{
				$this->addError($object,$attribute,Yii::t('client', 'Invalid Bulstat'));
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

		$pattern = $this->pattern;

		$condition="!value.match({$pattern})";
		$isEmpty="value || !/^\s*$/.test(value)";

		return "if(".$isEmpty."){
		if(".$condition.") {
		messages.push(".CJSON::encode(Yii::t('client', 'Invalid Bulstat')).");
	}}
	";
	}
}
?>