<?php namespace  Kris\LaravelFormBuilder\Fields;


class CheckableType extends FormField
{

    protected function getTemplate()
    {
        return 'laravel-form-builder::'.$this->type;
    }

    public function getDefaults()
    {
        return [
            'attr' => ['class' => null],
            'is_child' => false,
            'default_value' => null,
            'label_attr' => ['id' => '', 'for' => ''],
            'checked' => false
        ];
    }

}
