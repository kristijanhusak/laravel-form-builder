<?php  namespace Kris\LaravelFormBuilder;

use Illuminate\Contracts\View\Factory as View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\Fields\FormField;

class FormHelper
{

    /**
     * @var View
     */
    protected $view;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var FormBuilder
     */
    protected $formBuilder;

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
     * @param Request $request
     * @param array   $config
     */
    public function __construct(View $view, Request $request, array $config = [])
    {
        $this->view = $view;
        $this->config = $config;
        $this->request = $request;
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
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
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

        foreach ($fields as $field) {
            if ($fieldRules = $field->getValidationRules()) {
                $rules = array_merge($rules, $fieldRules['rules']);
                $attributes = array_merge($attributes, $fieldRules['attributes']);
            }
        }

        return [
            'rules' => $rules,
            'attributes' => $attributes
        ];
    }

    /**
     * @param string $string
     * @return string
     */
    public function transformToDotSyntax($string)
    {
        return str_replace(['.', '[]', '[', ']'], ['_', '', '.', ''], $string);
    }
}
