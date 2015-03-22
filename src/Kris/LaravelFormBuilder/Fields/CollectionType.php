<?php  namespace Kris\LaravelFormBuilder\Fields;

use Illuminate\Support\Collection;

class CollectionType extends ParentType
{
    /**
     * Contains template for a collection element
     *
     * @var FormField
     */
    protected $proto;

    /**
     * @return string
     */
    protected function getTemplate()
    {
        return 'collection';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaults()
    {
        return [
            'is_child' => true,
            'type' => null,
            'options' => [],
            'prototype' => true,
            'data' => [],
            'property' => 'id',
            'prototype_name' => '__NAME__'
        ];
    }

    /**
     * Get the prototype object
     *
     * @return FormField
     * @throws \Exception
     */
    public function prototype()
    {

        if ($this->getOption('prototype') === false) {
            throw new \Exception(
                'Prototype for collection field [' . $this->name .'] is disabled.'
            );
        }

        return $this->proto;
    }

    /**
     * @inheritdoc
     */
    protected function createChildren()
    {
        $type = $this->getOption('type');

        try {
            $fieldType = $this->formHelper->getFieldType($type);
        } catch (\Exception $e) {
            throw new \Exception(
                'Collection field ['.$this->name.'] requires [type] option'. "\n\n".
                $e->getMessage()
            );
        }

        $data = $this->getOption('data');

        if (!$data) {
            $data = $this->parent->getModel();
            $data = $this->getModelValueAttribute($data, $this->getName());
        }

        if ($data instanceof Collection) {
            $data = $data->all();
        }

        $field = new $fieldType($this->name, $type, $this->parent, $this->getOption('options'));

        if ($this->getOption('prototype')) {
            $this->generatePrototype(clone $field);
        }

        if (!$data || empty($data)) {
            return $this->children[] = $this->setupChild(clone $field, '[0]');
        }

        if (!is_array($data) && !$data instanceof \Traversable) {
            throw new \Exception(
                'Data for collection field ['.$this->name.'] must be iterable.'
            );
        }

        foreach ($data as $key => $val) {
            $this->children[] = $this->setupChild(clone $field, '['.$key.']', $val);
        }
    }

    /**
     * Set up a single child element for a collection
     *
     * @param FormField $field
     * @param           $name
     * @param null      $value
     * @return FormField
     */
    protected function setupChild(FormField $field, $name, $value = null)
    {
        $newFieldName = $field->getName().$name;

        $firstFieldOptions = $this->formHelper->mergeOptions(
            $this->getOption('options'),
            ['attr' => ['id' => $newFieldName]]
        );

        $field->setName($newFieldName);
        $field->setOptions($firstFieldOptions);

        if ($value && !$field instanceof ChildFormType) {
            $value = $this->getModelValueAttribute(
                $value,
                $this->getOption('property')
            );
        }

        $field->setValue($value, true);

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
