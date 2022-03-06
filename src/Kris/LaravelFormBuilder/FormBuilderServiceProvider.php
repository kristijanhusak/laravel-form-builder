<?php

namespace Kris\LaravelFormBuilder;

use Illuminate\Foundation\AliasLoader;
use Collective\Html\FormBuilder as LaravelForm;
use Collective\Html\HtmlBuilder;
use Illuminate\Support\ServiceProvider;
use Kris\LaravelFormBuilder\Traits\ValidatesWhenResolved;
use Kris\LaravelFormBuilder\Form;

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

        $this->mergeConfigFrom(
            __DIR__ . '/../../config/config.php',
            'laravel-form-builder'
        );

        $this->registerFormHelper();

        $this->app->singleton('laravel-form-builder', function ($app) {

            return new FormBuilder($app, $app['laravel-form-helper'], $app['events']);
        });

        $this->app->alias('laravel-form-builder', 'Kris\LaravelFormBuilder\FormBuilder');

        $this->app->afterResolving(Form::class, function ($object, $app) {
            $request = $app->make('request');

            if (in_array(ValidatesWhenResolved::class, class_uses($object)) && $request->method() !== 'GET') {
                $form = $app->make('laravel-form-builder')->setDependenciesAndOptions($object);
                $form->buildForm();
                $form->redirectIfNotValid();
            }
        });
    }

    /**
     * Register the form helper.
     *
     * @return void
     */
    protected function registerFormHelper()
    {
        $this->app->singleton('laravel-form-helper', function ($app) {

            $configuration = $app['config']->get('laravel-form-builder');

            return new FormHelper($app['view'], $app['translator'], $configuration);
        });

        $this->app->alias('laravel-form-helper', 'Kris\LaravelFormBuilder\FormHelper');
    }

    /**
     * Bootstrap the service.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../../views', 'laravel-form-builder');

        $this->publishes([
            __DIR__ . '/../../views' => base_path('resources/views/vendor/laravel-form-builder'),
            __DIR__ . '/../../config/config.php' => config_path('laravel-form-builder.php')
        ]);

        $form = $this->app['form'];

        $form->macro('customLabel', function($name, $value, $options = [], $escape_html = true) use ($form) {
            if (isset($options['for']) && $for = $options['for']) {
                unset($options['for']);
                return $form->label($for, $value, $options, $escape_html);
            }

            return $form->label($name, $value, $options, $escape_html);
        });
    }

    /**
     * Get the services provided by this provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return ['laravel-form-builder'];
    }

}
