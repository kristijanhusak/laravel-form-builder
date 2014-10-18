<?php  namespace Kris\LaravelFormBuilder\Console;

class FormGenerator
{

    /**
     * Get fields from options and create add methods from it
     *
     * @param null $fields
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
        if (strpos($name, '/') !== false) {
            $explodedClassNamespace = explode('\\', $name);
            $mainNamespace = array_shift($explodedClassNamespace);
            $overleftNamespace = explode('/', join('', $explodedClassNamespace));
            // Get class name from end of overleft namespace
            $className = array_pop($overleftNamespace);
            // Merge main namespace with overleft and remove any backslashes at the end
            $fullNamespacedPath = rtrim($mainNamespace.'\\'.join('\\', $overleftNamespace), '\\');
        } else {
            list($fullNamespacedPath, $className) = explode('\\', $name);
        }

        return (object)[
            'namespace' => $fullNamespacedPath,
            'className' => $className
        ];
    }

    /**
     * Parse fields from string
     *
     * @param $fields
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
     * Prepare template for single add field
     *
     * @param      $field
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
