<?php  namespace Kris\LaravelFormBuilder\Fields;

use Kris\LaravelFormBuilder\Form;

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
    protected $options;

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
        $this->setTemplate();
        $this->setDefaultOptions($options);
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
        if ($showField) {
            $this->rendered = true;
        }

        if (!$this->needsLabel()) {
            $showLabel = false;
        }

        $options = $this->prepareOptions($options);

        return $this->parent->getFormHelper()->getView()->make(
            $this->template, [
                'name' => $this->name,
                'type' => $this->type,
                'options' => $options,
                'showLabel' => $showLabel,
                'showField' => $showField,
                'showError' => $showError
            ])->render();
    }

    /**
     * Prepare options for rendering
     *
     * @param array $options
     * @return array
     */
    protected function prepareOptions(array $options = [])
    {
        $formHelper = $this->parent->getFormHelper();

        $options = $formHelper->mergeOptions($this->options, $options);

        $this->addErrorClass($options);

        $options['wrapperAttrs'] = $formHelper->prepareAttributes($options['wrapper']);
        $options['errorAttrs'] = $formHelper->prepareAttributes($options['errors']);

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
     * Get field options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
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
        if ($this->parent->getFormHelper()->getFieldType($type)) {
            $this->type = $type;
        }

        return $this;
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
        $formHelper = $this->parent->getFormHelper();

        return [
            'wrapper' => ['class' => $formHelper->getConfig('defaults.wrapper_class')],
            'attr' => ['class' => $formHelper->getConfig('defaults.field_class')],
            'default_value' => null,
            'label' => $this->name,
            'label_attr' => ['class' => $formHelper->getConfig('defaults.label_class')],
            'errors' => ['class' => $formHelper->getConfig('defaults.error_class')]
        ];
    }

    /**
     * Merge all defaults with field specific defaults and set template if passed
     *
     * @param array $options
     */
    protected function setDefaultOptions(array $options = [])
    {
        $formHelper = $this->parent->getFormHelper();

        $this->options = $formHelper->mergeOptions($this->allDefaults(), $this->getDefaults());
        $this->options = $this->prepareOptions($options);

        if (array_get($this->options, 'template') !== null) {
            $this->template = $this->options['template'];
            unset($this->options['template']);
        }
    }

    /**
     * Set the template property on the object
     */
    private function setTemplate()
    {
        $this->template = $this->parent->getFormHelper()
            ->getConfig($this->getTemplate());
    }

    /**
     * Add error class to wrapper if validation errors exist
     *
     * @param $options
     */
    protected function addErrorClass(&$options)
    {
        $formHelper = $this->parent->getFormHelper();

        $errors = $formHelper->getRequest()->getSession()->get('errors');

        if ($errors && $errors->has($this->name)) {
            $errorClass = $formHelper->getConfig('defaults.wrapper_error_class');

            if (strpos($options['wrapper']['class'], $errorClass) === false) {
                $options['wrapper']['class'] .= ' '.$errorClass;
            }
        }

        return $options;
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

}
