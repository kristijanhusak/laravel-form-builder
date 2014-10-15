<?php namespace Kris\LaravelFormBuilder;

use Illuminate\Support\ServiceProvider;

class FormBuilderServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('Kris/LaravelFormBuilder/FormBuilder', function ($app) {
            $formHelper = new FormHelper($app['view'], $app['config']);
            return new FormBuilder($app['container'], $formHelper);
        });

    }

    public function boot()
    {
        $this->package('kris/laravel-form-builder');
    }

    public function provides()
    {
        return ['laravel-form-builder'];
    }
}