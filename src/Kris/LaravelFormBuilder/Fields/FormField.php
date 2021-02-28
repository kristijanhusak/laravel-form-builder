<?php

namespace Kris\LaravelFormBuilder\Fields;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Kris\LaravelFormBuilder\Filters\Exception\FilterAlreadyBindedException;
use Kris\LaravelFormBuilder\Filters\FilterInterface;
use Kris\LaravelFormBuilder\Filters\FilterResolver;
use Kris\LaravelFormBuilder\Form;
use Kris\LaravelFormBuilder\FormHelper;
use Kris\LaravelFormBuilder\Rules;

/**
 * Class FormField
 *
 * @package Kris\LaravelFormBuilder\Fields
 */
abstract class FormField
{
    /**
     * Name of the field.
     *
     * @var string
     */
    protected $name;

    /**
     * Type of the field.
     *
     * @var string
     */
    protected $type;

    /**
     * All options for the field.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Is field rendered.
     *
     * @var bool
     */
    protected $rendered = false;

    /**
     * @var Form
     */
    protected $parent;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var FormHelper
     */
    protected $formHelper;

    /**
     * Name of the property for value setting.
     *
     * @var string
     */
    protected $valueProperty = 'value';

    /**
     * Name of the property for default value.
     *
     * @var string
     */
    protected $defaultValueProperty = 'default_value';

    /**
     * Is default value set?
     *
     * @var bool|false
     */
    protected $hasDefault = false;

    /**
     * @var \Closure|null
     */
    protected $valueClosure = null;

    /**
     * Array of filters key(alias/name) => objects.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Raw/unfiltered field value.
     *
     * @var mixed $rawValues
     */
    protected $rawValue;

    /**
     * Override filters with same alias/name for field.
     *
     * @var bool
     */
    protected $filtersOverride = false;

    /**
     * @param string $name
     * @param string $type
     * @param Form $parent
     * @param array $options
     */
    public function __construct($name, $type, Form $parent, array $options = [])
    {
        $this->name = $name;
        $this->type = $type;
        $this->parent = $parent;
        $this->formHelper = $this->parent->getFormHelper();
        $this->setTemplate();
        $this->setDefaultOptions($options);
        $this->setupValue();
        $this->initFilters();
    }


    /**
     * Setup the value of the form field.
     *
     * @return void
     */
    protected function setupValue()
    {
        $value = $this->getOption($this->valueProperty);
        $isChild = $this->getOption('is_child');

        if ($value instanceof \Closure) {
            $this->valueClosure = $value;
        }

        if (($value === null || $value instanceof \Closure) && !$isChild) {
            if ($this instanceof EntityType) {
                $attributeName = $this->name;
            } else {
                $attributeName = $this->getOption('value_property', $this->name);
            }

            $this->setValue($this->getModelValueAttribute($this->parent->getModel(), $attributeName));
        } elseif (!$isChild) {
            $this->hasDefault = true;
        }
    }

    /**
     * Get the template, can be config variable or view path.
     *
     * @return string
     */
    abstract protected function getTemplate();

    /**
     * @return string
     */
    protected function getViewTemplate()
    {
        return $this->parent->getTemplatePrefix() . $this->getOption('template', $this->template);
    }

    /**
     * Render the field.
     *
     * @param array $options
     * @param bool  $showLabel
     * @param bool  $showField
     * @param bool  $showError
     * @return string
     */
    public function render(array $options = [], $showLabel = true, $showField = true, $showError = true)
    {
        $this->prepareOptions($options);
        $value = $this->getValue();
        $defaultValue = $this->getDefaultValue();

        if ($showField) {
            $this->rendered = true;
        }

        // Override default value with value
        if (!$this->isValidValue($value) && $this->isValidValue($defaultValue)) {
            $this->setOption($this->valueProperty, $defaultValue);
        }

        if (!$this->needsLabel()) {
            $showLabel = false;
        }

        if ($showError) {
            $showError = $this->parent->haveErrorsEnabled();
        }

        $data = $this->getRenderData();

        return $this->formHelper->getView()->make(
            $this->getViewTemplate(),
            $data + [
                'name' => $this->name,
                'nameKey' => $this->getNameKey(),
                'type' => $this->type,
                'options' => $this->options,
                'showLabel' => $showLabel,
                'showField' => $showField,
                'showError' => $showError,
                'errorBag'  => $this->parent->getErrorBag(),
                'translationTemplate' => $this->parent->getTranslationTemplate(),
            ]
        )->render();
    }

    /**
     * Return the extra render data for this form field, passed into the field's template directly.
     *
     * @return array
     */
    protected function getRenderData()
    {
        return [];
    }

    /**
     * Get the attribute value from the model by name.
     *
     * @param mixed $model
     * @param string $name
     * @return mixed
     */
    protected function getModelValueAttribute($model, $name)
    {
        $transformedName = $this->transformKey($name);
        if (is_string($model)) {
            return $model;
        } elseif (is_object($model)) {
            return object_get($model, $transformedName);
        } elseif (is_array($model)) {
            return Arr::get($model, $transformedName);
        }
    }

    /**
     * Transform array like syntax to dot syntax.
     *
     * @param string $key
     * @return mixed
     */
    protected function transformKey($key)
    {
        return $this->formHelper->transformToDotSyntax($key);
    }

    /**
     * Prepare options for rendering.
     *
     * @param array $options
     * @return array The parsed options
     */
    protected function prepareOptions(array $options = [])
    {
        $helper = $this->formHelper;

        $this->options = $this->prepareRules($options);
        $this->options = $helper->mergeOptions($this->options, $options);

        $rulesParser = $helper->createRulesParser($this);
        $rules = $this->getOption('rules');
        $parsedRules = $rules ? $rulesParser->parse($rules) : [];


        foreach (['attr', 'label_attr', 'wrapper'] as $appendable) {
            // Append values to the 'class' attribute
            if ($this->getOption("{$appendable}.class_append")) {
                // Combine the current class attribute with the appends
                $append = $this->getOption("{$appendable}.class_append");
                $classAttribute = $this->getOption("{$appendable}.class", '') . ' ' . $append;
                $this->setOption("{$appendable}.class", $classAttribute);

                // Then remove the class_append option to prevent it from showing up as an attribute in the HTML
                $this->setOption("{$appendable}.class_append", null);
            }
        }

        if ($this->getOption('attr.multiple') && !$this->getOption('tmp.multipleBracesSet')) {
            $this->name = $this->name . '[]';
            $this->setOption('tmp.multipleBracesSet', true);
        }

        if ($this->parent->haveErrorsEnabled()) {
            $this->addErrorClass();
        }

        if ($this->getOption('required') === true || isset($parsedRules['required'])) {
            $lblClass = $this->getOption('label_attr.class', '');
            $requiredClass = $this->getConfig('defaults.required_class', 'required');

            if (!Str::contains($lblClass, $requiredClass)) {
                $lblClass .= ' ' . $requiredClass;
                $this->setOption('label_attr.class', $lblClass);
            }

            if ($this->parent->clientValidationEnabled()) {
                $this->setOption('attr.required', 'required');
            }

            if (isset($parsedRules['required'])) {
                unset($parsedRules['required']);
            }
        }

        if ($this->parent->clientValidationEnabled() && $parsedRules) {
            foreach($parsedRules as $rule => $param){
                $this->setOption('attr.' . $rule, $param);
            }
        }

        $this->setOption('wrapperAttrs', $helper->prepareAttributes($this->getOption('wrapper')));
        $this->setOption('errorAttrs', $helper->prepareAttributes($this->getOption('errors')));

        if ($this->getOption('help_block.text')) {
            $this->setOption(
                'help_block.helpBlockAttrs',
                $helper->prepareAttributes($this->getOption('help_block.attr'))
            );
        }

        return $this->options;
    }

    /**
     * Normalize and merge rules.
     * @param array $sourceOptions
     * @return array
     */
    protected function prepareRules(array &$sourceOptions = [])
    {
        $options = $this->options;

        // Normalize rules
        if (array_key_exists('rules_append', $sourceOptions)) {
            $sourceOptions['rules_append'] = $this->normalizeRules($sourceOptions['rules_append']);
        }

        if (array_key_exists('rules', $sourceOptions)) {
            $sourceOptions['rules'] = $this->normalizeRules($sourceOptions['rules']);
        }

        if (array_key_exists('rules', $options)) {
            $options['rules'] = $this->normalizeRules($options['rules']);
        }


        // Append rules
        if ($rulesToBeAppended = Arr::pull($sourceOptions, 'rules_append')) {
            $mergedRules = array_values(array_unique(array_merge($options['rules'], $rulesToBeAppended), SORT_REGULAR));
            $options['rules'] = $mergedRules;
        }

        return $options;
    }

    /**
     * Normalize the the given rule expression to an array.
     * @param mixed $rules
     * @return array
     */
    protected function normalizeRules($rules)
    {
        if (empty($rules)) {
            return [];
        }

        if (is_string($rules)) {
            return explode('|', $rules);
        }

        if (is_array($rules)) {
            return array_values(array_unique(Arr::flatten($rules), SORT_REGULAR));
        }

        return $rules;
    }


    /**
     * Get name of the field.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name of the field.
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get dot notation key for fields.
     *
     * @return string
     **/
    public function getNameKey()
    {
        return $this->transformKey($this->name);
    }

    /**
     * Get field options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get single option from options array. Can be used with dot notation ('attr.class').
     *
     * @param string $option
     * @param mixed|null $default
     * @return mixed
     */
    public function getOption($option, $default = null)
    {
        return Arr::get($this->options, $option, $default);
    }

    /**
     * Set field options.
     *
     * @param array $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $this->prepareOptions($options);

        return $this;
    }

    /**
     * Set single option on the field.
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setOption($name, $value)
    {
        Arr::set($this->options, $name, $value);

        return $this;
    }

    /**
     * Get the type of the field.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type of the field.
     *
     * @param mixed $type
     * @return $this
     */
    public function setType($type)
    {
        if ($this->formHelper->getFieldType($type)) {
            $this->type = $type;
        }

        return $this;
    }

    /**
     * @return Form
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Check if the field is rendered.
     *
     * @return bool
     */
    public function isRendered()
    {
        return $this->rendered;
    }

    /**
     * Default options for field.
     *
     * @return array
     */
    protected function getDefaults()
    {
        return [];
    }

    /**
     * Defaults used across all fields.
     *
     * @return array
     */
    private function allDefaults()
    {
        return [
            'wrapper' => ['class' => $this->getConfig('defaults.wrapper_class')],
            'attr' => ['class' => $this->getConfig('defaults.field_class')],
            'help_block' => ['text' => null, 'tag' => 'p', 'attr' => [
                'class' => $this->getConfig('defaults.help_block_class')
            ]],
            'value' => null,
            'default_value' => null,
            'label' => null,
            'label_show' => true,
            'is_child' => false,
            'label_attr' => ['class' => $this->getConfig('defaults.label_class')],
            'errors' => ['class' => $this->getConfig('defaults.error_class')],
            'rules' => [],
            'error_messages' => []
        ];
    }

    /**
     * Get real name of the field without form namespace.
     *
     * @return string
     */
    public function getRealName()
    {
        return $this->getOption('real_name', $this->name);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        if ($this->hasDefault) {
            return $this;
        }

        $closure = $this->valueClosure;

        if ($closure instanceof \Closure) {
            $value = $closure($value ?: null);
        }

        if (!$this->isValidValue($value)) {
            $value = $this->getOption($this->defaultValueProperty);
        }

        $this->options[$this->valueProperty] = $value;

        return $this;
    }

    /**
     * Set the template property on the object.
     *
     * @return void
     */
    private function setTemplate()
    {
        $this->template = $this->getConfig($this->getTemplate(), $this->getTemplate());
    }

    /**
     * Add error class to wrapper if validation errors exist.
     *
     * @return void
     */
    protected function addErrorClass()
    {
        $errors = [];
        if ($this->parent->getRequest()->hasSession()) {
            $errors = $this->parent->getRequest()->session()->get('errors');
        }
        $errorBag = $this->parent->getErrorBag();

        if ($errors && $errors->hasBag($errorBag) && $errors->getBag($errorBag)->has($this->getNameKey())) {
            $fieldErrorClass = $this->getConfig('defaults.field_error_class');
            $fieldClass = $this->getOption('attr.class');

            if ($fieldErrorClass && !Str::contains($fieldClass, $fieldErrorClass)) {
                $fieldClass .= ' ' . $fieldErrorClass;
                $this->setOption('attr.class', $fieldClass);
            }

            $wrapperErrorClass = $this->getConfig('defaults.wrapper_error_class');
            $wrapperClass = $this->getOption('wrapper.class');

            if ($wrapperErrorClass && $this->getOption('wrapper') && !Str::contains($wrapperClass, $wrapperErrorClass)) {
                $wrapperClass .= ' ' . $wrapperErrorClass;
                $this->setOption('wrapper.class', $wrapperClass);
            }
        }
    }

    /**
     * Merge all defaults with field specific defaults and set template if passed.
     *
     * @param array $options
     */
    protected function setDefaultOptions(array $options = [])
    {
        $this->options = $this->formHelper->mergeOptions($this->allDefaults(), $this->getDefaults());
        $this->options = $this->prepareOptions($options);

        $defaults = $this->setDefaultClasses($options);
        $this->options = $this->formHelper->mergeOptions($this->options, $defaults);

        $this->setupLabel();
    }

    /**
     * Creates default wrapper classes for the form element.
     *
     * @param array $options
     * @return array
     */
    protected function setDefaultClasses(array $options = [])
    {
        $wrapper_class = $this->getConfig('defaults.' . $this->type . '.wrapper_class', '');
        $label_class = $this->getConfig('defaults.' . $this->type . '.label_class', '');
        $field_class = $this->getConfig('defaults.' . $this->type . '.field_class', '');

        $defaults = [];
        if ($wrapper_class && !Arr::get($options, 'wrapper.class')) {
            $defaults['wrapper']['class'] = $wrapper_class;
        }
        if ($label_class && !Arr::get($options, 'label_attr.class')) {
            $defaults['label_attr']['class'] = $label_class;
        }
        if ($field_class && !Arr::get($options, 'attr.class')) {
            $defaults['attr']['class'] = $field_class;
        }
        return $defaults;
    }

    /**
     * Setup the label for the form field.
     *
     * @return void
     */
    protected function setupLabel()
    {
        if ($this->getOption('label') !== null) {
            return;
        }

        if ($template = $this->parent->getTranslationTemplate()) {
            $label = str_replace(
                ['{name}', '{type}'],
                [$this->getRealName(), 'label'],
                $template
            );
        } elseif ($langName = $this->parent->getLanguageName()) {
            $label = sprintf('%s.%s', $langName, $this->getRealName());
        } else {
            $label = $this->getRealName();
        }

        $this->setOption('label', $this->formHelper->formatLabel($label));
    }

    /**
     * Check if fields needs label.
     *
     * @return bool
     */
    protected function needsLabel()
    {
        // If field is <select> and child of choice, we don't need label for it
        $isChildSelect = $this->type == 'select' && $this->getOption('is_child') === true;

        if ($this->type == 'hidden' || $isChildSelect) {
            return false;
        }

        return true;
    }

    /**
     * Disable field.
     *
     * @return $this
     */
    public function disable()
    {
        $this->setOption('attr.disabled', 'disabled');

        return $this;
    }

    /**
     * Enable field.
     *
     * @return $this
     */
    public function enable()
    {
        Arr::forget($this->options, 'attr.disabled');

        return $this;
    }

    /**
     * Get validation rules for a field if any with label for attributes.
     *
     * @return array|null
     */
    public function getValidationRules()
    {
        $rules = $this->getOption('rules', []);
        $name = $this->getNameKey();
        $messages = $this->getOption('error_messages', []);
        $formName = $this->parent->getNameKey();

        if ($messages && $formName) {
            $newMessages = [];
            foreach ($messages as $messageKey => $message) {
                $messageKey = sprintf('%s.%s', $formName, $messageKey);
                $newMessages[$messageKey] = $message;
            }
            $messages = $newMessages;
        }

        if (!$rules) {
            return (new Rules([]))->setFieldName($this->getNameKey());
        }

        return (new Rules(
            [$name => $rules],
            [$name => $this->getOption('label')],
            $messages
        ))->setFieldName($this->getNameKey());
    }

    /**
     * Get this field's attributes, probably just one.
     *
     * @return array
     */
    public function getAllAttributes()
    {
        return [$this->getNameKey()];
    }

    /**
     * Get value property.
     *
     * @param mixed|null $default
     * @return mixed
     */
    public function getValue($default = null)
    {
        return $this->getOption($this->valueProperty, $default);
    }

    /**
     * Get default value property.
     *
     * @param mixed|null $default
     * @return mixed
     */
    public function getDefaultValue($default = null)
    {
        return $this->getOption($this->defaultValueProperty, $default);
    }

    /**
     * Check if provided value is valid for this type.
     *
     * @return bool
     */
    protected function isValidValue($value)
    {
        return $value !== null;
    }

    /**
     * Method initFilters used to initialize filters
     * from field options and bind it to the same.
     *
     * @return $this
     */
    protected function initFilters()
    {
        // If override status is set in field options to true
        // we will change filtersOverride property value to true
        // so we can override existing filters with registered
        // alias/name in addFilter method.
        $overrideStatus = $this->getOption('filters_override', false);
        if ($overrideStatus) {
            $this->setFiltersOverride(true);
        }

        // Get filters and bind it to field.
        $filters = $this->getOption('filters', []);
        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }

        return $this;
    }

    /**
     * Method setFilters used to set filters to current filters property.
     *
     * @param  array $filters
     *
     * @return \Kris\LaravelFormBuilder\Fields\FormField
     */
    public function setFilters(array $filters)
    {
        $this->clearFilters();
        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }

        return $this;
    }

    /**
     * Method getFilters returns array of binded filters
     * if there are any binded. Otherwise empty array.
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param  string|FilterInterface $filter
     *
     * @return \Kris\LaravelFormBuilder\Fields\FormField
     *
     * @throws FilterAlreadyBindedException
     */
    public function addFilter($filter)
    {
        // Resolve filter object from string/object or throw Ex.
        $filterObj = FilterResolver::instance($filter);

        // If filtersOverride is allowed we will override filter
        // with same alias/name if there is one with new resolved filter.
        if ($this->getFiltersOverride()) {
            if ($key = array_search($filterObj->getName(), $this->getFilters())) {
                $this->filters[$key] = $filterObj;
            } else {
                $this->filters[$filterObj->getName()] = $filterObj;
            }
        } else {
            // If filtersOverride is disabled and we found
            // equal alias defined we will throw Ex.
            if (array_key_exists($filterObj->getName(), $this->getFilters())) {
                $ex = new FilterAlreadyBindedException($filterObj->getName(), $this->getName());
                throw $ex;
            }

            // Filter with resolvedFilter alias/name doesn't exist
            // so we will bind it as new one to field.
            $this->filters[$filterObj->getName()] = $filterObj;
        }

        return $this;
    }

    /**
     * Method removeFilter used to remove filter by provided alias/name.
     *
     * @param  string $name
     *
     * @return \Kris\LaravelFormBuilder\Fields\FormField
     */
    public function removeFilter($name)
    {
        $filters = $this->getFilters();
        if (array_key_exists($name, $filters)) {
            unset($filters[$name]);
            $this->filters = $filters;
        }

        return $this;
    }

    /**
     * Method removeFilters used to remove filters by provided aliases/names.
     *
     * @param  array $filterNames
     *
     * @return \Kris\LaravelFormBuilder\Fields\FormField
     */
    public function removeFilters(array $filterNames)
    {
        $filters = $this->getFilters();
        foreach ($filterNames as $filterName) {
            if (array_key_exists($filterName, $filters)) {
                unset($filters[$filterName]);
                $this->filters = $filters;
            }
        }

        return $this;
    }

    /**
     * Method clearFilters used to empty current filters property.
     *
     * @return \Kris\LaravelFormBuilder\Fields\FormField
     */
    public function clearFilters()
    {
        $this->filters = [];
        return $this;
    }

    /**
     * Method used to set FiltersOverride status to provided value.
     *
     * @param $status
     *
     * @return \Kris\LaravelFormBuilder\Fields\FormField
     */
    public function setFiltersOverride($status)
    {
        $this->filtersOverride = $status;
        return $this;
    }

    /**
     * @return bool
     */
    public function getFiltersOverride()
    {
        return $this->filtersOverride;
    }

    /**
     * Method used to set Unfiltered/Unmutated field value.
     * Method is called before field value mutating starts - request value filtering.
     *
     * @param mixed $value
     *
     * @return \Kris\LaravelFormBuilder\Fields\FormField
     */
    public function setRawValue($value)
    {
        $this->rawValue = $value;
        return $this;
    }

    /**
     * Returns unfiltered raw value of field.
     *
     * @return mixed
     */
    public function getRawValue()
    {
        return $this->rawValue;
    }

    /**
     * Get config from the form.
     *
     * @return mixed
     */
    private function getConfig($key = null, $default = null)
    {
        return $this->parent->getConfig($key, $default);
    }
}
