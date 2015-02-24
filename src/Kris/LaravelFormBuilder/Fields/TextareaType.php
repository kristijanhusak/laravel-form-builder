<?php namespace Kris\LaravelFormBuilder\Fields;

class TextareaType extends FormField
{

    /**
     * @inheritdoc
     */
    protected function getTemplate()
    {
        return 'textarea';
    }
}
