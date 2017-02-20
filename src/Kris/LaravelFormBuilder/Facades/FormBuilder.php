<?php

namespace Kris\LaravelFormBuilder\Facades;

use Illuminate\Support\Facades\Facade;

class FormBuilder extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return 'laravel-form-builder';
    }
}
