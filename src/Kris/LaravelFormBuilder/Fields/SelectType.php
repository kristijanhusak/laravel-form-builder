<?php namespace  Kris\LaravelFormBuilder\Fields;

class SelectType extends FormField
{

    protected function getTemplate()
    {
        return 'select';
    }

    protected function setValue($val)
    {
        if (!$this->options['selected']) {
            $this->options['selected'] = $val;
        }

        return $this;
    }

    public function getDefaults()
    {
        return [
            'choices' => [],
            'empty_value' => null,
            'selected' => null
        ];
    }
}
