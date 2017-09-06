<?php

namespace Kris\LaravelFormBuilder\Console;

class FormGenerator
{

    /**
     * Get fields from options and create add methods from it.
     *
     * @param string|null $fields
     * @return string
     */
    public function getFieldsVariable($fields = null)
    {
        if ($fields) {
            return $this->parseFields($fields);
        }

        return '// Add fields here...';
    }

    /**
     * @param string $name
     * @return object
     */
    public function getClassInfo($name)
    {
        $explodedClassNamespace = explode('\\', $name);
        $className = array_pop($explodedClassNamespace);
        $fullNamespace = join('\\', $explodedClassNamespace);

        return (object)[
            'namespace' => $fullNamespace,
            'className' => $className
        ];
    }

    /**
     * Parse fields from string.
     *
     * @param string $fields
     * @return string
     */
    protected function parseFields($fields)
    {
        $fieldsArray = explode(',', $fields);
        $text = '$this'."\n";

        foreach ($fieldsArray as $field) {
            $text .= $this->prepareAdd($field, end($fieldsArray) == $field);
        }

        return $text.';';
    }

    /**
     * Prepare template for single add field.
     *
     * @param string $field
     * @param bool $isLast
     * @return string
     */
    protected function prepareAdd($field, $isLast = false)
    {
        $field = trim($field);
        list($name, $type) = explode(':', $field);
        $textArr = [
            "            ->add('",
            $name,
            "', '",
            $type,
            "')",
            ($isLast) ? "" : "\n"
        ];

        return join('', $textArr);
    }

}
