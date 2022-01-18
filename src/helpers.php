<?php

use Kris\LaravelFormBuilder\Fields\FormField;
use Kris\LaravelFormBuilder\Form;

if (!function_exists('getFormBuilderViewPath')) {
    /**
     * Get the path to the form builder view.
     *
     * @return string
     * @throws Exception
     */
    function getFormBuilderViewPath($fileName)
    {
        $p = explode('.', $fileName);
        $c = count($p);

        if ($c > 2 || $p[$c - 1] !== 'php') {
            throw new Exception('You should use only *.php files with this function');
        }

        $path = base_path('resources/views/vendor/laravel-form-builder/' . $fileName);

        return file_exists($path) ? $path : __DIR__ . '/views/' . $fileName;
    }
}

if (!function_exists('errorBlockPath')) {
    /**
     * Get the path to the error block view.
     *
     * @return string
     * @throws Exception
     */
    function errorBlockPath()
    {
        return getFormBuilderViewPath('errors.php');
    }
}

if (!function_exists('helpBlockPath')) {
    /**
     * Get the path to the help block view.
     *
     * @return string
     * @throws Exception
     */
    function helpBlockPath()
    {
        return getFormBuilderViewPath('help_block.php');
    }
}

if (!function_exists('form')) {
    /**
     * Render full form.
     *
     * @param Form $form
     * @param array $options
     * @return string
     */
    function form(Form $form, array $options = [])
    {
        return $form->renderForm($options);
    }
}

if (!function_exists('form_start')) {
    /**
     * Render form open tag.
     *
     * @param Form $form
     * @param array $options
     * @return string
     */
    function form_start(Form $form, array $options = [])
    {
        return $form->renderForm($options, true, false, false);
    }
}

if (!function_exists('form_end')) {
    /**
     * Render form end tag.
     *
     * @param Form $form
     * @param bool $showFields
     * @return string
     */
    function form_end(Form $form, $showFields = true)
    {
        return $form->renderRest(true, $showFields);
    }
}

if (!function_exists('form_rest')) {
    /**
     * Render rest of the form.
     *
     * @param Form $form
     * @return string
     */
    function form_rest(Form $form)
    {
        return $form->renderRest(false);
    }
}

if (!function_exists('form_until')) {
    /**
     * Renders the rest of the form up until the specified field name.
     *
     * @param Form $form
     * @param string $field_name
     * @return string
     */
    function form_until(Form $form, $field_name)
    {
        return $form->renderUntil($field_name, false);
    }
}

if (!function_exists('form_row')) {
    /**
     * Render the field.
     *
     * @param FormField $formField
     * @param array $options
     * @return string
     */
    function form_row(FormField $formField, array $options = [])
    {
        return $formField->render($options);
    }
}

if (!function_exists('form_rows')) {
    /**
     * Render the fields.
     *
     * @param Form $form
     * @param array $fields
     * @param array $options
     * @return string
     */
    function form_rows(Form $form, array $fields, array $options = [])
    {
        return implode(array_map(static function ($field) use ($form, $options) {
            return $form->has($field) ? $form->getField($field)->render($options) : '';
        }, $fields));
    }
}

if (!function_exists('form_label')) {
    /**
     * Render the label.
     *
     * @param FormField $formField
     * @param array $options
     * @return string
     */
    function form_label(FormField $formField, array $options = [])
    {
        return $formField->render($options, true, false, false);
    }
}

if (!function_exists('form_widget')) {
    /**
     * Render the widget.
     *
     * @param FormField $formField
     * @param array $options
     * @return string
     */
    function form_widget(FormField $formField, array $options = [])
    {
        return $formField->render($options, false, true, false);
    }
}

if (!function_exists('form_errors')) {
    /**
     * Render the errors.
     *
     * @param FormField $formField
     * @param array $options
     * @return string
     */
    function form_errors(FormField $formField, array $options = [])
    {
        return $formField->render($options, false, false, true);
    }
}

if (!function_exists('form_fields')) {
    /**
     * Render full form.
     *
     * @param Form $form
     * @param array $options
     * @return string
     */
    function form_fields(Form $form, array $options = [])
    {
        return $form->renderForm($options, false, true, false);
    }
}
