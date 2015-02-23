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
        $this->rebuild();
    }

    public function rebuild($bindValues = false)
    {
        $this->form = $this->getClassFromOptions();

        $this->form->setFormOptions([
            'name' => $this->name,
            'is_child' => true
        ]);

        $model = $this->getOption('default_values');

        if ($bindValues && $model) {
            foreach ($this->form->getFields() as $name => $field) {
                var_dump($this->getModelValueAttribute($model, $name));
                $field->setOptions([
                    'default_value' => $this->getModelValueAttribute($model, $name)
                ]);
            }
        }

        $this->form->rebuildForm();

        dump($this->form);

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
