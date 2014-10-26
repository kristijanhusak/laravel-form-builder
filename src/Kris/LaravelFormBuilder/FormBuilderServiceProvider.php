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

        $this->registerFormHelper();

        $this->app->bindShared('laravel-form-builder', function ($app) {

            return new FormBuilder($app, $app['laravel-form-helper']);
        });
    }

    protected function registerFormHelper()
    {
        $this->app->bindShared('laravel-form-helper', function ($app) {

            $configuration = $app['config']->get('laravel-form-builder::config');

            return new FormHelper($app['view'], $app['request'], $configuration);
        });

        $this->app->alias('laravel-form-helper', 'Kris\LaravelFormBuilder\FormHelper');
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
