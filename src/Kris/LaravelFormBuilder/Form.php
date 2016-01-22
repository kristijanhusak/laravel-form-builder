<?php namespace Kris\LaravelFormBuilder;

use Illuminate\Contracts\Validation\Factory as ValidatorFactory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
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
     * Model to use
     *
     * @var mixed
     */
    protected $model = [];

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
     * Enable html5 validation
     *
     * @var bool
     */
    protected $clientValidationEnabled = true;

    /**
     * Name of the parent form if any
     *
     * @var string|null
     */
    protected $name = null;

    /**
     * @var FormBuilder
     */
    protected $formBuilder;

    /**
     * @var ValidatorFactory
     */
    protected $validatorFactory;

    /**
     * @var Validator
     */
    protected $validator = null;

    /**
     * @var Request
     */
    protected $request;

    /**
     * List of fields to not render
     *
     * @var array
     **/
    protected $exclude = [];

    /**
     * Are form being rebuilt?
     *
     * @var bool
     */
    protected $rebuilding = false;

    /**
     * @var string
     */
    protected $templatePrefix;

    /**
     * @var string
     */
    protected $languageName;

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
     *
     * @return $this
     */
    public function rebuildForm()
    {
        $this->rebuilding = true;
        // If form is plain, buildForm method is empty, so we need to take
        // existing fields and add them again
        if (get_class($this) === 'Kris\LaravelFormBuilder\Form') {
            foreach ($this->fields as $name => $field) {
                // Remove any temp variables added in previous instance
                $options = array_except($field->getOptions(), 'tmp');
                $this->add($name, $field->getType(), $options);
            }
        } else {
            $this->buildForm();
        }
        $this->rebuilding = false;

        return $this;
    }

    /**
     * Create the FormField object
     *
     * @param string $name
     * @param string $type
     * @param array  $options
     * @return FormField
     */
    protected function makeField($name, $type = 'text', array $options = [])
    {
        $this->setupFieldOptions($name, $options);

        $fieldName = $this->getFieldName($name);

        $fieldType = $this->getFieldType($type);

        return new $fieldType($fieldName, $type, $this, $options);
    }

    /**
     * Create a new field and add it to the form
     *
     * @param string $name
     * @param string $type
     * @param array  $options
     * @param bool   $modify
     * @return $this
     */
    public function add($name, $type = 'text', array $options = [], $modify = false)
    {
        if (!$name || trim($name) == '') {
            throw new \InvalidArgumentException(
                'Please provide valid field name for class ['. get_class($this) .']'
            );
        }

        if ($this->rebuilding && !$this->has($name)) {
            return $this;
        }

        $this->addField($this->makeField($name, $type, $options), $modify);

        return $this;
    }

    /**
     * Add a FormField to the form's fields
     *
     * @param FormField $field
     * @return $this
     */
    protected function addField(FormField $field, $modify = false)
    {
        if (!$modify && !$this->rebuilding) {
            $this->preventDuplicate($field->getRealName());
        }


        if ($field->getType() == 'file') {
            $this->formOptions['files'] = true;
        }

        $this->fields[$field->getRealName()] = $field;

        return $this;
    }

    /**
     * Add field before another field
     *
     * @param string  $name         Name of the field before which new field is added
     * @param string  $fieldName    Field name which will be added
     * @param string  $type
     * @param array   $options
     * @param boolean $modify
     * @return $this
     */
    public function addBefore($name, $fieldName, $type = 'text', $options = [], $modify = false)
    {
        $offset = array_search($name, array_keys($this->fields));

        $beforeFields = array_slice($this->fields, 0, $offset);
        $afterFields = array_slice($this->fields, $offset);

        $this->fields = $beforeFields;

        $this->add($fieldName, $type, $options, $modify);

        $this->fields += $afterFields;

        return $this;
    }

    /**
     * Add field before another field
     * @param string  $name         Name of the field after which new field is added
     * @param string  $fieldName    Field name which will be added
     * @param string  $type
     * @param array   $options
     * @param boolean $modify
     * @return $this
     */
    public function addAfter($name, $fieldName, $type = 'text', $options = [], $modify = false)
    {
        $offset = array_search($name, array_keys($this->fields));

        $beforeFields = array_slice($this->fields, 0, $offset + 1);
        $afterFields = array_slice($this->fields, $offset + 1);

        $this->fields = $beforeFields;

        $this->add($fieldName, $type, $options, $modify);

        $this->fields += $afterFields;

        return $this;
    }

    /**
     * Take another form and add it's fields directly to this form
     * @param mixed   $class        Form to merge
     * @param array   $options
     * @param boolean $modify
     * @return $this
     */
    public function compose($class, array $options = [], $modify = false)
    {
        $options['class'] = $class;

        // If we pass a ready made form just extract the fields
        if ($class instanceof Form) {
            $fields = $class->getFields();
        } elseif ($class instanceof Fields\ChildFormType) {
            $fields = $class->getForm()->getFields();
        } elseif (is_string($class)) {
            // If its a string of a class make it the usual way
            $options['model'] = $this->model;
            $options['name'] = $this->name;

            $form = $this->formBuilder->create($class, $options);
            $fields = $form->getFields();
        } else {
            throw new \InvalidArgumentException(
                "[{$class}] is invalid. Please provide either a full class name, Form or ChildFormType"
            );
        }

        foreach ($fields as $field) {
            $this->addField($field, $modify);
        }

        return $this;
    }

    /**
     * Remove field with specified name from the form
     *
     * @param $name
     * @return $this
     */
    public function remove($name)
    {
        if ($this->has($name)) {
            unset($this->fields[$name]);
        }

        return $this;
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
     * Renders the rest of the form up until the specified field name
     *
     * @param string $field_name
     * @param bool   $showFormEnd
     * @param bool   $showFields
     * @return string
     */
    public function renderUntil($field_name, $showFormEnd = true, $showFields = true)
    {
        if (!$this->has($field_name)) {
            $this->fieldDoesNotExist($field_name);
        }

        $fields = $this->getUnrenderedFields();

        $i = 1;
        foreach ($fields as $key => $value) {
            if ($value->getRealName() == $field_name) {
                break;
            }
            $i++;
        }

        $fields = array_slice($fields, 0, $i, true);

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

        $this->fieldDoesNotExist($name);
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
     * Set single form option on form
     *
     * @param string $option
     * @param mixed $value
     *
     * @return $this
     */
    public function setFormOption($option, $value)
    {
        $this->formOptions[$option] = $value;

        return $this;
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
        $this->checkIfNamedForm();
        $this->pullFromOptions('data', 'addData');
        $this->pullFromOptions('model', 'setupModel');
        $this->pullFromOptions('errors_enabled', 'setErrorsEnabled');
        $this->pullFromOptions('client_validation', 'setClientValidationEnabled');
        $this->pullFromOptions('template_prefix', 'setTemplatePrefix');
        $this->pullFromOptions('language_name', 'setLanguageName');

        return $this;
    }

    /**
     * Get an option from provided options and call method with that value
     *
     * @param $name
     * @param $method
     */
    protected function pullFromOptions($name, $method)
    {
        if (array_get($this->formOptions, $name) !== null) {
            $this->{$method}(array_pull($this->formOptions, $name));
        }
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
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @param bool $rebuild
     *
     * @return $this
     */
    public function setName($name, $rebuild = true)
    {
        $this->name = $name;

        if ($rebuild) {
            $this->rebuildForm();
        }

        return $this;
    }

    /**
     * Get model that is bind to form object
     *
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set model to form object
     *
     * @param mixed $model
     * @return $this
     * @deprecated deprecated since 1.6.31, will be removed in 1.7 - pass model as option when creating a form
     */
    public function setModel($model)
    {
        $this->model = $model;

        $this->setupNamedModel();

        $this->rebuildForm();

        return $this;
    }

    /**
     * Setup model for form, add namespace if needed for child forms
     * @return $this
     */
    protected function setupModel($model)
    {
        $this->model = $model;

        $this->setupNamedModel();

        return $this;
    }

    /**
     * Get all fields
     *
     * @return FormField[]
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
     * Enable or disable showing errors under fields
     *
     * @param boolean $enabled
     * @return $this
     */
    public function setErrorsEnabled($enabled)
    {
        $this->showFieldErrors = (boolean) $enabled;

        return $this;
    }

    /**
     * Is client validation enabled?
     *
     * @return boolean
     */
    public function clientValidationEnabled()
    {
        return $this->clientValidationEnabled;
    }

    /**
     * Enable/disable client validation
     *
     * @param boolean $enable
     * @return $this
     */
    public function setClientValidationEnabled($enable)
    {
        $this->clientValidationEnabled = (boolean) $enable;

        return $this;
    }

    /**
     * Add any aditional data that field needs (ex. array of choices)
     *
     * @deprecated deprecated since 1.6.20, will be removed in 1.7 - use 3rd param on create, or 2nd on plain method to pass data
     * will be switched to protected in 1.7
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
    public function getData($name = null, $default = null)
    {
        if (is_null($name)) {
            return $this->data;
        }

        return array_get($this->data, $name, $default);
    }

    /**
     * Add multiple peices of data at once
     *
     * @deprecated deprecated since 1.6.12, will be removed in 1.7 - use 3rd param on create, or 2nd on plain method to pass data
     * will be switched to protected in 1.7
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
        return $this->request;
    }

    /**
     * Set request on form
     *
     * @param Request $request
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get template prefix that is prepended to all template paths
     *
     * @return string
     */
    public function getTemplatePrefix()
    {
        if ($this->templatePrefix !== null) {
            return $this->templatePrefix;
        }

        return $this->formHelper->getConfig('template_prefix');
    }

    /**
     * Set a template prefix for the form and its fields
     *
     * @param string $prefix
     * @return $this
     */
    public function setTemplatePrefix($prefix)
    {
        $this->templatePrefix = (string) $prefix;

        return $this;
    }

    /**
     * Get the language name
     *
     * @return string
     */
    public function getLanguageName()
    {
        return $this->languageName;
    }

    /**
     * Set a language name, used as prefix for translated strings
     *
     * @param string $prefix
     * @return $this
     */
    public function setLanguageName($prefix)
    {
        $this->languageName = (string) $prefix;

        return $this;
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

        $this->setupNamedModel();

        return $this->formHelper->getView()
            ->make($this->getTemplate())
            ->with(compact('showStart', 'showFields', 'showEnd'))
            ->with('formOptions', $formOptions)
            ->with('fields', $fields)
            ->with('model', $this->getModel())
            ->with('exclude', $this->exclude)
            ->with('form', $this)
            ->render();
    }

    /**
     * Get template from options if provided, otherwise fallback to config
     *
     * @return mixed
     */
    protected function getTemplate()
    {
        return $this->getTemplatePrefix() . $this->getFormOption('template', $this->formHelper->getConfig('form'));
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

        return $fieldType;
    }

    /**
     * Check if form is named form
     */
    protected function checkIfNamedForm()
    {
        if ($this->getFormOption('name')) {
            $this->name = array_pull($this->formOptions, 'name', $this->name);
        }
    }

    /**
     * Set up options on single field depending on form options
     *
     * @param string $name
     * @param $options
     */
    protected function setupFieldOptions($name, &$options)
    {
        $options['real_name'] = $name;
    }

    /**
     * Set namespace to model if form is named so the data is bound properly
     * Returns true if model is changed, otherwise false
     *
     * @return bool
     */
    protected function setupNamedModel()
    {
        if (!$this->getModel() || !$this->getName()) {
            return false;
        }

        $dotName = $this->formHelper->transformToDotSyntax($this->getName());
        $model = $this->formHelper->convertModelToArray($this->getModel());

        if (!array_get($model, $dotName)) {
            $newModel = [];
            array_set($newModel, $dotName, $model);
            $this->model = $newModel;

            return true;
        }

        return false;
    }


    /**
     * Set form builder instance on helper so we can use it later
     *
     * @param FormBuilder $formBuilder
     * @return $this
     */
    public function setFormBuilder(FormBuilder $formBuilder)
    {
        $this->formBuilder = $formBuilder;

        return $this;
    }

    /**
     * @return FormBuilder
     */
    public function getFormBuilder()
    {
        return $this->formBuilder;
    }

    /**
     * @param ValidatorFactory $validator
     * @return $this
     */
    public function setValidator(ValidatorFactory $validator)
    {
        $this->validatorFactory = $validator;

        return $this;
    }

    /**
     * Exclude some fields from rendering
     *
     * @return $this
     */
    public function exclude(array $fields)
    {
        $this->exclude = array_merge($this->exclude, $fields);

        return $this;
    }


    /**
     * If form is named form, modify names to be contained in single key (parent[child_field_name])
     *
     * @param string $name
     * @return string
     */
    protected function getFieldName($name)
    {
        if ($this->getName() !== null) {
            return $this->getName().'['.$name.']';
        }

        return $name;
    }

    /**
     * Disable all fields in a form
     */
    public function disableFields()
    {
        foreach ($this->fields as $field) {
            $field->disable();
        }
    }

    /**
     * Enable all fields in a form
     */
    public function enableFields()
    {
        foreach ($this->fields as $field) {
            $field->enable();
        }
    }

    /**
     * Validate the form
     *
     * @param array $validationRules
     * @param array $messages
     * @return Validator
     */
    public function validate($validationRules = [], $messages = [])
    {
        $fieldRules = $this->formHelper->mergeFieldsRules($this->fields);
        $rules = array_merge($fieldRules['rules'], $validationRules);

        $this->validator = $this->validatorFactory->make($this->getRequest()->all(), $rules, $messages);
        $this->validator->setAttributeNames($fieldRules['attributes']);

        return $this->validator;
    }

    /**
     * Get validatdion rules for the form
     *
     * @param array $overrideRules
     * @return array
     */
    public function getRules($overrideRules = [])
    {
        $fieldRules = $this->formHelper->mergeFieldsRules($this->fields);

        return array_merge($fieldRules['rules'], $overrideRules);
    }

    /**
     * Check if the form is valid
     *
     * @return bool
     */
    public function isValid()
    {
        if (!$this->validator) {
            $this->validate();
        }

        return !$this->validator->fails();
    }

    /**
     * Get validation errors
     *
     * @return array
     */
    public function getErrors()
    {
        if (!$this->validator || !$this->validator instanceof Validator) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Form %s was not validated. To validate it, call "isValid" method before retrieving the errors',
                    get_class($this)
                )
            );
        }

        return $this->validator->getMessageBag()->getMessages();
    }

    /**
     * Throw an exception indicating a field does not exist on the class
     *
     * @param string $name
     * @throws \InvalidArgumentException
     */
    protected function fieldDoesNotExist($name)
    {
        throw new \InvalidArgumentException('Field ['.$name.'] does not exist in '.get_class($this));
    }
}
