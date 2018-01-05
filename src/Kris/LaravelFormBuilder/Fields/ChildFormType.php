<?php

namespace Kris\LaravelFormBuilder\Fields;

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
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @inheritdoc
     */
    protected function getDefaults()
    {
        return [
            'class' => null,
            'value' => null,
            'formOptions' => [],
            'data' => [],
            'exclude' => []
        ];
    }

    /**
     * @inheritdoc
     */
    public function getAllAttributes()
    {
        // Collect all children's attributes.
        return $this->parent->getFormHelper()->mergeAttributes($this->children);
    }

    /**
     * Allow form-specific value alters.
     *
     * @param  array $values
     * @return void
     */
    public function alterFieldValues(array &$values)
    {
        $this->parent->getFormHelper()->alterFieldValues($this->form, $values);
    }

    /**
     * Allow form-specific valid alters.
     *
     * @param  Form  $mainForm
     * @param  bool  $isValid
     * @return void
     */
    public function alterValid(Form $mainForm, &$isValid)
    {
        $this->parent->getFormHelper()->alterValid($this->form, $mainForm, $isValid);
    }

    /**
     * @return mixed|void
     */
    protected function createChildren()
    {
        $this->form = $this->getClassFromOptions();

        if ($this->form->getFormOption('files')) {
            $this->parent->setFormOption('files', true);
        }
        $model = $this->getOption($this->valueProperty);
        if ($this->isValidValue($model)) {
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
            return $this->form->setName($this->name);
        }

        $class = $this->getOption('class');

        if (!$class) {
            throw new \InvalidArgumentException(
                'Please provide full name or instance of Form class.'
            );
        }

        if (is_string($class)) {
            $options = [
                'model' => $this->getOption($this->valueProperty) ?: $this->parent->getModel(),
                'name' => $this->name,
                'language_name' => $this->parent->getLanguageName(),
                'translation_template' => $this->parent->getTranslationTemplate(),
            ];

            if (!$this->parent->clientValidationEnabled()) {
                $options['client_validation'] = false;
            }

            if (!$this->parent->haveErrorsEnabled()) {
                $options['errors_enabled'] = false;
            }

            $formOptions = array_merge($options, $this->getOption('formOptions'));

            $data = array_merge($this->parent->getData(), $this->getOption('data'));

            return $this->parent->getFormBuilder()->create($class, $formOptions, $data);
        }

        if ($class instanceof Form) {
            $class->setName($this->name, false);
            $class->setModel($class->getModel() ?: $this->parent->getModel());

            if (!$class->getData()) {
                $class->addData($this->parent->getData());
            }

            if (!$class->getLanguageName()) {
                $class->setLanguageName($this->parent->getLanguageName());
            }

            if (!$class->getTranslationTemplate()) {
                $class->setTranslationTemplate($this->parent->getTranslationTemplate());
            }

            if (!$this->parent->clientValidationEnabled()) {
                $class->setClientValidationEnabled(false);
            }

            if (!$this->parent->haveErrorsEnabled()) {
                $class->setErrorsEnabled(false);
            }

            return $class->setName($this->name);
        }

        throw new \InvalidArgumentException(
            'Class provided does not exist or it passed in wrong format.'
        );
    }

    /**
     * @inheritdoc
     */
    public function removeChild($key)
    {
        if ($this->getChild($key)) {
            $this->form->remove($key);
            return parent::removeChild($key);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function getRenderData() {
        $data = parent::getRenderData();
        $data['child_form'] = $this->form;
        return $data;
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

    /**
     * Check if provided value is valid for this type.
     *
     * @return bool
     */
    protected function isValidValue($value)
    {
        return $value !== null;
    }
}
