<?php
	
	
	/**
	 * @method FieldOptionSetters text(      string  $field_name, array $options =[] , boolean $modify = false)
	 * @method FieldOptionSetters textarea(  string  $field_name, array $options = [] , boolean $modify = false)
	 * @method FieldOptionSetters radio(     string  $field_name, array $options = [] , boolean $modify = false)
	 * @method FieldOptionSetters email(     string  $field_name, array $options = [] , boolean $modify = false)
	 * @method FieldOptionSetters hidden(    string  $field_name, array $options = [] , boolean $modify = false)
	 * @method FieldOptionSetters password(  string  $field_name, array $options = [] , boolean $modify = false)
	 * @method FieldOptionSetters image(     string  $field_name, array $options = [] , boolean $modify = false)
	 * @method FieldOptionSetters number(    string  $field_name, array $options = [] , boolean $modify = false)
	 * @method FieldOptionSetters file(      string  $field_name, array $options = [] , boolean $modify = false)
	 * @method FieldOptionSetters submit(    string  $field_name, array $options = [] , boolean $modify = false)
	 * @method FieldOptionSetters url(       string  $field_name, array $options = [] , boolean $modify = false)
	 * @method FieldOptionSetters tel(       string  $field_name, array $options = [] , boolean $modify = false)
	 * @method FieldOptionSetters search(    string  $field_name, array $options = [] , boolean $modify = false)
	 * @method FieldOptionSetters color(     string  $field_name, array $options = [] , boolean $modify = false)
	 * @method FieldOptionSetters date(      string  $field_name, array $options = [] , boolean $modify = false)
	 * @method FieldOptionSetters month(     string  $field_name, array $options = [] , boolean $modify = false)
	 * @method FieldOptionSetters week(      string  $field_name, array $options = [] , boolean $modify = false)
	 * @method FieldOptionSetters range(     string  $field_name, array $options = [] , boolean $modify = false)
	 * @method FieldOptionSetters time(      string  $field_name, array $options = [] , boolean $modify = false)
	 * @method SelectFieldOptionSetters select_(string  $field_name, array $options = [] , boolean $modify = false)
	 */
	class NewAPI extends Form
	{
		public $fieldName;
		public function __call($fieldType,$args = [])
		{
			$this->fieldName =  $args[0];
			$options = [];
			if( isset($args[1]) ) $options = $args[1];
			$this->add($this->fieldName, $fieldType, $options);
			return new FieldOptionSetters($this);
		}
		public function select( $fieldName , $options = [] )
		{
			$this->fieldName = $fieldName;
			$this->add($fieldName, 'select',$options);
			return new SelectFieldOptionSetters($this);
		}
		public function choice( $fieldName , $options = [] )
		{
			$this->fieldName = $fieldName;
			$this->add($fieldName, 'select',$options);
			return new ChoiceFieldOptionSetters($this);
		}
		public function checkbox( $fieldName , $options = [] )
		{
			$this->fieldName = $fieldName;
			$this->add($fieldName, 'select',$options);
			return new CheckboxFieldOptionSetters($this);
		}
	}
	/**
	 * @method $this rules(          string  $field_name, array $options =[] , boolean $modify = false)
	 * @method $this cssClass(       string  $field_name, array $options =[] , boolean $modify = false)
	 * @method $this labelText(      string  $field_name, array $options =[] , boolean $modify = false)
	 * @method $this defaultValue(   string  $field_name, array $options =[] , boolean $modify = false)
	 * @method $this value(          string  $field_name, array $options =[] , boolean $modify = false)
	 * @method $this helpText(       string  $field_name, array $options =[] , boolean $modify = false)
	 * @method $this helpTag(        string  $field_name, array $options =[] , boolean $modify = false)
	 * @method $this wrapperClass(   string  $field_name, array $options =[] , boolean $modify = false)
	 * @method $this errorsCssClass( string  $field_name, array $options =[] , boolean $modify = false)
	 * @method $this labelCssClass(  string  $field_name, array $options =[] , boolean $modify = false)
	 */
	class FieldOptionSetters
	{
		private $formObj;
		private $currentFieldObj;
		private $mapping = [
			//  'methodName'    => 'aKeyToSet',
				'options'       => 'choices',
				'choices'       => 'choices',
				'selected'      => 'selected',
				'rules'         => 'rules',
				'cssClass'      => 'attr.class',
				'labelText'     => 'label',
				'defaultValue'  => 'default_value',
				'value'         => 'value',
				'helpText'      => 'help_block.text',
				'helpTag'       => 'help_block.tag',
				'wrapperClass'  => 'wrapper.class',
				'errorsCssClass'=> 'errors.class',
				'labelCssClass' => 'label_attr.class'
		];
		public function __construct($formObj)
		{
			$this->formObj = $formObj;
			$this->currentFieldObj = $formObj->getField($formObj->fieldName);
		}
		public function __call($calledMethodName,$valueArr)
		{
			$optionKey = $this->mapping[$calledMethodName];
			$this->currentFieldObj->setOption($optionKey,$valueArr[0]);
			return $this;
		}
	}
	/**
	 * @method $this options(        array   $choices, array $options =[] , boolean $modify = false)
	 * @method $this selected(       string  $field_name, array $options =[] , boolean $modify = false)
	 * @method $this empty_value(    string  $empty_value, array $options =[] , boolean $modify = false)
	 */
	class SelectFieldOptionSetters extends FieldOptionSetters{	}
	/**
	 * @method $this value(   array   $value, array $options =[] , boolean $modify = false)
	 * @method $this checked( string  $field_name, array $options =[] , boolean $modify = false)
	 */
	class CheckboxFieldOptionSetters extends FieldOptionSetters{	}
	/**
	 * @method $this choices(  array $choices,  array $options =[] , boolean $modify = false)
	 * @method $this selected( array $preSelected, array $options =[] , boolean $modify = false)
	 * @method $this expanded( bool  $boolean, array $options =[] , boolean $modify = false)
	 * @method $this multiple( bool  $boolean, array $options =[] , boolean $modify = false)
	 */
	class ChoiceFieldOptionSetters extends FieldOptionSetters{	}
