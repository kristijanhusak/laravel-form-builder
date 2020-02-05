<?php

namespace Kris\LaravelFormBuilder;

use Illuminate\Support\Arr;

class FieldGroup
{
    /**
     * The form that holds all the added fields.
     *
     * @var Form
     */
    protected $form;

    /**
     * The parent form.
     *
     * @var Form
     */
    protected $parent;

    /**
     * @var FormHelper
     */
    protected $formHelper;

    /**
     * All options for the group.
     *
     * @var array
     */
    protected $options = [];

    /**
     * The template name to be used.
     *
     * @var string
     */
    protected $template;

    /**
     * FieldGroup constructor.
     * @param Form $parent
     * @param array $options
     */
    public function __construct(Form $parent, array $options = [])
    {
        $this->parent = $parent;
        $this->formHelper = $parent->getFormHelper();
        $this->options = $this->formHelper->mergeOptions($this->getDefaults(), $options);
        $this->form = (clone $parent)->removeAll();
        $this->template = $parent->getConfig($this->getTemplate(), $this->getTemplate());
    }

    /**
     * Get the template, can be config variable or view path.
     *
     * @return string
     */
    protected function getTemplate()
    {
        return 'group';
    }

    /**
     * Default options for field.
     *
     * @return array
     */
    protected function getDefaults()
    {
        return [
            'label' => null,
            'label_attr' => ['class' => $this->parent->getConfig('defaults.label_class')],
            'wrapper' => ['class' => $this->parent->getConfig('defaults.group.wrapper_class') ?? $this->parent->getConfig('defaults.wrapper_class')],
        ];
    }

    /**
     * @return Fields\FormField[]
     */
    public function getFields()
    {
        return array_intersect_key($this->parent->getFields(), $this->form->getFields());
    }

    public function hasField(string $fieldName)
    {
        return array_key_exists($fieldName, $this->getFields());
    }

    /**
     * @param $name
     * @param string $type
     * @param array $options
     * @return $this
     */
    public function add($name, $type = 'text', array $options = [])
    {
        $this->form->add($name, $type, ['fieldGroup' => $this] + $options);

        return $this;
    }

    /**
     * @param \Closure $callback
     * @param FieldGroup|null $fieldGroup
     * @return $this
     */
    public function group(\Closure $callback, FieldGroup $fieldGroup = null)
    {
        $this->form->group($callback, $fieldGroup, $this->parent);

        return $this;
    }

    /**
     * Render the field.
     *
     * @param array $options
     * @return string
     */
    public function render(array $options = [])
    {
        $data = $this->getRenderData();

        return $this->formHelper->getView()->make(
            $this->getViewTemplate(),
            $data + [
                'group' => $this,
                'options' => $this->formHelper->mergeOptions($this->options, $options),
                'fields' => static::buildFieldsForRendering($this),
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
     * @return string
     */
    protected function getViewTemplate()
    {
        return $this->parent->getTemplatePrefix() . $this->getOption('template', $this->template);
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
     * @return Form
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param Form|FieldGroup $fieldsContainer
     * @return array (Fields\FormField|FieldGroup)[]
     */
    public static function buildFieldsForRendering($fieldsContainer)
    {
        $groupId = 0;
        $prevFieldGroup = null;
        $fieldsForRendering = [];

        foreach ($fieldsContainer->getFields() as $field) {
            $fieldGroup = $field->getOption('fieldGroup');

            if ($fieldGroup === $fieldsContainer) {
                $fieldsForRendering[$field->getRealName()] = $field;
                continue;
            }

            // init the $prevFieldGroup
            if ($fieldGroup && !$prevFieldGroup) {
                $prevFieldGroup = $fieldGroup;
            }

            // filter out sub-group
            if ($prevFieldGroup && $prevFieldGroup !== $fieldGroup && $prevFieldGroup->hasField($field->getRealName())) {
                continue;
            }

            if ($fieldGroup && $prevFieldGroup !== $fieldGroup) {
                $groupId++;
            }

            if ($fieldGroup) {
                $prevFieldGroup = $fieldGroup;
                $fieldsForRendering['group_' . $groupId] = $fieldGroup;
                continue;
            }

            $fieldsForRendering[$field->getRealName()] = $field;
        }

        return $fieldsForRendering;
    }

}