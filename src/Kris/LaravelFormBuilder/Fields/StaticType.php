<?php

namespace  Kris\LaravelFormBuilder\Fields;

class StaticType extends FormField
{
    /**
     * @inheritdoc
     */
    public function render(array $options = [], $showLabel = true, $showField = true, $showError = false)
    {
        $this->setupStaticOptions($options);
        return parent::render($options, $showLabel, $showField, $showError);
    }

    /**
     * Setup static field options.
     *
     * @param array $options
     * @return void
     */
    private function setupStaticOptions(&$options)
    {
        $options['elemAttrs'] = $this->formHelper->prepareAttributes($this->getOption('attr'));
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

    /**
     * @inheritdoc
     */
    public function getAllAttributes()
    {
        // No input allowed for Static fields.
        return [];
    }
}
