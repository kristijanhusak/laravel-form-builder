<?php

namespace  Kris\LaravelFormBuilder\Fields;

class ButtonGroupType extends FormField
{

    /**
     * The path the template.
     *
     * @return string
     */
    protected function getTemplate()
    {
        return 'buttongroup';
    }

    /**
     * @inheritdoc
     */
    public function render(array $options = [], $showLabel = true, $showField = true, $showError = true)
    {
        $options['splitted']    = $this->getOption('splitted', false);
        $options['size']        = $this->getOption('size', 'md');
        $options['buttons']     = $this->getOption('buttons', []);

        return parent::render($options, $showLabel, $showField, $showError);
    }
}
