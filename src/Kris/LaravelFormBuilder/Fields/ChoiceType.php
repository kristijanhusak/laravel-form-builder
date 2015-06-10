<?php namespace  Kris\LaravelFormBuilder\Fields;

class ChoiceType extends ParentType
{
    /**
     * @var string
     */
    protected $choiceType = 'select';

    /**
     * @inheritdoc
     */
    protected $valueProperty = 'selected';

    /**
     * @inheritdoc
     */
    protected function getTemplate()
    {
        return 'choice';
    }

    /**
     * Determine which choice type to use
     *
     * @return string
     */
    protected function determineChoiceField()
    {
        $expanded = $this->options['expanded'];
        $multiple = $this->options['multiple'];

        if ($multiple) {
            $this->options['attr']['multiple'] = true;
        }

        if ($expanded && !$multiple) {
            return $this->choiceType = 'radio';
        }

        if ($expanded && $multiple) {
            return $this->choiceType = 'checkbox';
        }
    }

    /**
     * @inheritdoc
     */
    protected function getDefaults()
    {
        return [
            'choices' => null,
            'selected' => null,
            'expanded' => false,
            'multiple' => false,
            'choice_options' => [
                'wrapper' => false,
                'is_child' => true
            ]
        ];
    }

    /**
     * Create children depending on choice type
     */
    protected function createChildren()
    {
        $this->children = [];
        $this->determineChoiceField();

        $fieldType = $this->formHelper->getFieldType($this->choiceType);

        switch ($this->choiceType) {
            case 'radio':
            case 'checkbox':
                $this->buildCheckableChildren($fieldType);
                break;
            default:
                $this->buildSelect($fieldType);
                break;
        }
    }

    /**
     * Build checkable children fields from choice type
     *
     * @param string $fieldType
     */
    protected function buildCheckableChildren($fieldType)
    {
        $multiple = $this->getOption('multiple') ? '[]' : '';

        foreach ((array)$this->options['choices'] as $key => $choice) {
            $id = str_replace('.', '_', $this->getNameKey()) . '_' . $key;
            $options = $this->formHelper->mergeOptions(
                $this->getOption('choice_options'),
                [
                    'attr'       => ['id' => $id],
                    'label_attr' => ['for' => $id],
                    'label'      => $this->formHelper->formatLabel($choice),
                    'checked'    => in_array($key, (array)$this->options[$this->valueProperty]),
                    'value'      => $key
                ]
            );
            $this->children[] = new $fieldType(
                $this->name . $multiple,
                $this->choiceType,
                $this->parent,
                $options
            );
        }
    }

    /**
     * Build select field from choice
     *
     * @param string $fieldType
     */
    protected function buildSelect($fieldType)
    {
        $this->children[] = new $fieldType(
            $this->name,
            $this->choiceType,
            $this->parent,
            $this->formHelper->mergeOptions($this->options, ['is_child' => true])
        );
    }
}
