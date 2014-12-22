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
        'url' => null
    ];

    /**
     * Additional data which can be used to build fields
     *
     * @var array
     */
    protected $data = [];

    /**
     * Should errors for each field be shown when called form($form) or form_rest($form) ?
     *
     * @var bool
     */
    protected $showFieldErrors = true;

    /**
     * Is this form instance child of another form
     *
     * @var bool
     */
    protected $isChildForm = false;

    /**
     * Name of the parent form if any
     *
     * @var null
     */
    protected $childFormName = null;

    /**
     * Build the form
     *
     * @return mixed
     */
    public function buildForm()
    {
    }

    /**
     * Rebuild the form from scratch
     */
    public function rebuildForm()
    {
        $this->fields = [];
        return $this->buildForm();
    }

    /**
     * Add a single field to the form
     *
     * @param string $name
     * @param string $type
     * @param array  $options
     * @param bool   $modify
     * @return $this
     */
    public function add($name, $type = 'text', array $options = [], $modify = false)
    {
        if (!$modify) {
            $this->preventDuplicate($name);
        }

        $this->setupFieldOptions($name, $options);

        $fieldName = $this->getFieldName($name);

        $fieldType = $this->getFieldType($type);

        $this->fields[$name] = new $fieldType($fieldName, $type, $this, $options);

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
        // If we don't want to overwrite options, we merge them with old options
        if ($overwriteOptions === false && $this->has($name)) {
            $options = $this->formHelper->mergeOptions(
                $this->getField($name)->getOptions(),
                $options
            );
        }

        return $this->add($name, $type, $options, true);
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
     * @param bool $showFormEnd
     * @param bool $showFields
     * @return string
     */
    public function renderRest($showFormEnd = true, $showFields = true)
    {
        $fields = $this->getUnrenderedFields();

        return $this->render([], $fields, false, $showFields, $showFormEnd);
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

        throw new \InvalidArgumentException(
            'Field with name ['. $name .'] does not exist in class '.get_class($this)
        );
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
     * Get single form option
     *
     * @param string $option
     * @param $default
     * @return mixed
     */
    public function getFormOption($option, $default = null)
    {
        return array_get($this->formOptions, $option, $default);
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

        $this->getDataFromOptions();

        $this->checkIfChildForm();

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
    }

    /**
     * Set the form helper only on first instantiation
     *
     * @param FormHelper $formHelper
     * @return $this
     */
    public function setFormHelper(FormHelper $formHelper)
    {
        $this->formHelper = $formHelper;

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
     * Should form errors be shown under every field ?
     *
     * @return bool
     */
    public function haveErrorsEnabled()
    {
        return $this->showFieldErrors;
    }

    /**
     * Is form child of another form ?
     *
     * @return bool
     */
    public function isChildForm()
    {
        return $this->isChildForm;
    }

    /**
     * Add any aditional data that field needs (ex. array of choices)
     *
     * @param string $name
     * @param mixed $data
     */
    public function setData($name, $data)
    {
        $this->data[$name] = $data;
    }

    /**
     * Get single additional data
     *
     * @param string $name
     * @param null   $default
     * @return mixed
     */
    public function getData($name, $default = null)
    {
        return array_get($this->data, $name, $default);
    }

    /**
     * Add multiple peices of data at once
     *
     * @param $data
     * @return $this
     **/
    public function addData(array $data)
    {
        foreach ($data as $key => $value) {
            $this->setData($key, $value);
        }

        return $this;
    }

    /**
     * Get current request
     *
     * @return \Illuminate\Http\Request
     */
    public function getRequest()
    {
        return $this->formHelper->getRequest();
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
            ->render();
    }

    /**
     * Get the model from the options
     */
    private function getModelFromOptions()
    {
        if (array_get($this->formOptions, 'model') instanceof Model) {
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
    protected function preventDuplicate($name)
    {
        if ($this->has($name)) {
            throw new \InvalidArgumentException('Field ['.$name.'] already exists in the form '.get_class($this));
        }
    }

    /**
     * @param string $type
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

    /**
     * Check if form is child of another form
     */
    protected function checkIfChildForm()
    {
        if ($this->getFormOption('is_child')) {
            $this->isChildForm = array_pull($this->formOptions, 'is_child');
            $this->childFormName = array_pull($this->formOptions, 'name');
        }
    }

    /**
     * If form is child of another form, modify names to be contained in single key (parent[child_field_name])
     *
     * @param string $name
     * @return string
     */
    protected function getFieldName($name)
    {
        if ($this->isChildForm && $this->childFormName !== null) {
            return $this->childFormName.'['.$name.']';
        }

        return $name;
    }

    /**
     * Set up options on single field depending on form options
     *
     * @param string $name
     * @param $options
     */
    protected function setupFieldOptions($name, &$options)
    {
        if (!$this->isChildForm()) {
            return;
        }

        $options['real_name'] = $name;

        if (!isset($options['label'])) {
            $options['label'] = $name;
        }
    }

    /**
     * Get any data from options and remove it
     */
    protected function getDataFromOptions()
    {
        if (array_get($this->formOptions, 'data')) {
            $this->addData(array_pull($this->formOptions, 'data'));
        }
    }
}
