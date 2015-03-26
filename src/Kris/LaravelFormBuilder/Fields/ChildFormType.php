<?php  namespace Kris\LaravelFormBuilder\Fields;

use Kris\LaravelFormBuilder\Form;

class ChildFormType extends ParentType
{

    /**
     * @var Form
     */
    protected $form;

    /**
     * @inheritdoc
     */
    protected function getTemplate()
    {
        return 'child_form';
    }

    /**
     * Add a single field to the form
     *
     * @param        $name
     * @param string $type
     * @param array  $options
     * @param bool   $modify
     *
     * @return Form
     */
    public function add($name, $type = 'text', array $options = [], $modify = false)
    {
        $this->form->add($name, $type, $options, $modify);
        $this->children[$name] = $this->form->getField($name);

        return $this->form;
    }

    /**
     * Remove field with specified name from the form
     *
     * @param $name
     *
     * @return Form
     */
    public function remove($name)
    {
        $this->form->remove($name);
        if ($this->getChild($name)) {
            unset($this->children[$name]);
        }

        return $this->form;
    }

    /**
     * Modify existing field. If it doesn't exist, it is added to form
     *
     * @param        $name
     * @param string $type
     * @param array  $options
     * @param bool   $overwriteOptions
     *
     * @return Form
     */
    public function modify($name, $type = 'text', array $options = [], $overwriteOptions = false)
    {
        $this->form->modify($name, $type, $options, $overwriteOptions);
        $this->children[$name] = $this->form->getField($name);

        return $this->form;
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param      $val
     * @param bool $bindValues
     * @return $this
     */
    protected function setValue($val, $bindValues = false)
    {
        $this->options['default_value'] = $val;
        $this->rebuild($bindValues);

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function getDefaults()
    {
        return [
            'class' => null,
            'default_value' => null,
            'formOptions' => [],
            'data' => []
        ];
    }

    /**
     * @inheritdoc
     */
    protected function createChildren()
    {
        $this->rebuild();
    }

    /**
     * @param bool $bindValues should model value be bound to form
     * @return mixed|void
     */
    protected function rebuild($bindValues = false)
    {
        $this->form = $this->getClassFromOptions();

        if (!$this->form->getModel()) {
            $this->form->setModel($this->parent->getModel());
        }

        $this->form->setFormOptions([
            'name' => $this->name
        ])->rebuildFields();

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
