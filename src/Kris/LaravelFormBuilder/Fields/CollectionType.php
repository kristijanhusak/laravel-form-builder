<?php  namespace Kris\LaravelFormBuilder\Fields;

use Illuminate\Support\Collection;
use Kris\LaravelFormBuilder\Form;

class CollectionType extends ParentType
{
    /**
     * Contains template for a collection element
     *
     * @var string
     */
    protected $proto;

    protected function getTemplate()
    {
        return 'child_form';
    }

    protected function getDefaults()
    {
        return [
            'is_child' => true,
            'type' => null,
            'options' => [],
            'prototype' => true,
            'data' => [],
            'prototype_name' => '__NAME__'
        ];
    }

    public function prototype() {

        if ($this->getOption('prototype') === false) {
            throw new \Exception(
                'Prototype for collection field [' . $this->name .'] is disabled.'
            );
        }

        return $this->proto;
    }

    protected function createChildren()
    {
        $type = $this->getOption('type');
        $fieldType = $this->formHelper->getFieldType($type);
        $data = $this->getOption('data');

        /**
         * @var FormField $field
         */
        $field = new $fieldType($this->name, $type, $this->parent, $this->getOption('options'));

        if ($this->getOption('prototype')) {
            $this->generatePrototype(clone $field);
        }

        if (!$data || empty($data)) {
            return $this->children[] = $this->setupChild($field, '[0]');
        }

        if (!is_array($data) && !$data instanceof \Traversable) {
            throw new \Exception(
                'Data for collection field ['.$this->name.'] must be iterable.'
            );
        }

        foreach ($data as $key => $val) {
            $this->children[] = $this->setupChild($field, '['.$key.']', $val);
        }
    }

    protected function setupChild(FormField $field, $name, $value = null)
    {
        $newFieldName = $field->getName().$name;
        $firstFieldOptions = $this->formHelper->mergeOptions(
            $this->getOption('options'),
            [
                'attr' => ['id' => $newFieldName],
                'default_value' => $value
            ]
        );

        $field->setName($newFieldName);
        $field->setOptions($firstFieldOptions);

        if ($field instanceof ChildFormType) {
            $field->rebuild(true);
        }

        return $field;
    }

    /**
     * Generate prototype for regular form field
     *
     * @param FormField $field
     */
    protected function generatePrototype(FormField $field)
    {
        $field = $this->setupChild($field, $this->getPrototypeName());

        $this->proto = $field;
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
