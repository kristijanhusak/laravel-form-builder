<?php namespace  Kris\LaravelFormBuilder\Fields;

use Kris\LaravelFormBuilder\Form;

class ChoiceType extends FormField
{

    /**
     * All children of the choice field
     *
     * @var array
     */
    protected $children = [];

    /**
     * Choice type
     *
     * @var string
     */
    protected $choiceType = 'select';

    public function __construct($name, $type, Form $parent, array $options = [])
    {
        parent::__construct($name, $type, $parent, $options);
        $this->determineChoiceField();
        $this->createChildren();
    }

    protected function getTemplate()
    {
        return 'choice';
    }

    public function render(array $options = [], $showLabel = true, $showField = true, $showError = true)
    {
        $options['children'] = $this->children;

        return parent::render($options, $showLabel, $showField, $showError);
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
            'multiple' => false
        ];
    }

    /**
     * Get all children of the choice field
     *
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Create children depending on choice type
     */
    protected function createChildren()
    {
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
            $this->children[] = new $fieldType(
                $this->name . $fieldMultiple,
                $this->choiceType,
                $this->parent,
                [
                    'attr'       => ['id' => $id],
                    'label_attr' => ['for' => $id],
                    'label'         => $choice,
                    'is_child'      => true,
                    'checked'       => in_array($key, (array)$this->options['selected']),
                    'default_value' => $key
                ]
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
