<?php  namespace Kris\LaravelFormBuilder\Fields;

use Kris\LaravelFormBuilder\Form;

class CollectionType extends ParentType
{
    use BuildChildFormTrait;

    /**
     * Contains template for a collection element
     *
     * @var string
     */
    protected $prototype = '';

    protected function getTemplate()
    {
        return 'child_form';
    }

    protected function getDefaults()
    {
        return [
            'is_child' => true,
            'type' => null,
            'class' => null,
            'formOptions' => [],
            'data' => [],
            'options' => [],
            'prototype' => true,
            'prototype_name' => '__NAME__'
        ];
    }

    public function getPrototype() {
        return $this->prototype;
    }

    protected function createChildren()
    {
        $type = $this->getOption('type');
        $fieldType = $this->formHelper->getFieldType($type);

        if ($type === 'form') {
            return $this->buildChildForm();
        }

        /**
         * @var FormField $field
         */
        $field = new $fieldType($this->name, $type, $this->parent, $this->getOption('options'));

        if ($this->getOption('prototype')) {
            $this->generatePrototype(clone $field);
        }

        $firstFieldName = $field->getName().'[0]';
        $firstFieldOptions = $this->formHelper->mergeOptions(
            $this->getOption('options'),
            ['attr' => ['id' => $firstFieldName]]
        );

        $field->setName($firstFieldName);
        $field->setOptions($firstFieldOptions);

        return $this->children[] = $field;
    }

    /**
     * Generate prototype for regular form field
     *
     * @param FormField $field
     */
    protected function generatePrototype(FormField $field)
    {
        $name = $field->getName().$this->getPrototypeName();
        $field->setName($name);

        $options = $this->formHelper->mergeOptions(
            $this->getOption('options'),
            ['attr' => ['id' => $name]]
        );

        $field->setOptions($options);

        $this->prototype = $field->render();
    }

    /**
     * Generate collection from child form
     *
     * @return array
     */
    protected function buildChildForm()
    {
        $class = $this->getClassFromOptions();

        if ($this->getOption('prototype')) {
            $this->generateChildFormPrototype($class);
        }

        $class->setFormOptions([
            'name' => $this->name.'[0]',
            'is_child' => true
        ])->rebuildForm();



        return $this->children[] = $class->getFields();
    }

    /**
     * Generate prototype for child form type
     *
     * @param Form $class
     */
    protected function generateChildFormPrototype(Form $class)
    {
        $class->setFormOptions([
            'name' => $this->name.$this->getPrototypeName(),
            'is_child' => true
        ])->rebuildForm();

        $this->children = $class->getFields();

        $this->prototype = $this->render();
    }

    /**
     * Generate array like prototype name
     *
     * @return string
     */
    protected function getPrototypeName()
    {
        return '[' . $this->getOption('prototype_name') . ']';
    }
}
