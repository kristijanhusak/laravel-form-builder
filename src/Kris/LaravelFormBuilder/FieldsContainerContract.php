<?php namespace Kris\LaravelFormBuilder;

interface FieldsContainerContract {

    /**
     * @return FormField
     */
    public function getField($name);

    /**
     * @return FormField[]
     */
    public function getFields();

    /**
     * @return bool
     */
    public function has($name);

}
