<?php  namespace Kris\LaravelFormBuilder\Fields;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Kris\LaravelFormBuilder\Form;
use Kris\LaravelFormBuilder\FormHelper;

/**
 * Class FormField
 *
 * @package Kris\LaravelFormBuilder\Fields
 */
abstract class FormField
{
    /**
     * Name of the field
     *
     * @var
     */
    protected $name;

    /**
     * Type of the field
     *
     * @var
     */
    protected $type;

    /**
     * All options for the field
     *
     * @var
     */
    protected $options = [];

    /**
     * Is field rendered
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
     * Name of the property for value setting
     *
     * @var string
     */
    protected $valueProperty = 'value';

    /**
     * Name of the property for default value
     *
     * @var string
     */
    protected $defaultValueProperty = 'default_value';

    /**
     * Is default value set?
     * @var bool
     */
    protected $hasDefault = false;

    /**
     * @var \Closure|null
     */
    protected $valueClosure = null;

    /**
     * @param             $name
     * @param             $type
     * @param Form        $parent
     * @param array       $options
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
    }

    protected function setupValue()
    {
        $value = $this->getOption($this->valueProperty);
        $isChild = $this->getOption('is_child');

        if ($value instanceof \Closure) {
            $this->valueClosure = $value;
        }

        if (($value === null || $value instanceof \Closure) && !$isChild) {
            $this->setValue($this->getModelValueAttribute($this->parent->getModel(), $this->name));
        } elseif (!$isChild) {
            $this->hasDefault = true;
        }
    }

    /**
     * Get the template, can be config variable or view path
     *
     * @return string
     */
    abstract protected function getTemplate();

    /**
     * @param array $options
     * @param bool  $showLabel
     * @param bool  $showField
     * @param bool  $showError
     * @return string
     */
    public function render(array $options = [], $showLabel = true, $showField = true, $showError = true)
    {
        $val = null;
        $value = array_get($options, $this->valueProperty);
        $defaultValue = array_get($options, $this->defaultValueProperty);

        if ($showField) {
            $this->rendered = true;
        }

        // Check if default value is passed to render function from view.
        // If it is, we save it to a variable and then override it before
        // rendering the view
        if ($value) {
            $val = $value;
        } elseif ($defaultValue && !$this->getOption($this->valueProperty)) {
            $val = $defaultValue;
        }

        $options = $this->prepareOptions($options);

        if ($val) {
            $options[$this->valueProperty] = $val;
        }

        if (!$this->needsLabel($options)) {
            $showLabel = false;
        }

        if ($showError) {
            $showError = $this->parent->haveErrorsEnabled();
        }

        return $this->formHelper->getView()->make(
            $this->template,
            [
                'name' => $this->name,
                'nameKey' => $this->getNameKey(),
                'type' => $this->type,
                'options' => $options,
                'showLabel' => $showLabel,
                'showField' => $showField,
                'showError' => $showError
            ]
        )->render();
    }

    /**
     * Get the attribute value from the model by name
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
            return array_get($model, $transformedName);
        }
    }

    /**
     * Transform array like syntax to dot syntax
     *
     * @param $key
     * @return mixed
     */
    protected function transformKey($key)
    {
        return str_replace(['.', '[]', '[', ']'], ['_', '', '.', ''], $key);
    }

    /**
     * Prepare options for rendering
     *
     * @param array $options
     * @return array
     */
    protected function prepareOptions(array $options = [])
    {
        $helper = $this->formHelper;

        if (array_get($this->options, 'template') !== null) {
            $this->template = array_pull($this->options, 'template');
        }

        $options = $helper->mergeOptions($this->options, $options);

        if ($this->parent->haveErrorsEnabled()) {
            $this->addErrorClass($options);
        }

        if ($this->getOption('attr.multiple')) {
            $this->name = $this->name.'[]';
        }

        if ($this->getOption('required') === true) {
            $options['label_attr']['class'] .= ' ' . $this->formHelper
                ->getConfig('defaults.required_class', 'required');
            $options['attr']['required'] = 'required';
        }

        $options['wrapperAttrs'] = $helper->prepareAttributes($options['wrapper']);
        $options['errorAttrs'] = $helper->prepareAttributes($options['errors']);

        if ($options['is_child']) {
            $options['labelAttrs'] = $helper->prepareAttributes($options['label_attr']);
        }

        if ($options['help_block']['text']) {
            $options['help_block']['helpBlockAttrs'] = $helper->prepareAttributes(
                $options['help_block']['attr']
            );
        }

        return $options;
    }

    /**
     * Get name of the field
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name of the field
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
     * Get dot notation key for fields
     *
     * @return string
     **/
    public function getNameKey()
    {
        return $this->transformKey($this->name);
    }

    /**
     * Get field options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get single option from options array. Can be used with dot notation ('attr.class')
     *
     * @param        $option
     * @param string $default
     *
     * @return mixed
     */
    public function getOption($option, $default = null)
    {
        return array_get($this->options, $option, $default);
    }

    /**
     * Set field options
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
     * Set single option on the field
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setOption($name, $value)
    {
        array_set($this->options, $name, $value);

        return $this;
    }

    /**
     * Get the type of the field
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type of the field
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
     * Check if the field is rendered
     *
     * @return bool
     */
    public function isRendered()
    {
        return $this->rendered;
    }

    /**
     * Default options for field
     *
     * @return array
     */
    protected function getDefaults()
    {
        return [];
    }

    /**
     * Defaults used across all fields
     *
     * @return array
     */
    private function allDefaults()
    {
        return [
            'wrapper' => ['class' => $this->formHelper->getConfig('defaults.wrapper_class')],
            'attr' => ['class' => $this->formHelper->getConfig('defaults.field_class')],
            'help_block' => ['text' => null, 'tag' => 'p', 'attr' => [
                'class' => $this->formHelper->getConfig('defaults.help_block_class')
            ]],
            'value' => null,
            'default_value' => null,
            'label' => $this->formHelper->formatLabel($this->getRealName()),
            'is_child' => false,
            'label_attr' => ['class' => $this->formHelper->getConfig('defaults.label_class')],
            'errors' => ['class' => $this->formHelper->getConfig('defaults.error_class')]
        ];
    }

    /**
     * Get real name of the field without form namespace
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
    protected function setValue($value)
    {
        if ($this->hasDefault) {
            return $this;
        }

        $closure = $this->valueClosure;

        if ($closure instanceof \Closure) {
            $value = $closure($value ?: null);
        }

        if ($value === null || $value === false) {
            $value = $this->getOption($this->defaultValueProperty);
        }

        $this->options[$this->valueProperty] = $value;

        return $this;
    }

    /**
     * Set the template property on the object
     */
    private function setTemplate()
    {
        $this->template = $this->formHelper->getConfig($this->getTemplate(), $this->getTemplate());
    }

    /**
     * Add error class to wrapper if validation errors exist
     *
     * @param $options
     */
    protected function addErrorClass(&$options)
    {
        $errors = $this->formHelper->getRequest()->getSession()->get('errors');

        if ($errors && $errors->has($this->getNameKey())) {
            $errorClass = $this->formHelper->getConfig('defaults.wrapper_error_class');

            if ($options['wrapper'] && !str_contains($options['wrapper']['class'], $errorClass)) {
                $options['wrapper']['class'] .= ' '.$errorClass;
            }
        }

        return $options;
    }


    /**
     * Merge all defaults with field specific defaults and set template if passed
     *
     * @param array $options
     */
    protected function setDefaultOptions(array $options = [])
    {
        $this->options = $this->formHelper->mergeOptions($this->allDefaults(), $this->getDefaults());
        $this->options = $this->prepareOptions($options);
    }

    /**
     * Check if fields needs label
     *
     * @param array $options
     * @return bool
     */
    protected function needsLabel(array $options = [])
    {
        // If field is <select> and child of choice, we don't need label for it
        $isChildSelect = $this->type == 'select' && array_get($options, 'is_child') === true;

        if ($this->type == 'hidden' || $isChildSelect) {
            return false;
        }

        return true;
    }

    /**
     * Disable field
     *
     * @return $this
     */
    public function disable()
    {
        $this->setOption('attr.disabled', 'disabled');

        return $this;
    }

    /**
     * Enable field
     *
     * @return $this
     */
    public function enable()
    {
        array_forget($this->options, 'attr.disabled');

        return $this;
    }
}
