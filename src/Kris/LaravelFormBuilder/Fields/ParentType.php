<?php

namespace Kris\LaravelFormBuilder\Fields;

use Illuminate\Support\Arr;
use Kris\LaravelFormBuilder\Form;

abstract class ParentType extends FormField
{

    /**
     * @var FormField[]
     */
    protected $children;

    /**
     * Populate children array.
     *
     * @return mixed
     */
    abstract protected function createChildren();

    /**
     * @param       $name
     * @param       $type
     * @param Form  $parent
     * @param array $options
     * @return void
     */
    public function __construct($name, $type, Form $parent, array $options = [])
    {
        parent::__construct($name, $type, $parent, $options + ['copy_options_to_children' => true]);
        // If there is default value provided and  setValue was not triggered
        // in the parent call, make sure we generate child elements.
        if ($this->hasDefault) {
            $this->createChildren();
        }
        $this->checkIfFileType();
    }

    /**
     * @param  mixed $val
     *
     * @return ChildFormType
     */
    public function setValue($val)
    {
        parent::setValue($val);
        $this->createChildren();

        return $this;
    }

    /**
     * {inheritdoc}
     */
    public function render(array $options = [], $showLabel = true, $showField = true, $showError = true)
    {
        $options['children'] = $this->children;
        return parent::render($options, $showLabel, $showField, $showError);
    }

    /**
     * Get all children of the choice field.
     *
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Get a child of the choice field.
     *
     * @return mixed
     */
    public function getChild($key)
    {
        return Arr::get($this->children, $key);
    }

    /**
     * Remove child.
     *
     * @return $this
     */
    public function removeChild($key)
    {
        if ($this->getChild($key)) {
            unset($this->children[$key]);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setOption($name, $value)
    {
        parent::setOption($name, $value);

        if ($this->options['copy_options_to_children']) {
            foreach ((array) $this->children as $key => $child) {
                $this->children[$key]->setOption($name, $value);
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if ($this->options['copy_options_to_children']) {
            foreach ((array) $this->children as $key => $child) {
                $this->children[$key]->setOptions($options);
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isRendered()
    {
        foreach ((array) $this->children as $key => $child) {
            if ($child->isRendered()) {
                return true;
            }
        }

        return parent::isRendered();
    }

    /**
     * Get child dynamically.
     *
     * @param string $name
     * @return FormField
     */
    public function __get($name)
    {
        return $this->getChild($name);
    }

    /**
     * Check if field has type property and if it's file add enctype/multipart to form.
     *
     * @return void
     */
    protected function checkIfFileType()
    {
        if ($this->getOption('type') === 'file') {
            $this->parent->setFormOption('files', true);
        }
    }

    public function __clone()
    {
        foreach ((array) $this->children as $key => $child) {
            $this->children[$key] = clone $child;
        }
    }

    /**
     * @inheritdoc
     */
    public function disable()
    {
        foreach ($this->children as $field) {
            $field->disable();
        }
    }

    /**
     * @inheritdoc
     */
    public function enable()
    {
        foreach ($this->children as $field) {
            $field->enable();
        }
    }

    /**
     * @inheritdoc
     */
    public function getValidationRules()
    {
        $rules = parent::getValidationRules();
        $childrenRules = $this->formHelper->mergeFieldsRules($this->children);

        return $rules->append($childrenRules);
    }
}
