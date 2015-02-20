<?php  namespace Kris\LaravelFormBuilder\Fields;

use Kris\LaravelFormBuilder\Form;

class ChildFormType extends ParentType
{
    use BuildChildFormTrait;

    protected function getTemplate()
    {
        return 'child_form';
    }

    protected function getDefaults()
    {
        return [
            'is_child' => true,
            'class' => null,
            'formOptions' => [],
            'data' => []
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
}
