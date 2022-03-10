<?php

namespace  Kris\LaravelFormBuilder\Fields;

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
     * Determine which choice type to use.
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

        return $this->choiceType = 'select';
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
     * Create children depending on choice type.
     *
     * @return void
     */
    protected function createChildren()
    {
        if (($data_override = $this->getOption('data_override')) && $data_override instanceof \Closure) {
            $this->options['choices'] = $data_override($this->options['choices'], $this);
        }
        
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
     * Build checkable children fields from choice type.
     *
     * @param string $fieldType
     *
     * @return void
     */
    protected function buildCheckableChildren($fieldType)
    {
        $multiple = $this->getOption('multiple') ? '[]' : '';

        foreach ((array)$this->options['choices'] as $key => $choice) {
            $id = str_replace('.', '_', $this->getNameKey()) . '_' . $key;
            $options = $this->formHelper->mergeOptions(
                $this->getOption('choice_options'),
                [
                    'attr'       => array_merge(['id' => $id], $this->options['option_attributes'][$key] ?? []),
                    'label_attr' => ['for' => $id],
                    'label'      => $choice,
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
     * Build select field from choice.
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

    /**
     * Creates default wrapper classes for the form element.
     *
     * @param array $options
     * @return array
     */
    protected function setDefaultClasses(array $options = [])
    {
        $defaults = parent::setDefaultClasses($options);
        $choice_type = $this->determineChoiceField();

        $wrapper_class = $this->formHelper->getConfig('defaults.' . $this->type . '.' . $choice_type . '_wrapper_class', '');
        if ($wrapper_class) {
            $defaults['wrapper']['class'] = (isset($defaults['wrapper']['class']) ? $defaults['wrapper']['class'] . ' ' : '') . $wrapper_class;
        }

        $choice_wrapper_class = $this->formHelper->getConfig('defaults.' . $this->type . '.choice_options.wrapper_class', '');
        $choice_label_class = $this->formHelper->getConfig('defaults.' . $this->type . '.choice_options.label_class', '');
        $choice_field_class = $this->formHelper->getConfig('defaults.' . $this->type . '.choice_options.field_class', '');

        if ($choice_wrapper_class) {
            $defaults['choice_options']['wrapper']['class'] = $choice_wrapper_class;
        }
        if ($choice_label_class) {
            $defaults['choice_options']['label_attr']['class'] = $choice_label_class;
        }
        if ($choice_field_class) {
            $defaults['choice_options']['attr']['class'] = $choice_field_class;
        }

        return $defaults;
    }
}
