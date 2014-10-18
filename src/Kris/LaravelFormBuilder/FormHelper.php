<?php  namespace Kris\LaravelFormBuilder;

use Illuminate\Contracts\View\Factory as View;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Http\Request;

class FormHelper
{

    /**
     * @var View
     */
    protected $view;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Request
     */
    protected $request;

    /**
     * All available field types
     *
     * @var array
     */
    protected static $availableFieldTypes = [
        'text',
        'email',
        'url',
        'tel',
        'password',
        'hidden',
        'number',
        'date',
        'textarea',
        'submit',
        'reset',
        'button',
        'file',
        'image',
        'select',
        'checkbox',
        'radio',
        'choice'
    ];

    /**
     * Custom types
     *
     * @var array
     */
    private $customTypes = [];

    public function __construct(View $view, Config $config, Request $request)
    {
        $this->view = $view;
        $this->config = $config;
        $this->request = $request;
        $this->loadCustomTypes();
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
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
        if (array_key_exists($type, $this->customTypes)) {
            return $this->customTypes[$type];
        }

        if (!in_array($type, static::$availableFieldTypes)) {
            throw new \InvalidArgumentException(
                'Unsupported field type ['. $type .']. Avaiable types are: '.join(', ', static::$availableFieldTypes)
            );
        }

        switch($type) {
            case 'select':
                $fieldType = __NAMESPACE__.'\\Fields\\SelectType';
                break;
            case 'textarea':
                $fieldType = __NAMESPACE__.'\\Fields\\TextareaType';
                break;
            case 'button':
            case 'submit':
            case 'reset':
                $fieldType = __NAMESPACE__.'\\Fields\\ButtonType';
                break;
            case 'radio':
            case 'checkbox':
                $fieldType = __NAMESPACE__.'\\Fields\\CheckableType';
                break;
            case 'choice':
                $fieldType = __NAMESPACE__.'\\Fields\\ChoiceType';
                break;
            default:
                $fieldType = __NAMESPACE__.'\\Fields\\InputType';
                break;
        }

        return $fieldType;
    }

    /**
     * Convert array of attributes to html attributes
     *
     * @param $options
     * @return string
     */
    public function prepareAttributes($options)
    {
        $attributes = [];

        foreach ($options as $name => $option) {
            if ($option !== null) {
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
        $customFields = (array)$this->config->get('laravel-form-builder::custom_fields');

        if (!empty($customFields)) {
            foreach ($customFields as $fieldName => $fieldClass) {
                $this->addCustomField($fieldName, $fieldClass);
            }
        }
    }
}