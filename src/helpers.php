<?php

use Kris\LaravelFormBuilder\Fields\FormField;
use Kris\LaravelFormBuilder\Form;

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

if (!function_exists('array_merge_map_recursive')) {
    /**
     * Array merge recursive where a callback is called when values exist in both
     * arrays but at least one of them is not an array.
     */
    function array_merge_map_recursive()
    {
        $arrays = func_get_args();
        $callback = array_shift($arrays);
        $arr1 = array_shift($arrays);
        $arr2 = array_shift($arrays);
        $merged = [];

        $keys = array_merge(array_keys($arr1), array_keys($arr2));

        if (!is_array($arr1))
        {
            $merged = $arr2;
        }
        elseif (!is_array($arr2))
        {
            $merged = $arr1;
        }
        else
        {
            foreach ($keys as $key)
            {
                // Value doesn't exist in arr1, use arr2.
                if (!array_key_exists($key, $arr1))
                {
                    $merged[$key] = $arr2[$key];
                }
                // Value doesn't exist in arr2, use arr1.
                elseif (!array_key_exists($key, $arr2))
                {
                    $merged[$key] = $arr1[$key];
                }
                // Value exists in both and are both arrays.
                elseif (is_array($arr1[$key]) && is_array($arr2[$key]))
                {
                    $merged[$key] = array_merge_map_recursive($callback, $arr1[$key], $arr2[$key]);
                }
                // Value exists in both and at least one is not an array.
                else
                {
                    $merged[$key] = $callback($key, $arr1[$key], $arr2[$key]);
                }
            }
        }

        // Merge arrays 3, 4...
        if (!empty($arrays))
        {
            $merged = call_user_func_array('array_merge_map_recursive', array_merge([$callback], $arrays));
        }

        return $merged;
    }
}
