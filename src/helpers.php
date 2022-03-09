<?php

use Kris\LaravelFormBuilder\Fields\FormField;
use Kris\LaravelFormBuilder\Form;

if(!function_exists('getFormBuilderViewPath'))
{
    function getFormBuilderViewPath($fileName)
    {
        $p = explode('.',$fileName);
        $c = count($p);
        if($c>2 || $p[$c-1]!='php')
            throw new Exception('You should use only *.php files with this function');

        $path = base_path('resources/views/vendor/laravel-form-builder/'.$fileName);
        return file_exists($path) ? $path : __DIR__.'/views/'.$fileName;
    }
}

if(!function_exists('errorBlockPath'))
{

    function errorBlockPath()
    {
        return getFormBuilderViewPath('errors.php');
    }

}

if(!function_exists('helpBlockPath'))
{

    function helpBlockPath()
    {
        return getFormBuilderViewPath('help_block.php');
    }

}

if (!function_exists('form')) {

    function form(Form $form, array $options = [])
    {
        return $form->renderForm($options);
    }

}

if (!function_exists('form_start')) {

    function form_start(Form $form, array $options = [])
    {
        return $form->renderForm($options, true, false, false);
    }

}

if (!function_exists('form_end')) {

    function form_end(Form $form, $showFields = true)
    {
        return $form->renderRest(true, $showFields);
    }

}

if (!function_exists('form_rest')) {

    function form_rest(Form $form)
    {
        return $form->renderRest(false);
    }

}

if (!function_exists('form_until')) {

    function form_until(Form $form, $field_name)
    {
        return $form->renderUntil($field_name, false);
    }

}

if (!function_exists('form_row')) {

    function form_row(FormField $formField, array $options = [])
    {
        return $formField->render($options);
    }

}

if (!function_exists('form_rows')) {
    function form_rows(Form $form, array $fields, array $options = [])
    {
        return implode(array_map(function($field) use ($form, $options) {
            return $form->has($field) ? $form->getField($field)->render($options) : '';
        }, $fields));
    }
}

if (!function_exists('form_label')) {

    function form_label(FormField $formField, array $options = [])
    {
        return $formField->render($options, true, false, false);
    }

}

if (!function_exists('form_widget')) {

    function form_widget(FormField $formField, array $options = [])
    {
        return $formField->render($options, false, true, false);
    }

}

if (!function_exists('form_errors')) {

    function form_errors(FormField $formField, array $options = [])
    {
        return $formField->render($options, false, false, true);
    }

}

if (!function_exists('form_fields')) {

    function form_fields(Form $form, array $options = [])
    {
        return $form->renderForm($options, false, true, false);
    }

}
