<?php 
class MobilePhone_Validator extends CValidator
{
	//Excludes the following numbers
	//0882218059
    //0893926396
    //0888319933

	private $phone_pattern1 = '/^((((\+|00)?(359))|0)(\s?\d){9})$/';
	private $phone_pattern = '/^((359)|0)[1-9]\d{8}$/';
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
		
		if(!preg_match($pattern, $value))
		{
			$this->addError($object,$attribute,Yii::t('client', 'Mobile Validator message'));
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