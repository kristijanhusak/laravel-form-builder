<?php

namespace Kris\LaravelFormBuilder;

use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Contracts\Validation\Factory as ValidatorFactory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Kris\LaravelFormBuilder\Events\AfterFieldCreation;
use Kris\LaravelFormBuilder\Events\AfterFormValidation;
use Kris\LaravelFormBuilder\Events\BeforeFormValidation;
use Kris\LaravelFormBuilder\Fields\FormField;
use Kris\LaravelFormBuilder\Filters\FilterResolver;

class Form
{
    /**
     * All fields that are added.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Model to use.
     *
     * @var mixed
     */
    protected $model = [];

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var FormHelper
     */
    protected $formHelper;

    /**
     * Form options.
     *
     * @var array
     */
    protected $formOptions = [
        'method' => 'GET',
        'url' => null,
        'attr' => [],
    ];

    /**
     * Form specific configuration.
     *
     * @var array
     */
    protected $formConfig = [];

    /**
     * Additional data which can be used to build fields.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Wether errors for each field should be shown when calling form($form) or form_rest($form).
     *
     * @var bool
     */
    protected $showFieldErrors = true;

    /**
     * Enable html5 validation.
     *
     * @var bool
     */
    protected $clientValidationEnabled = true;

    /**
     * Name of the parent form if any.
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
     * List of fields to not render.
     *
     * @var array
     **/
    protected $exclude = [];

    /**
     * Wether the form is beign rebuild.
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
     * @var string
     */
    protected $translationTemplate;

    /**
     * To filter and mutate request values or not.
     *
     * @var bool
     */
    protected $lockFiltering = false;

    /**
     * Define the error bag name for the form.
     *
     * @var string
     */
    protected $errorBag = 'default';

    /**
     * Build the form.
     *
     * @return mixed
     */
    public function buildForm()
    {
    }

    /**
     * Rebuild the form from scratch.
     *
     * @return $this
     */
    public function rebuildForm()
    {
        $this->rebuilding = true;
        // If form is plain, buildForm method is empty, so we need to take
        // existing fields and add them again
        if ($this->isPlain()) {
            foreach ($this->fields as $name => $field) {
                // Remove any temp variables added in previous instance
                $options =  Arr::except($field->getOptions(), 'tmp');
                $this->add($name, $field->getType(), $options);
            }
        } else {
            $this->buildForm();
        }
        $this->rebuilding = false;

        return $this;
    }

    /**
     * @return bool
     */
    protected function isPlain()
    {
        if($this->formBuilder === null) {
            throw new \RuntimeException('FormBuilder is not set');
        }

        return static::class === $this->formBuilder->getFormClass();
    }

    /**
     * Create the FormField object.
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

        $field = new $fieldType($fieldName, $type, $this, $options);

        $this->eventDispatcher->dispatch(new AfterFieldCreation($this, $field));

        return $field;
    }

    /**
     * Create a new field and add it to the form.
     *
     * @param string $name
     * @param string $type
     * @param array  $options
     * @param bool   $modify
     * @return $this
     */
    public function add($name, $type = 'text', array $options = [], $modify = false)
    {
        $this->formHelper->checkFieldName($name, get_class($this));

        if ($this->rebuilding && !$this->has($name)) {
            return $this;
        }

        $this->addField($this->makeField($name, $type, $options), $modify);

        return $this;
    }

    /**
     * Add a FormField to the form's fields.
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
     * Add field before another field.
     *
     * @param string  $name         Name of the field before which new field is added.
     * @param string  $fieldName    Field name which will be added.
     * @param string  $type
     * @param array   $options
     * @param bool $modify
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
     * Add field before another field.
     *
     * @param string  $name         Name of the field after which new field is added.
     * @param string  $fieldName    Field name which will be added.
     * @param string  $type
     * @param array   $options
     * @param bool $modify
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
     * Take another form and add it's fields directly to this form.
     *
     * @param mixed   $class        Form to merge.
     * @param array   $options
     * @param boolean $modify
     * @return $this
     */
    public function compose($class, array $options = [], $modify = false)
    {
        $options['class'] = $class;

        // If we pass a ready made form just extract the fields.
        if ($class instanceof Form) {
            $fields = $class->getFields();
        } elseif ($class instanceof Fields\ChildFormType) {
            $fields = $class->getForm()->getFields();
        } elseif (is_string($class)) {
            // If its a string of a class make it the usual way.
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
     * Remove field with specified name from the form.
     *
     * @param string|string[] $names
     * @return $this
     */
    public function remove($names)
    {
        foreach (is_array($names) ? $names : func_get_args() as $name) {
            if ($this->has($name)) {
                unset($this->fields[$name]);
            }
        }

        return $this;
    }

    /**
     * Take only the given fields from the form.
     *
     * @param string|string[] $fieldNames
     * @return $this
     */
    public function only($fieldNames)
    {
        $newFields = [];

        foreach (is_array($fieldNames) ? $fieldNames : func_get_args() as $fieldName) {
            $newFields[$fieldName] = $this->getField($fieldName);
        }

        $this->fields = $newFields;

        return $this;
    }

    /**
     * Modify existing field. If it doesn't exist, it is added to form.
     *
     * @param string $name
     * @param string $type
     * @param array  $options
     * @param bool   $overwriteOptions
     * @return Form
     */
    public function modify($name, $type = 'text', array $options = [], $overwriteOptions = false)
    {
        // If we don't want to overwrite options, we merge them with old options.
        if ($overwriteOptions === false && $this->has($name)) {
            $options = $this->formHelper->mergeOptions(
                $this->getField($name)->getOptions(),
                $options
            );
        }

        return $this->add($name, $type, $options, true);
    }

    /**
     * Render full form.
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
     * Render rest of the form.
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
     * Renders the rest of the form up until the specified field name.
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
     * Get single field instance from form object.
     *
     * @param string $name
     * @return FormField
     */
    public function getField($name)
    {
        if ($this->has($name)) {
            return $this->fields[$name];
        }

        $this->fieldDoesNotExist($name);
    }

    public function getErrorBag()
    {
        return $this->errorBag;
    }

    /**
     * Check if form has field.
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->fields);
    }

    /**
     * Get all form options.
     *
     * @return array
     */
    public function getFormOptions()
    {
        return $this->formOptions;
    }

    /**
     * Get single form option.
     *
     * @param string $option
     * @param mixed|null $default
     * @return mixed
     */
    public function getFormOption($option, $default = null)
    {
        return Arr::get($this->formOptions, $option, $default);
    }

    /**
     * Set single form option on form.
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
     * Get the passed config key using the custom
     * form config, if any.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getConfig($key = null, $default = null)
    {
        return $this->formHelper->getConfig($key, $default, $this->formConfig);
    }

    /**
     * Set form options.
     *
     * @param array $formOptions
     * @return $this
     */
    public function setFormOptions(array $formOptions)
    {
        $this->formOptions = $this->formHelper->mergeOptions($this->formOptions, $formOptions);
        $this->checkIfNamedForm();
        $this->pullFromOptions('data', 'addData');
        $this->pullFromOptions('model', 'setupModel');
        $this->pullFromOptions('errors_enabled', 'setErrorsEnabled');
        $this->pullFromOptions('client_validation', 'setClientValidationEnabled');
        $this->pullFromOptions('template_prefix', 'setTemplatePrefix');
        $this->pullFromOptions('language_name', 'setLanguageName');
        $this->pullFromOptions('translation_template', 'setTranslationTemplate');

        return $this;
    }

    /**
     * Get an option from provided options and call method with that value.
     *
     * @param string $name
     * @param string $method
     */
    protected function pullFromOptions($name, $method)
    {
        if (Arr::get($this->formOptions, $name) !== null) {
            $this->{$method}(Arr::pull($this->formOptions, $name));
        }
    }

    /**
     * Get form http method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->formOptions['method'];
    }

    /**
     * Set form http method.
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
     * Get form action url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->formOptions['url'];
    }

    /**
     * Set form action url.
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
     * Returns the name of the form.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get dot notation key for the form.
     *
     * @return string
     **/
    public function getNameKey()
    {
        if ($this->name === null) {
            return '';
        }

        return $this->formHelper->transformToDotSyntax($this->name);
    }

    /**
     * Set the name of the form.
     *
     * @param string $name
     * @param bool $rebuild
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
     * Get model that is bind to form object.
     *
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set model to form object.
     *
     * @param mixed $model
     * @return $this
     * @deprecated deprecated since 1.6.31, will be removed in 1.7 - pass model as option when creating a form
     */
    public function setModel($model)
    {
        $this->model = $model;

        $this->rebuildForm();

        return $this;
    }

    /**
     * Setup model for form, add namespace if needed for child forms.
     *
     * @return $this
     */
    protected function setupModel($model)
    {
        $this->model = $model;
        $this->setupNamedModel();

        return $this;
    }

    /**
     * Get all fields.
     *
     * @return FormField[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Get field dynamically.
     *
     * @param string $name
     * @return FormField
     */
    public function __get($name)
    {
        if ($this->has($name)) {
            return $this->getField($name);
        }
    }

    /**
     * Check if field exists when fetched using magic methods.
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->has($name);
    }

    /**
     * Set the Event Dispatcher to fire Laravel events.
     *
     * @param EventDispatcher $eventDispatcher
     * @return $this
     */
    public function setEventDispatcher(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * Set the form helper only on first instantiation.
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
     * Get form helper.
     *
     * @return FormHelper
     */
    public function getFormHelper()
    {
        return $this->formHelper;
    }

    /**
     * Add custom field.
     *
     * @param $name
     * @param $class
     */
    public function addCustomField($name, $class)
    {
        if ($this->rebuilding && $this->formHelper->hasCustomField($name)) {
            return $this;
        }

        $this->formHelper->addCustomField($name, $class);
    }

    /**
     * Returns wether form errors should be shown under every field.
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
     * @param bool $enabled
     * @return $this
     */
    public function setErrorsEnabled($enabled)
    {
        $this->showFieldErrors = (bool) $enabled;

        return $this;
    }

    /**
     * Is client validation enabled?
     *
     * @return bool
     */
    public function clientValidationEnabled()
    {
        return $this->clientValidationEnabled;
    }

    /**
     * Enable/disable client validation.
     *
     * @param bool $enable
     * @return $this
     */
    public function setClientValidationEnabled($enable)
    {
        $this->clientValidationEnabled = (bool) $enable;

        return $this;
    }

    /**
     * Add any aditional data that field needs (ex. array of choices).
     *
     * @deprecated deprecated since 1.6.20, will be removed in 1.7 - use 3rd param on create, or 2nd on plain method to pass data
     * will be switched to protected in 1.7.
     * @param string $name
     * @param mixed $data
     */
    public function setData($name, $data)
    {
        $this->data[$name] = $data;
    }

    /**
     * Get single additional data.
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

        return Arr::get($this->data, $name, $default);
    }

    /**
     * Add multiple peices of data at once.
     *
     * @deprecated deprecated since 1.6.12, will be removed in 1.7 - use 3rd param on create, or 2nd on plain method to pass data
     * will be switched to protected in 1.7.
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
     * Get current request.
     *
     * @return \Illuminate\Http\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set request on form.
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
     * Get template prefix that is prepended to all template paths.
     *
     * @return string
     */
    public function getTemplatePrefix()
    {
        if ($this->templatePrefix !== null) {
            return $this->templatePrefix;
        }

        return $this->getConfig('template_prefix');
    }

    /**
     * Set a template prefix for the form and its fields.
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
     * Get the language name.
     *
     * @return string
     */
    public function getLanguageName()
    {
        return $this->languageName;
    }

    /**
     * Set a language name, used as prefix for translated strings.
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
     * Get the translation template.
     *
     * @return string
     */
    public function getTranslationTemplate()
    {
        return $this->translationTemplate;
    }

    /**
     * Set a translation template, used to determine labels for fields.
     *
     * @param string $template
     * @return $this
     */
    public function setTranslationTemplate($template)
    {
        $this->translationTemplate = (string) $template;

        return $this;
    }

    /**
     * Render the form.
     *
     * @param array $options
     * @param string $fields
     * @param bool $showStart
     * @param bool $showFields
     * @param bool $showEnd
     * @return string
     */
    protected function render($options, $fields, $showStart, $showFields, $showEnd)
    {
        $formOptions = $this->buildFormOptionsForFormBuilder(
            $this->formHelper->mergeOptions($this->formOptions, $options)
        );

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
     * @param $formOptions
     * @return array
     */
    protected function buildFormOptionsForFormBuilder($formOptions)
    {
        $reserved = ['method', 'url', 'route', 'action', 'files'];
        $formAttributes = Arr::get($formOptions, 'attr', []);

        // move string value to `attr` to maintain backward compatibility
        foreach ($formOptions as $key => $formOption) {
            if (!in_array($formOption, $reserved) && is_string($formOption)) {
                $formAttributes[$key] = $formOption;
            }
        }

        return array_merge(
            $formAttributes, Arr::only($formOptions, $reserved)
        );
    }


    /**
     * Get template from options if provided, otherwise fallback to config.
     *
     * @return mixed
     */
    protected function getTemplate()
    {
        return $this->getTemplatePrefix() . $this->getFormOption('template', $this->getConfig('form'));
    }

    /**
     * Get all fields that are not rendered.
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
     * Prevent adding fields with same name.
     *
     * @param string $name
     * @throws \InvalidArgumentException
     * @return void
     */
    protected function preventDuplicate($name)
    {
        if ($this->has($name)) {
            throw new \InvalidArgumentException('Field ['.$name.'] already exists in the form '.get_class($this));
        }
    }

    /**
     * Returns and checks the type of the field.
     *
     * @param string $type
     * @return string
     */
    protected function getFieldType($type)
    {
        $fieldType = $this->formHelper->getFieldType($type);

        return $fieldType;
    }

    /**
     * Check if form is named form.
     *
     * @return void
     */
    protected function checkIfNamedForm()
    {
        if ($this->getFormOption('name')) {
            $this->name = Arr::pull($this->formOptions, 'name', $this->name);
        }
    }

    /**
     * Set up options on single field depending on form options.
     *
     * @param string $name
     * @param $options
     */
    protected function setupFieldOptions($name, &$options)
    {
        $options['real_name'] = $name;
    }

    /**
     * Set namespace to model if form is named so the data is bound properly.
     * Returns true if model is changed, otherwise false.
     *
     * @return bool
     */
    protected function setupNamedModel()
    {
        if (!$this->getModel() || !$this->getName()) {
            return false;
        }

        $dotName = $this->getNameKey();
        $model = $this->formHelper->convertModelToArray($this->getModel());
        $isCollectionFormModel = (bool) preg_match('/^.*\.\d+$/', $dotName);
        $isCollectionPrototype = strpos($dotName, '__NAME__') !== false;

        if (!Arr::get($model, $dotName) && !$isCollectionFormModel && !$isCollectionPrototype) {
            $newModel = [];
            Arr::set($newModel, $dotName, $model);
            $this->model = $newModel;

            return true;
        }

        return false;
    }

    /**
     * Set form builder instance on helper so we can use it later.
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
     * Returns the instance of the FormBuilder.
     *
     * @return FormBuilder
     */
    public function getFormBuilder()
    {
        return $this->formBuilder;
    }

    /**
     * Set the Validator instance on this so we can use it later.
     *
     * @param ValidatorFactory $validator
     * @return $this
     */
    public function setValidator(ValidatorFactory $validator)
    {
        $this->validatorFactory = $validator;

        return $this;
    }

    /**
     * Returns the validator instance.
     *
     * @return Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Exclude some fields from rendering.
     *
     * @return $this
     */
    public function exclude(array $fields)
    {
        $this->exclude = array_merge($this->exclude, $fields);

        return $this;
    }

    /**
     * If form is named form, modify names to be contained in single key (parent[child_field_name]).
     *
     * @param string $name
     * @return string
     */
    protected function getFieldName($name)
    {
        $formName = $this->getName();
        if ($formName !== null) {
            if (strpos($formName, '[') !== false || strpos($formName, ']') !== false) {
                return $this->formHelper->transformToBracketSyntax(
                    $this->formHelper->transformToDotSyntax(
                        $formName . '[' . $name . ']'
                    )
                );
            }

            return $formName . '[' . $name . ']';
        }

        return $name;
    }

    /**
     * Disable all fields in a form.
     */
    public function disableFields()
    {
        foreach ($this->fields as $field) {
            $field->disable();
        }
    }

    /**
     * Enable all fields in a form.
     */
    public function enableFields()
    {
        foreach ($this->fields as $field) {
            $field->enable();
        }
    }

    /**
     * Validate the form.
     *
     * @param array $validationRules
     * @param array $messages
     * @return Validator
     */
    public function validate($validationRules = [], $messages = [])
    {
        $fieldRules = $this->formHelper->mergeFieldsRules($this->fields);
        $rules = array_merge($fieldRules->getRules(), $validationRules);
        $messages = array_merge($fieldRules->getMessages(), $messages);

        $this->validator = $this->validatorFactory->make($this->getRequest()->all(), $rules, $messages);
        $this->validator->setAttributeNames($fieldRules->getAttributes());

        $this->eventDispatcher->dispatch(new BeforeFormValidation($this, $this->validator));

        return $this->validator;
    }

    /**
     * Get validation rules for the form.
     *
     * @param array $overrideRules
     * @return array
     */
    public function getRules($overrideRules = [])
    {
        $fieldRules = $this->formHelper->mergeFieldsRules($this->fields);

        return array_merge($fieldRules->getRules(), $overrideRules);
    }

    /**
     * Redirects to a destination when form is invalid.
     *
     * @param  string|null $destination The target url.
     * @return HttpResponseException
     */
    public function redirectIfNotValid($destination = null)
    {
        if (! $this->isValid()) {
            $response = redirect($destination);

            if (is_null($destination)) {
                $response = $response->back();
            }

            $response = $response->withErrors($this->getErrors(), $this->getErrorBag())->withInput();

            throw new HttpResponseException($response);
        }
    }

    /**
     * Get all form field attributes, including child forms, in a flat array.
     *
     * @return array
     */
    public function getAllAttributes()
    {
        return $this->formHelper->mergeAttributes($this->fields);
    }

    /**
     * Check if the form is valid.
     *
     * @return bool
     */
    public function isValid()
    {
        if (!$this->validator) {
            $this->validate();
        }

        $isValid = !$this->validator->fails();

        $this->formHelper->alterValid($this, $this, $isValid);

        $this->eventDispatcher->dispatch(new AfterFormValidation($this, $this->validator, $isValid));

        return $isValid;
    }

    /**
     * Optionally change the validation result, and/or add error messages.
     *
     * @param Form $mainForm
     * @param bool $isValid
     * @return void|array
     */
    public function alterValid(Form $mainForm, &$isValid)
    {
        // return ['name' => ['Some other error about the Name field.']];
    }

    /**
     * Get validation errors.
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
     * Get all Request values from all fields, and nothing else.
     *
     * @param bool $with_nulls
     * @return array
     */
    public function getFieldValues($with_nulls = true)
    {
        $request_values = $this->getRequest()->all();

        $values = [];
        foreach ($this->getAllAttributes() as $attribute) {
            $value = Arr::get($request_values, $attribute);
            if ($with_nulls || $value !== null) {
                Arr::set($values, $attribute, $value);
            }
        }

        // If this form is a child form, cherry pick a part
        if ($this->getName()) {
            $prefix = $this->getNameKey();
            $values = Arr::get($values, $prefix);
        }

        // Allow form-specific value alters
        $this->formHelper->alterFieldValues($this, $values);

        return $values;
    }

    /**
     * Optionally mess with this form's $values before it's returned from getFieldValues().
     *
     * @param array $values
     * @return void
     */
    public function alterFieldValues(array &$values)
    {
    }

    /**
     * Throw an exception indicating a field does not exist on the class.
     *
     * @param string $name
     * @throws \InvalidArgumentException
     * @return void
     */
    protected function fieldDoesNotExist($name)
    {
        throw new \InvalidArgumentException('Field ['.$name.'] does not exist in '.get_class($this));
    }

    /**
     * Method filterFields used as *Main* method for starting
     * filtering and request field mutating process.
     *
     * @return \Kris\LaravelFormBuilder\Form
     */
    public function filterFields()
    {
        // If filtering is unlocked/allowed we can start with filtering process.
        if (!$this->isFilteringLocked()) {
            $filters = array_filter($this->getFilters());

            if (count($filters)) {
                $dotForm = $this->getNameKey();

                $request = $this->getRequest();
                $requestData = $request->all();

                foreach ($filters as $field => $fieldFilters) {
                    $dotField = $this->formHelper->transformToDotSyntax($field);
                    $fieldData = Arr::get($requestData, $dotField);
                    if ($fieldData !== null) {
                        // Assign current Raw/Unmutated value from request.
                        $localDotField = preg_replace('#^' . preg_quote("$dotForm.", '#') . '#', '', $dotField);
                        $localBracketField = $this->formHelper->transformToBracketSyntax($localDotField);
                        $this->getField($localBracketField)->setRawValue($fieldData);
                        foreach ($fieldFilters as $filter) {
                            $filterObj = FilterResolver::instance($filter);
                            $fieldData = $filterObj->filter($fieldData);
                        }
                        Arr::set($requestData, $dotField, $fieldData);
                    }
                }

                foreach ($requestData as $name => $value) {
                    $request[$name] = $value;
                }
            }
        }

        return $this;
    }

    /**
     * Method getFilters used to return array of all binded filters to form fields.
     *
     * @return array
     */
    public function getFilters()
    {
        $filters = [];
        foreach ($this->getFields() as $field) {
            $filters[$field->getName()] = $field->getFilters();
        }

        return $filters;
    }

    /**
     * If lockFiltering is set to true then we will not
     * filter fields and mutate request data binded to fields.
     *
     * @return \Kris\LaravelFormBuilder\Form
     */
    public function lockFiltering()
    {
        $this->lockFiltering = true;
        return $this;
    }

    /**
     * Unlock fields filtering/mutating.
     *
     * @return \Kris\LaravelFormBuilder\Form
     */
    public function unlockFiltering()
    {
        $this->lockFiltering = false;
        return $this;
    }

    /**
     * Method isFilteringLocked used to check
     * if current filteringLocked property status is set to true.
     *
     * @return bool
     */
    public function isFilteringLocked()
    {
        return !$this->lockFiltering ? false : true;
    }

    /**
     * Method getRawValues returns Unfiltered/Unmutated fields -> values.
     *
     * @return array
     */
    public function getRawValues()
    {
        $rawValues = [];
        foreach ($this->getFields() as $field) {
            $rawValues[$field->getName()] = $field->getRawValue();
        }

        return $rawValues;
    }
}
