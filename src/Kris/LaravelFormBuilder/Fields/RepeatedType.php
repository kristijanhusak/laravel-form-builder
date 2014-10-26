<?php  namespace Kris\LaravelFormBuilder\Fields;

class RepeatedType extends ParentType
{

    /**
     * Get the template, can be config variable or view path
     *
     * @return string
     */
    protected function getTemplate()
    {
        return 'repeated';
    }

    protected function getDefaults()
    {
        return [
            'type' => 'password',
            'first_name' => 'password',
            'second_name' => 'password_confirmation',
            'first_options' => ['label' => 'Password', 'is_child' => true],
            'second_options' => ['label' => 'Password confirmation', 'is_child' => true]
        ];
    }

    /**
     * @param $name
     * @return FormField
     */
    public function __get($name)
    {
        if (($child = array_get($this->children, $name)) !== null) {
            return $child;
        }
    }

    protected function createChildren()
    {
        $fieldType = $this->formHelper->getFieldType($this->options['type']);

        $this->children['first'] = new $fieldType(
            $this->options['first_name'],
            $this->options['type'],
            $this->parent,
            $this->options['first_options']
        );

        $this->children['second'] = new $fieldType(
            $this->options['second_name'],
            $this->options['type'],
            $this->parent,
            $this->options['second_options']
        );
    }
}