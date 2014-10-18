<?php  namespace Kris\LaravelFormBuilder\Facades;

use Illuminate\Support\Facades\Facade;

class FormBuilder extends Facade {

    public static function getFacadeAccessor()
    {
        return 'laravel-form-builder';
    }
}
