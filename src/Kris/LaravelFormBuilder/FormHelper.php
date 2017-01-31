<?php  namespace Kris\LaravelFormBuilder;

use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Contracts\View\Factory as View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kris\LaravelFormBuilder\Fields\FormField;
use Kris\LaravelFormBuilder\Form;
use Illuminate\Translation\Translator;

class FormHelper
{

    /**
     * @var View
     */
    protected $view;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var FormBuilder
     */
    protected $formBuilder;

    /**
     * @var array
     */
    protected static $reservedFieldNames = [
        'save'
    ];

    /**
     * All available field types
     *
     * @var array
     */
    protected static $availableFieldTypes = [
        'text'           => 'InputType',
        'email'          => 'InputType',
        'url'            => 'InputType',
        'tel'            => 'InputType',
        'search'         => 'InputType',
        'password'       => 'InputType',
        'hidden'         => 'InputType',
        'number'         => 'InputType',
        'date'           => 'InputType',
        'file'           => 'InputType',
        'image'          => 'InputType',
        'color'          => 'InputType',
        'datetime-local' => 'InputType',
        'month'          => 'InputType',
        'range'          => 'InputType',
        'time'           => 'InputType',
        'week'           => 'InputType',
        'select'         => 'SelectType',
        'textarea'       => 'TextareaType',
        'button'         => 'ButtonType',
        'buttongroup'    => 'ButtonGroupType',
        'submit'         => 'ButtonType',
        'reset'          => 'ButtonType',
        'radio'          => 'CheckableType',
        'checkbox'       => 'CheckableType',
        'choice'         => 'ChoiceType',
        'form'           => 'ChildFormType',
        'entity'         => 'EntityType',
        'collection'     => 'CollectionType',
        'repeated'       => 'RepeatedType',
        'static'         => 'StaticType'
    ];

    /**
     * Custom types
     *
     * @var array
     */
    private $customTypes = [];

    /**
     * @param View    $view
     * @param Translator $translator
     * @param array   $config
     */
    public function __construct(View $view, Translator $translator, array $config = [])
    {
        $this->view = $view;
        $this->translator = $translator;
        $this->config = $config;
        $this->loadCustomTypes();
    }

    /**
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public function getConfig($key, $default = null)
    {
        return array_get($this->config, $key, $default);
    }

    /**
     * @return View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Merge options array
     *
     * @param array $first
     * @param array $second
     * @return array
     */
    public function mergeOptions(array $first, array $second)
    {
        return array_replace_recursive($first, $second);
    }

    /**
     * Get proper class for field type
     *
     * @param $type
     * @return string
     */
    public function getFieldType($type)
    {
        $types = array_keys(static::$availableFieldTypes);

        if (!$type || trim($type) == '') {
            throw new \InvalidArgumentException('Field type must be provided.');
        }

        if (array_key_exists($type, $this->customTypes)) {
            return $this->customTypes[$type];
        }

        if (!in_array($type, $types)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Unsupported field type [%s]. Available types are: %s',
                    $type,
                    join(', ', array_merge($types, array_keys($this->customTypes)))
                )
            );
        }

        $namespace = __NAMESPACE__.'\\Fields\\';

        return $namespace . static::$availableFieldTypes[$type];
    }

    /**
     * Convert array of attributes to html attributes
     *
     * @param $options
     * @return string
     */
    public function prepareAttributes($options)
    {
        if (!$options) {
            return null;
        }

        $attributes = [];

        foreach ($options as $name => $option) {
            if ($option !== null) {
                $name = is_numeric($name) ? $option : $name;
                $attributes[] = $name.'="'.$option.'" ';
            }
        }

        return join('', $attributes);
    }

    /**
     * Add custom field
     *
     * @param $name
     * @param $class
     */
    public function addCustomField($name, $class)
    {
        if (!array_key_exists($name, $this->customTypes)) {
            return $this->customTypes[$name] = $class;
        }

        throw new \InvalidArgumentException('Custom field ['.$name.'] already exists on this form object.');
    }

    /**
     * Load custom field types from config file
     */
    private function loadCustomTypes()
    {
        $customFields = (array) $this->getConfig('custom_fields');

        if (!empty($customFields)) {
            foreach ($customFields as $fieldName => $fieldClass) {
                $this->addCustomField($fieldName, $fieldClass);
            }
        }
    }

    public function convertModelToArray($model)
    {
        if (!$model) {
            return null;
        }

        if ($model instanceof Model) {
            return $model->toArray();
        }

        if ($model instanceof Collection) {
            return $model->all();
        }

        return $model;
    }

    /**
     * Format the label to the proper format
     *
     * @param $name
     * @return string
     */
    public function formatLabel($name)
    {
        if (!$name) {
            return null;
        }

        if ($this->translator->has($name)) {
            $translatedName = $this->translator->get($name);

            if (is_string($translatedName)) {
                return $translatedName;
            }
        }

        return ucfirst(str_replace('_', ' ', $name));
    }

    /**
     * @param FormField[] $fields
     * @return array
     */
    public function mergeFieldsRules($fields)
    {
        $rules = [];
        $attributes = [];
        $messages = [];

        foreach ($fields as $field) {
            if ($fieldRules = $field->getValidationRules()) {
                $rules = array_merge($rules, $fieldRules['rules']);
                $attributes = array_merge($attributes, $fieldRules['attributes']);
                $messages = array_merge($messages, $fieldRules['error_messages']);
            }
        }

        return [
            'rules' => $rules,
            'attributes' => $attributes,
            'error_messages' => $messages
        ];
    }

    /**
     * @return array
     */
    public function mergeAttributes(array $fields)
    {
        $attributes = [];
        foreach ($fields as $field) {
            $attributes = array_merge($attributes, $field->getAllAttributes());
        }

        return $attributes;
    }

    /**
     * Alter a form's values recursively according to its fields
     *
     * @return void
     */
    public function alterFieldValues(Form $form, array &$values)
    {
        // Alter the form itself
        $form->alterFieldValues($values);

        // Alter the form's child forms recursively
        foreach ($form->getFields() as $name => $field) {
            if (method_exists($field, 'alterFieldValues')) {
                $fullName = $this->transformToDotSyntax($name);

                $subValues = Arr::get($values, $fullName);
                $field->alterFieldValues($subValues);
                Arr::set($values, $fullName, $subValues);
            }
        }
    }

    /**
     * Alter a form's validity recursively, and add messages with nested form prefix
     *
     * @return void
     */
    public function alterValid(Form $form, Form $mainForm, &$isValid)
    {
        // Alter the form itself
        $messages = $form->alterValid($mainForm, $isValid);

        // Add messages to the existing Bag
        if ($messages) {
            $messageBag = $mainForm->getValidator()->getMessageBag();
            $this->appendMessagesWithPrefix($messageBag, $form->getName(), $messages);
        }

        // Alter the form's child forms recursively
        foreach ($form->getFields() as $name => $field) {
            if (method_exists($field, 'alterValid')) {
                $field->alterValid($mainForm, $isValid);
            }
        }
    }

    /**
     * Add unprefixed messages with prefix to a MessageBag
     */
    public function appendMessagesWithPrefix(MessageBag $messageBag, $prefix, array $keyedMessages)
    {
        foreach ($keyedMessages as $key => $messages) {
            if ($prefix) {
                $key = $this->transformToDotSyntax($prefix . '[' . $key . ']');
            }

            foreach ((array) $messages as $message) {
                $messageBag->add($key, $message);
            }
        }
    }

    /**
     * @param string $string
     * @return string
     */
    public function transformToDotSyntax($string)
    {
        return str_replace(['.', '[]', '[', ']'], ['_', '', '.', ''], $string);
    }

    /**
     * @param string $string
     * @return string
     */
    public function transformToBracketSyntax($string)
    {
        $name = explode('.', $string);
        if (count($name) == 1) {
            return $name[0];
        }

        $first = array_shift($name);
        return $first . '[' . implode('][', $name) . ']';
    }

    /**
     * @return TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Check if field name is valid and not reserved
     *
     * @throws \InvalidArgumentException
     * @param string $name
     * @param string $className
     */
    public function checkFieldName($name, $className)
    {
        if (!$name || trim($name) == '') {
            throw new \InvalidArgumentException(
                "Please provide valid field name for class [{$className}]"
            );
        }

        if (in_array($name, static::$reservedFieldNames)) {
            throw new \InvalidArgumentException(
                "Field name [{$name}] in form [{$className}] is a reserved word. Please use a different field name." .
                "\nList of all reserved words: " . join(', ', static::$reservedFieldNames)
            );
        }

        return true;
    }
}
