<?php namespace  Kris\LaravelFormBuilder\Fields;

class ChoiceType extends ParentType
{
    /**
     * @var string
     */
    protected $choiceType = 'select';

    protected function getTemplate()
    {
        return 'choice';
    }

    protected function setValue($val)
    {
        $this->options['selected'] = $val;
        $this->rebuild();

        return $this;
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
        $this->determineChoiceField();

        $fieldMultiple = $this->options['multiple'] ? '[]' : '';
        $fieldType = $this->formHelper->getFieldType($this->choiceType);

        switch ($this->choiceType) {
            case 'radio':
            case 'checkbox':
                $this->buildCheckableChildren($fieldType, $fieldMultiple);
                break;
            default:
                $this->buildSelect($fieldType, $fieldMultiple);
                break;
        }
    }

    /**
     * Build checkable children fields from choice type
     *
     * @param string $fieldType
     * @param string $fieldMultiple
     */
    protected function buildCheckableChildren($fieldType, $fieldMultiple)
    {
        foreach ((array)$this->options['choices'] as $key => $choice) {
            $id = $choice . '_' . $key;
            $options = $this->formHelper->mergeOptions(
                $this->getOption('choice_options'),
                [
                    'attr'       => ['id' => $id],
                    'label_attr' => ['for' => $id],
                    'label'         => $this->formHelper->formatLabel($choice),
                    'checked'       => in_array($key, (array)$this->options['selected']),
                    'default_value' => $key
                ]
            );
            $this->children[] = new $fieldType(
                $this->name . $fieldMultiple,
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
     * @param string $fieldMultiple Append [] if multiple choice
     */
    protected function buildSelect($fieldType, $fieldMultiple)
    {
        $this->children[] = new $fieldType(
            $this->name . $fieldMultiple,
            $this->choiceType,
            $this->parent,
            $this->formHelper->mergeOptions($this->options, ['is_child' => true])
        );
    }
}
