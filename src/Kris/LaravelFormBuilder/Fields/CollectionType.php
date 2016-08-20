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
     * @inheritdoc
     */
    protected $valueProperty = 'data';

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
            'type' => null,
            'options' => ['is_child' => true],
            'prototype' => true,
            'data' => null,
            'property' => 'id',
            'prototype_name' => '__NAME__',
            'empty_row' => true
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
    public function getAllAttributes()
    {
        // Collect all children's attributes.
        return $this->parent->getFormHelper()->mergeAttributes($this->children);
    }

    /**
     * @inheritdoc
     */
    protected function createChildren()
    {
        $this->children = [];
        $type = $this->getOption('type');
        $oldInput = $this->parent->getRequest()->old($this->getNameKey());
        $currentInput = $this->parent->getRequest()->get($this->getNameKey());

        try {
            $fieldType = $this->formHelper->getFieldType($type);
        } catch (\Exception $e) {
            throw new \Exception(
                'Collection field ['.$this->name.'] requires [type] option'. "\n\n".
                $e->getMessage()
            );
        }

        $data = $this->getOption($this->valueProperty, []);

        // If no value is provided, get values from current request
        if (count($data) === 0) {
            $data = $currentInput;
        }

        // Needs to have more than 1 item because 1 is rendered by default.
        // This overrides current request in situations when validation fails.
        if (count($oldInput) > 1) {
            $data = $oldInput;
        }

        if ($data instanceof Collection) {
            $data = $data->all();
        }

        $field = new $fieldType($this->name, $type, $this->parent, $this->getOption('options'));

        if ($this->getOption('prototype')) {
            $this->generatePrototype(clone $field);
        }

        if (!$data || empty($data)) {
            if ($this->getOption('empty_row')) {
                return $this->children[] = $this->setupChild(clone $field, '[0]');
            }

            return $this->children = [];
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

        $field->setValue($value);


        return $field;
    }

    /**
     * Generate prototype for regular form field
     *
     * @param FormField $field
     */
    protected function generatePrototype(FormField $field)
    {
        $value = $field instanceof ChildFormType ? false : null;
        $field->setOption('is_prototype', true);
        $field = $this->setupChild($field, $this->getPrototypeName(), $value);

        if ($field instanceof ChildFormType) {
            foreach ($field->getChildren() as $child) {
                if ($child instanceof CollectionType) {
                    $child->preparePrototype($child->prototype());
                }
            }
        }

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

    /**
     * Prepare collection for prototype by adding prototype as child
     * @param FormField $field
     */
    public function preparePrototype(FormField $field)
    {
        if (!$field->getOption('is_prototype')) {
            throw new \InvalidArgumentException(
                'Field ['.$field->getRealName().'] is not a valid prototype object.'
            );
        }

        $this->children = [];
        $this->children[] = $field;
    }
}
