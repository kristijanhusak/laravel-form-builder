<?php namespace  Kris\LaravelFormBuilder\Fields;


class ButtonType extends FormField
{
    protected function getTemplate()
    {
        return 'laravel-form-builder::button';
    }

    protected function getDefaults()
    {
        return [
            'attr' => ['type' => $this->type]
        ];
    }

}
