<?php  namespace Kris\LaravelFormBuilder\Fields;

use Kris\LaravelFormBuilder\Form;

class ChildFormType extends ParentType
{

    /**
     * @var Form
     */
    protected $form;

    protected function getTemplate()
    {
        return 'child_form';
    }

    public function getForm()
    {
        return $this->form;
    }

    protected function setValue($val, $bindValues = false)
    {
        $this->options['default_value'] = $val;
        $this->rebuild($bindValues);

        return $this;
    }

    protected function getDefaults()
    {
        return [
            'is_child' => true,
            'class' => null,
            'default_value' => null,
            'formOptions' => [],
            'data' => []
        ];
    }

    protected function createChildren()
    {
        $this->rebuild();
    }

    public function rebuild($bindValues = false)
    {
        $this->form = $this->getClassFromOptions();

        $this->form->setFormOptions([
            'name' => $this->name,
            'is_child' => true
        ])->rebuildForm();

        $model = $this->getOption('default_value');

        if ($bindValues && $model) {
            foreach ($this->form->getFields() as $name => $field) {
                $field->setValue($this->getModelValueAttribute($model, $name));
            }
        }

        $this->children = $this->form->getFields();
    }

    /**
     * @return Form
     * @throws \Exception
     */
    protected function getClassFromOptions()
    {
        $class = $this->getOption('class');

        if (!$class) {
            throw new \InvalidArgumentException(
                'Please provide full name or instance of Form class.'
            );
        }

        if (is_string($class)) {
            return $this->parent->getFormBuilder()->create(
                $class,
                $this->getOption('formOptions'),
                $this->getOption('data')
            );
        }

        if ($class instanceof Form) {
            return $class;
        }

        throw new \InvalidArgumentException(
            'Class provided does not exist or it passed in wrong format.'
        );

    }
}
