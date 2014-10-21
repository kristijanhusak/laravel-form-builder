<?php  namespace Kris\LaravelFormBuilder\Fields;

use Kris\LaravelFormBuilder\Form;

class ChildFormType extends ParentType
{
    protected function getTemplate()
    {
        return 'child_form';
    }

    protected function getDefaults()
    {
        return [
            'is_child' => true,
            'class' => null
        ];
    }

    protected function createChildren()
    {
        $class = $this->getClassFromOptions();

        $class->setFormOptions([
            'name' => $this->name,
            'is_child' => true
        ])->rebuildForm();

        $this->children = $class->getFields();
    }

    /**
     * @return Form
     * @throws \Exception
     */
    protected function getClassFromOptions()
    {
        $class = array_get($this->options, 'class');

        if ($class && $class instanceof Form) {
            return $class;
        }

        throw new \Exception('Please provide instance of Form class.');
    }
}
