<?php
    
    
    namespace App\Form;
    
    use Kris\LaravelFormBuilder\Form;
    
    
    /**
     * @method FieldOptionSetters text(string $field_name, array $options = [], bool $modify = false)
     * @method FieldOptionSetters textarea(string $field_name, array $options = [], bool $modify = false)
     * @method FieldOptionSetters radio(string $field_name, array $options = [], bool $modify = false)
     * @method FieldOptionSetters email(string $field_name, array $options = [], bool $modify = false)
     * @method FieldOptionSetters hidden(string $field_name, array $options = [], bool $modify = false)
     * @method FieldOptionSetters password(string $field_name, array $options = [], bool $modify = false)
     * @method FieldOptionSetters image(string $field_name, array $options = [], bool $modify = false)
     * @method FieldOptionSetters number(string $field_name, array $options = [], bool $modify = false)
     * @method FieldOptionSetters file(string $field_name, array $options = [], bool $modify = false)
     * @method FieldOptionSetters submit(string $field_name, array $options = [], bool $modify = false)
     * @method FieldOptionSetters url(string $field_name, array $options = [], bool $modify = false)
     * @method FieldOptionSetters tel(string $field_name, array $options = [], bool $modify = false)
     * @method FieldOptionSetters search(string $field_name, array $options = [], bool $modify = false)
     * @method FieldOptionSetters color(string $field_name, array $options = [], bool $modify = false)
     * @method FieldOptionSetters date(string $field_name, array $options = [], bool $modify = false)
     * @method FieldOptionSetters month(string $field_name, array $options = [], bool $modify = false)
     * @method FieldOptionSetters week(string $field_name, array $options = [], bool $modify = false)
     * @method FieldOptionSetters range(string $field_name, array $options = [], bool $modify = false)
     * @method FieldOptionSetters time(string $field_name, array $options = [], bool $modify = false)
     * @method FieldOptionSetters reset(string $field_name, array $options = [], bool $modify = false)
     * @method FieldOptionSetters collection(string $field_name, array $options = [], bool $modify = false)
     */
    class NewAPI extends Form
    {
        public $fieldName;
        
        public function __call($fieldType, $args = [])
        {
            $this->fieldName = $args[ 0 ];
            $options         = [];
            if (isset( $args[ 1 ] )) {
                $options = $args[ 1 ];
            }
            $this->add($this->fieldName, $fieldType, $options);
            
            return new FieldOptionSetters($this);
        }

        /**
         * @param string $fieldName
         * @param array $options
         *
         * @return SelectFieldOptionSetters
         */
        public function select($fieldName, $options = [])
        {
            $this->fieldName = $fieldName;
            $this->add($fieldName, 'select', $options);
            
            return new SelectFieldOptionSetters($this);
        }

        /**
         * @param string $fieldName
         * @param array $options
         *
         * @return ChoiceFieldOptionSetters
         */
        public function choice($fieldName, $options = [])
        {
            $this->fieldName = $fieldName;
            $this->add($fieldName, 'select', $options);
            
            return new ChoiceFieldOptionSetters($this);
        }

        /**
         * @param string $fieldName
         * @param array $options
         *
         * @return CheckboxFieldOptionSetters
         */
        public function checkbox($fieldName, $options = [])
        {
            $this->fieldName = $fieldName;
            $this->add($fieldName, 'select', $options);
            
            return new CheckboxFieldOptionSetters($this);
        }

        /**
         * @param string $fieldName
         * @param array $options
         *
         * @return EntityFieldOptionSetters
         */
        public function entity($fieldName, $options = [])
        {
            $this->fieldName = $fieldName;
            $this->add($fieldName, 'entity', $options);

            return new EntityFieldOptionSetters($this);
        }

        /**
         * @param string $fieldName
         * @param array $options
         *
         * @return RepeatedFieldOptionSetters
         */
        public function repeated($fieldName, $options = [])
        {
            $this->fieldName = $fieldName;
            $this->add($fieldName, 'repeated', $options);

            return new RepeatedFieldOptionSetters($this);
        }

    }
    
    /**
     * @method $this rules(string $field_name, array $options = [], bool $modify = false)
     * @method $this cssClass(string $field_name, array $options = [], bool $modify = false)
     * @method $this labelText(string $field_name, array $options = [], bool $modify = false)
     * @method $this defaultValue(string $field_name, array $options = [], bool $modify = false)
     * @method $this value(string $field_name, array $options = [], bool $modify = false)
     * @method $this helpText(string $field_name, array $options = [], bool $modify = false)
     * @method $this helpTag(string $field_name, array $options = [], bool $modify = false)
     * @method $this wrapperClass(string $field_name, array $options = [], bool $modify = false)
     * @method $this errorsCssClass(string $field_name, array $options = [], bool $modify = false)
     * @method $this labelCssClass(string $field_name, array $options = [], bool $modify = false)
     */
    class FieldOptionSetters
    {
        private $formObj;
        private $currentFieldObj;
        private $mapping = [
            //  'methodName' => 'aKeyToSet',
            'options'        => 'choices',
            'cssClass'       => 'attr.class',
            'labelText'      => 'label',
            'defaultValue'   => 'default_value',
            'helpText'       => 'help_block.text',
            'helpTag'        => 'help_block.tag',
            'wrapperClass'   => 'wrapper.class',
            'errorsCssClass' => 'errors.class',
            'labelCssClass'  => 'label_attr.class',
        ];
        
        public function __construct($formObj)
        {
            $this->formObj         = $formObj;
            $this->currentFieldObj = $formObj->getField($formObj->fieldName);
        }
        
        public function __call($calledMethodName, $valueArr)
        {
            if(isset($this->mapping[$calledMethodName]))
                $calledMethodName = $this->mapping[$calledMethodName];

            $this->currentFieldObj->setOption($calledMethodName, $valueArr[0]);
            
            return $this;
        }
    }
    
    /**
     * @method $this options(array $choices, array $options = [], boolean $modify = false)
     * @method $this selected(string $field_name, array $options = [], boolean $modify = false)
     * @method $this empty_value(string $empty_value, array $options = [], boolean $modify = false)
     */
    class SelectFieldOptionSetters extends FieldOptionSetters
    {
    }
    
    /**
     * @method $this value(array $value, array $options = [], boolean $modify = false)
     * @method $this checked(string $field_name, array $options = [], boolean $modify = false)
     */
    class CheckboxFieldOptionSetters extends FieldOptionSetters
    {
    }
    
    /**
     * @method $this choices(array $choices, array $options = [], boolean $modify = false)
     * @method $this selected(array $preSelected, array $options = [], boolean $modify = false)
     * @method $this expanded(bool $boolean, array $options = [], boolean $modify = false)
     * @method $this multiple(bool $boolean, array $options = [], boolean $modify = false)
     */
    class ChoiceFieldOptionSetters extends FieldOptionSetters
    {
    }
    
    /**
     * @method $this class(string $className=null) Full path to the Model class that will be used to fetch choices.
     * @method $this query_builder(closure $func) If provided, used to filter data before setting as choices. If null, gets all data.
     * @method $this property(string $property) Property from model that will be used as label for options in choices.
     * @method $this property_key(string $property_key) Property from model that will be used as value for options in choices.
     */
    class EntityFieldOptionSetters extends FieldOptionSetters
    {
    }
    /**
     * @method $this type(string $fieldType='password') Field type to be used.;
     * @method $this second_name(string $second_name={FIELD_NAME}_confirmation) Name of the second field, if empty, uses the default name with _confirmation appended.
     * @method $this first_options(array $options=[]) Options for the first field.
     * @method $this second_options(array $options=[]) Options for the second field.
     */
    class RepeatedFieldOptionSetters extends FieldOptionSetters
    {
    }
