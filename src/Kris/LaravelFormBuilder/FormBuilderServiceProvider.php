<?php namespace Kris\LaravelFormBuilder;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Html\FormBuilder as LaravelForm;
use Illuminate\Html\HtmlBuilder;
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
        $this->bindHtmlIfNeeded();
        $this->bindFormIfNeeded();

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
     * @return array
     */
    public function provides()
    {
        return ['laravel-form-builder'];
    }

    /**
     * Add Laravel Form to container if not already set
     */
    private function bindFormIfNeeded()
    {
        if (!$this->app->offsetExists('form')) {

            $this->app->bindShared('form', function($app) {

                $form = new LaravelForm($app['html'], $app['url'], $app['session.store']->getToken());

                return $form->setSessionStore($app['session.store']);
            });

            AliasLoader::getInstance()->alias(
                'Form',
                'Illuminate\Html\FormFacade'
            );
        }
    }

    /**
     * Add Laravel Html to container if not already set
     */
    private function bindHtmlIfNeeded()
    {
        if (!$this->app->offsetExists('html')) {

            $this->app->bindShared('html', function($app) {
                return new HtmlBuilder($app['url']);
            });

            AliasLoader::getInstance()->alias(
                'Html',
                'Illuminate\Html\HtmlFacade'
            );
        }
    }
}
