<?php 
class CyrillicName_Validator extends CValidator
{

	private $address_pattern1 = '/^[а-пр-яА-ПР-Я\s\-]+$/';
	private $address_pattern = '/^[\p{Cyrillic}\s\-]+$/u';
	private $errorMessage='Полето трябва да бъде попълнено на кирилица!';
	/**
	 * Validates the attribute of the object.
	 * If there is any error, the error message is added to the object.
	 * @param CModel $object the object being validated
	 * @param string $attribute the attribute being validated
	 */
	protected function validateAttribute($object,$attribute)
	{
		$value=$object->$attribute;
		if(!preg_match($this->address_pattern, $value))
		{
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
		$condition="!value.match({$this->address_pattern1})";
		$isEmpty="value || !/^\s*$/.test(value)";	
		return "if(".$isEmpty."){
		if(".$condition.") {
		messages.push(".CJSON::encode($this->errorMessage).");
	}}
	";
	}
}
?>