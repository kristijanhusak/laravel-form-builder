<?php

namespace Kris\LaravelFormBuilder\Fields;

class ContainerType extends ParentType
{
    protected function getTemplate()
    {
        return 'container';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaults()
    {
        return [
            'fields' => [],
            'container_class' => 'form-group',
        ];
    }

    /**
     * @return mixed|void
     * @throws \InvalidArgumentException
     */
    protected function createChildren()
    {
        $this->children = [];
        $fields = $this->getOption('fields');
        $requiredOptions = ['type', 'name'];

        foreach ($fields as $field) {
            foreach ($requiredOptions as $requiredOption) {
                if (empty($field[$requiredOption])) {
                    throw new \InvalidArgumentException("Fields field [{$this->name}] requires [{$requiredOption}] option");
                }
            }

            $parentName = $this->parent->getName();
            $rawName = $field['name'];
            $name = $parentName ? "{$parentName}[{$rawName}]" : $rawName;
            $options = $this->formHelper->mergeOptions($field['options'] ?? [], ['attr' => ['id' => $name]]);
            $type = $field['type'];
            $fieldType = $this->formHelper->getFieldType($type);
            $field = new $fieldType($name, $type, $this->parent, $options);
            $value = $this->getModelValueAttribute($this->parent->getModel(), $rawName);
            $field->setValue($value);
            $this->children[$rawName] = $field;
        }
    }


    /**
     * @inheritdoc
     */
    public function getAllAttributes()
    {
        return $this->formHelper->mergeAttributes($this->children);
    }

    /**
     * @inheritdoc
     */
    public function getValidationRules()
    {
        return $this->formHelper->mergeFieldsRules($this->children);
    }
}
