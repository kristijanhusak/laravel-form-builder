<?php namespace Kris\LaravelFormBuilder;

use Illuminate\Database\Eloquent\Model;
use Kris\LaravelFormBuilder\Fields\FormField;

class Form
{

    /**
     * All fields that are added
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Eloquent model to use
     *
     * @var Model
     */
    protected $model = null;

    /**
     * @var FormHelper
     */
    protected $formHelper;

    /**
     * Form options
     *
     * @var array
     */
    protected $formOptions = [
        'method' => 'GET',
        'url' => ''
    ];

    /**
     * Should errors for each field be shown when called form($form) or form_rest($form) ?
     *
     * @var bool
     */
    protected $showFieldErrors = true;

    /**
     * Build the form
     *
     * @return mixed
     */
    public function buildForm()
    {
    }

    /**
     * Add a single field to the form
     *
     * @param string $name
     * @param string $type
     * @param array $options
     * @return $this
     */
    public function add($name, $type = 'text', array $options = [])
    {
        $this->preventDuplicate($name);

        $fieldType = $this->getFieldType($type);

        $this->fields[$name] = new $fieldType($name, $type, $this, $options);

        return $this;
    }

    /**
     * Remove field with specified name from the form
     *
     * @param $name
     */
    public function remove($name)
    {
        if ($this->has($name)) {
            unset($this->fields[$name]);
            return $this;
        }

        throw new \InvalidArgumentException('Field ['.$name.'] does not exist in '.get_class($this));
    }

    /**
     * Modify existing field. If it doesn't exist, it is added to form
     *
     * @param        $name
     * @param string $type
     * @param array  $options
     * @param bool   $overwriteOptions
     * @return Form
     */
    public function modify($name, $type = 'text', array $options = [], $overwriteOptions = false)
    {
        if (!$this->has($name)) {
            return $this->add($name, $type, $options);
        }

        $fieldType = $this->getFieldType($type);

        // If we don't want to overwrite options, we merge them with old options
        if ($overwriteOptions === false) {

            $modifiedOptions = $this->formHelper->mergeOptions(
                $this->getField($name)->getOptions(),
                $options
            );

            $this->fields[$name] = new $fieldType($name, $type, $this, $modifiedOptions);
        } else {
            $this->fields[$name] = new $fieldType($name, $type, $this, $options);
        }

        return $this;
    }

    /**
     * Render full form
     *
     * @param array $options
     * @param bool  $showStart
     * @param bool  $showFields
     * @param bool  $showEnd
     * @return string
     */
    public function renderForm(array $options = [], $showStart = true, $showFields = true, $showEnd = true)
    {
        return $this->render($options, $this->fields, $showStart, $showFields, $showEnd);
    }

    /**
     * Render rest of the form
     *
     * @param array $options
     * @return string
     */
    public function renderRest(array $options = [])
    {
        $fields = $this->getUnrenderedFields();

        return $this->render($options, $fields, false, true, false);
    }

    /**
     * Get single field instance from form object
     *
     * @param $name
     * @return FormField
     */
    public function getField($name)
    {
        if ($this->has($name)) {
            return $this->fields[$name];
        }

        throw new \InvalidArgumentException('field with name ['. $name .'] does not exits.');
    }

    /**
     * Check if form has field
     *
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->fields);
    }

    /**
     * Get all form options
     *
     * @return array
     */
    public function getFormOptions()
    {
        return $this->formOptions;
    }

    /**
     * Set form options
     *
     * @param array $formOptions
     * @return $this
     */
    public function setFormOptions($formOptions)
    {
        $this->formOptions = $this->formHelper->mergeOptions($this->formOptions, $formOptions);

        $this->getModelFromOptions();

        return $this;
    }

    /**
     * Get form http method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->formOptions['method'];
    }

    /**
     * Set form http method
     *
     * @param string $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->formOptions['method'] = $method;

        return $this;
    }

    /**
     * Get form action url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->formOptions['url'];
    }

    /**
     * Set form action url
     *
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->formOptions['url'] = $url;

        return $this;
    }

    /**
     * Get model that is bind to form object
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set model to form object
     *
     * @param Model $model
     * @return $this
     */
    public function setModel(Model $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get all fields
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Get field dynamically
     *
     * @param $name
     * @return FormField
     */
    public function __get($name)
    {
        if ($this->has($name)) {
            return $this->getField($name);
        }

        throw new \InvalidArgumentException('No property ['.$name.'] on '.get_class($this));
    }

    /**
     * Set the form helper only on first instantiation
     *
     * @param FormHelper $formHelper
     * @return $this
     */
    public function setFormHelper(FormHelper $formHelper)
    {
        if ($this->formHelper === null) {
            $this->formHelper = $formHelper;
        }

        return $this;
    }

    /**
     * Get form helper
     *
     * @return FormHelper
     */
    public function getFormHelper()
    {
        return $this->formHelper;
    }

    /**
     * Add custom field
     *
     * @param $name
     * @param $class
     */
    public function addCustomField($name, $class)
    {
        $this->formHelper->addCustomField($name, $class);
    }

    /**
     * Render the form
     *
     * @param $options
     * @param $fields
     * @param boolean $showStart
     * @param boolean $showFields
     * @param boolean $showEnd
     * @return string
     */
    protected function render($options, $fields, $showStart, $showFields, $showEnd)
    {
        $formOptions = $this->formHelper->mergeOptions($this->formOptions, $options);

        return $this->formHelper->getView()
            ->make($this->formHelper->getConfig('form'))
            ->with(compact('showStart', 'showFields', 'showEnd'))
            ->with('formOptions', $formOptions)
            ->with('fields', $fields)
            ->with('model', $this->getModel())
            ->with('showFieldErrors', $this->showFieldErrors)
            ->render();
    }

    /**
     * Get the model from the options
     */
    private function getModelFromOptions()
    {
        if (($model = array_get($this->formOptions, 'model')) instanceof Model) {
            $this->setModel(array_pull($this->formOptions, 'model'));
        }
    }

    /**
     * Get all fields that are not rendered
     *
     * @return array
     */
    protected function getUnrenderedFields()
    {
        $unrenderedFields = [];

        foreach ($this->fields as $field) {
            if (!$field->isRendered()) {
                $unrenderedFields[] = $field;
                continue;
            }
        }

        return $unrenderedFields;
    }

    /**
     * Prevent adding fields with same name
     *
     * @param string $name
     */
    private function preventDuplicate($name)
    {
        if ($this->has($name)) {
            throw new \InvalidArgumentException('Field ['.$name.'] already exists in the form '.get_class($this));
        }
    }

    /**
     * @param $type
     * @return string
     */
    protected function getFieldType($type)
    {
        $fieldType = $this->formHelper->getFieldType($type);

        if ($type == 'file') {
            $this->formOptions['files'] = true;
        }

        return $fieldType;
    }
}
