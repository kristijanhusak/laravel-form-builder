<?php

namespace Kris\LaravelFormBuilder;

use Illuminate\Foundation\AliasLoader;
use Collective\Html\FormBuilder as LaravelForm;
use Collective\Html\HtmlBuilder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Kris\LaravelFormBuilder\Traits\ValidatesWhenResolved;
use Kris\LaravelFormBuilder\Form;

class FormBuilderServiceProvider extends ServiceProvider
{
    protected const HTML_ABSTRACT = 'html';
    protected const FORM_ABSTRACT = 'form';
    protected const BUILDER_ABSTRACT = 'laravel-form-builder';
    protected const HELPER_ABSTRACT = 'laravel-form-helper';

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
            dirname(__DIR__, 2) . '/config/config.php',
            'laravel-form-builder'
        );

        $this->registerFormHelper();
        $this->registerFormBuilder();
    }

    /**
     * Register the form helper.
     *
     * @return void
     */
    protected function registerFormBuilder()
    {
        $abstract = static::BUILDER_ABSTRACT;

        $formBuilder = $this->getFormBuilderClass();

        $this->app->singleton($abstract, function ($app) use ($formBuilder) {
            return new $formBuilder($app, $app[static::HELPER_ABSTRACT], $app['events']);
        });

        $this->app->alias($abstract, $formBuilder);

        $this->app->afterResolving(Form::class, function ($object, $app) use ($abstract) {
            $request = $app->make('request');

            if (in_array(ValidatesWhenResolved::class, class_uses($object), true) && $request->method() !== 'GET') {
                $form = $app->make($abstract)->setDependenciesAndOptions($object);
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
        $abstract = static::HELPER_ABSTRACT;

        $formHelper = $this->getFormHelperClass();

        $this->app->singleton($abstract, function ($app) use ($formHelper) {
            $configuration = $app['config']->get('laravel-form-builder');

            return new $formHelper($app['view'], $app['translator'], $configuration);
        });

        $this->app->alias($abstract, $formHelper);
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

        $form = $this->app[static::FORM_ABSTRACT];

        $form->macro('customLabel', function ($name, $value, $options = []) use ($form) {
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
        if (!$this->app->offsetExists(static::FORM_ABSTRACT)) {

            $this->app->singleton(static::FORM_ABSTRACT, function ($app) {

                // LaravelCollective\HtmlBuilder 5.2 is not backward compatible and will throw an exception
                $version = substr(Application::VERSION, 0, 3);

                if (Str::is('5.4', $version)) {
                    $form = new LaravelForm($app[static::HTML_ABSTRACT], $app['url'], $app['view'], $app['session.store']->token());
                } else if (Str::is('5.0', $version) || Str::is('5.1', $version)) {
                    $form = new LaravelForm($app[static::HTML_ABSTRACT], $app['url'], $app['session.store']->token());
                } else {
                    $form = new LaravelForm($app[static::HTML_ABSTRACT], $app['url'], $app['view'], $app['session.store']->token());
                }

                return $form->setSessionStore($app['session.store']);
            });

            if (!$this->aliasExists('Form')) {

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
        if (!$this->app->offsetExists(static::HTML_ABSTRACT)) {

            $this->app->singleton(static::HTML_ABSTRACT, function ($app) {
                return new HtmlBuilder($app['url'], $app['view']);
            });

            if (!$this->aliasExists('Html')) {

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

    /**
     * @return class-string
     */
    protected function getFormBuilderClass()
    {
        $expectedClass = FormBuilder::class;
        $defaultClass = FormBuilder::class;

        $class = $this->app['config']->get('laravel-form-builder.form_builder', $defaultClass);

        if (!class_exists($class)) {
            throw new InvalidArgumentException("Class {$class} does not exist");
        }

        if ($class !== $expectedClass && !is_subclass_of($class, $expectedClass)) {
            throw new InvalidArgumentException("Class {$class} must extend " . $expectedClass);
        }

        return $class;
    }

    /**
     * @return class-string
     */
    protected function getFormHelperClass()
    {
        $expectedClass = FormHelper::class;
        $defaultClass = FormHelper::class;

        $class = $this->app['config']->get('laravel-form-builder.helper', $defaultClass);

        if (!class_exists($class)) {
            throw new InvalidArgumentException("Class {$class} does not exist");
        }

        if ($class !== $expectedClass && !is_subclass_of($class, $expectedClass)) {
            throw new InvalidArgumentException("Class {$class} must extend " . $expectedClass);
        }

        return $class;
    }
}
