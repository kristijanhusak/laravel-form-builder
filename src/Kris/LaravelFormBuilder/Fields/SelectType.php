<?php namespace  Kris\LaravelFormBuilder\Fields;

class SelectType extends FormField
{

    protected function getTemplate()
    {
        return 'select';
    }

    public function getDefaults()
    {
        return [
            'choices' => [],
            'selected' => null
        ];
    }
}
