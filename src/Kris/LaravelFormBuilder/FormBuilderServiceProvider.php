<?php

namespace Kris\LaravelFormBuilder;

use Illuminate\Foundation\AliasLoader;
use Collective\Html\FormBuilder as LaravelForm;
use Collective\Html\HtmlBuilder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;
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

        $this->registerHtmlIfNeeded();
        $this->registerFormIfHeeded();

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

        $form->macro('customLabel', function($name, $value, $options = []) use ($form) {
            if (isset($options['for']) && $for = $options['for']) {
                unset($options['for']);
                return $form->label($for, $value, $options);
            }

            return $form->label($name, $value, $options);
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

    /**
     * Add Laravel Form to container if not already set.
     *
     * @return void
     */
    private function registerFormIfHeeded()
    {
        if (!$this->app->offsetExists('form')) {

            $this->app->singleton('form', function($app) {

                // LaravelCollective\HtmlBuilder 5.2 is not backward compatible and will throw an exception
                $version = substr(Application::VERSION, 0, 3);

                if(Str::is('5.4', $version)) {
                    $form = new LaravelForm($app[ 'html' ], $app[ 'url' ], $app[ 'view' ], $app[ 'session.store' ]->token());
                }
                else if (Str::is('5.0', $version) || Str::is('5.1', $version)) {
                    $form = new LaravelForm($app[ 'html' ], $app[ 'url' ], $app[ 'session.store' ]->token());
                }
                else {
                    $form = new LaravelForm($app['html'], $app['url'], $app['view'], $app['session.store']->token());
                }

                return $form->setSessionStore($app['session.store']);
            });

            if (! $this->aliasExists('Form')) {

                AliasLoader::getInstance()->alias(
                    'Form',
                    'Collective\Html\FormFacade'
                );
            }
        }
    }

    /**
     * Add Laravel Html to container if not already set.
     */
    private function registerHtmlIfNeeded()
    {
        if (!$this->app->offsetExists('html')) {

            $this->app->singleton('html', function($app) {
                return new HtmlBuilder($app['url'], $app['view']);
            });

            if (! $this->aliasExists('Html')) {

                AliasLoader::getInstance()->alias(
                    'Html',
                    'Collective\Html\HtmlFacade'
                );
            }
        }
    }

    /**
     * Check if an alias already exists in the IOC.
     *
     * @param string $alias
     * @return bool
     */
    private function aliasExists($alias)
    {
        return array_key_exists($alias, AliasLoader::getInstance()->getAliases());
    }

}
