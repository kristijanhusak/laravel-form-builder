<?php namespace  Kris\LaravelFormBuilder\Fields;

class StaticType extends FormField
{
    public function render(array $options = [], $showLabel = true, $showField = true, $showError = false)
    {
        $this->setupStaticOptions($options);
        return parent::render($options, $showLabel, $showField, $showError);
    }

    /**
     * Setup static field options
     */
    private function setupStaticOptions(&$options)
    {
        $options['elemAttrs'] = $this->formHelper->prepareAttributes($this->getOption('attr'));
        $options['labelAttrs'] = $this->formHelper->prepareAttributes($this->getOption('label_attr'));
        $this->options = $this->formHelper->mergeOptions($this->options, $options);

        if (!$this->getOption('default_value')) {
            $options['default_value'] = $this->getModelValueAttribute($this->parent->getModel(), $this->getName());
        }
    }

    /**
     * @inheritdoc
     */
    protected function getTemplate()
    {
        return 'static';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaults()
    {
        return [
            'tag' => 'div',
            'attr' => ['class' => 'form-control-static', 'id' => $this->getName()]
        ];
    }
}
