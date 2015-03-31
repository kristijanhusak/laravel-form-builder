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
     * @inheritdoc
     */
    public function render(array $options = [], $showLabel = true, $showField = true, $showError = true)
    {
        $this->rebuild();
        return parent::render($options, $showLabel, $showField, $showError);
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

        if ($this->form->getFormOption('files')) {
            $this->parent->setFormOption('files', true);
        }

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
        if ($this->form instanceof Form) {
            return $this->form;
        }

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

    /**
     * @param $method
     * @param $arguments
     *
     * @return Form|null
     */
    public function __call($method, $arguments)
    {
        if (method_exists($this->form, $method)) {
            return call_user_func_array([$this->form, $method], $arguments);
        }

        throw new \BadMethodCallException(
            'Method ['.$method.'] does not exist on form ['.get_class($this->form).']'
        );
    }
}
