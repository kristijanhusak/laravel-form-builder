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
        $this->commands('Kris\LaravelFormBuilder\Console\FormMakeCommand');

        $this->app->bindShared('Kris/LaravelFormBuilder/FormBuilder', function ($app) {

            // Load complete config file and handle it with form helper
            $configuration = $app['config']->get('laravel-form-builder::config');

            $formHelper = new FormHelper($app['view'], $app['request'], $configuration);
            return new FormBuilder($app, $formHelper);
        });

        $this->app->alias('Kris/LaravelFormBuilder/FormBuilder', 'laravel-form-builder');

    }

    public function boot()
    {
        $this->package('kris/laravel-form-builder');
    }

    /**
     * @return string[]
     */
    public function provides()
    {
        return ['laravel-form-builder'];
    }
}
