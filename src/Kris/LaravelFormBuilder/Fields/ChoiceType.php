<?php namespace  Kris\LaravelFormBuilder\Fields;


class ChoiceType extends FormField
{

    protected $children;

    protected $choiceType = 'select';

    protected function getTemplate()
    {
        return 'laravel-form-builder::choice';
    }

    public function render(array $options = [], $showLabel = true, $showField = true, $showError = true)
    {
        $this->determineChoiceField();
        $this->createChildren();
        $options['children'] = $this->children;

        return parent::render($options, $showLabel, $showField, $showError);
    }

    protected function determineChoiceField()
    {
        $expanded = (bool) $this->options['expanded'];
        $multiple = (bool) $this->options['multiple'];

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
            'multiple' => false
        ];
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    protected function createChildren()
    {
        $fieldMultiple = $this->options['multiple'] ? '[]' : '';
        $fieldType = $this->parent->getFormHelper()->getFieldType($this->choiceType);

        if ($this->choiceType == 'radio' || $this->choiceType == 'checkbox') {
            $this->buildCheckableChildren($fieldType, $fieldMultiple);
        } else {
            $this->buildSelect($fieldType, $fieldMultiple);
        }

    }

    /**
     * @param $fieldType
     * @param $fieldMultiple
     */
    protected function buildCheckableChildren($fieldType, $fieldMultiple)
    {
        foreach ($this->options['choices'] as $key => $choice) {
            $this->children[] = new $fieldType(
                $this->name . $fieldMultiple,
                $this->choiceType,
                $this->parent,
                [
                    'attr'          => ['id' => $choice . '_' . $key],
                    'label'         => $choice,
                    'is_child'      => true,
                    'selected'      => in_array($choice, (array)$this->options['selected']),
                    'default_value' => $key,
                    'labelAttrs'    => $this->parent->getFormHelper()->prepareAttributes([
                        'for' => $choice . '_' . $key
                    ])
                ]
            );
        }
    }

    /**
     * @param string $fieldType
     * @param string $fieldMultiple Append [] if multiple choice
     */
    protected function buildSelect($fieldType, $fieldMultiple)
    {
        $this->children[] = new $fieldType(
            $this->name . $fieldMultiple,
            $this->choiceType,
            $this->parent,
            $this->options
        );
    }
}
