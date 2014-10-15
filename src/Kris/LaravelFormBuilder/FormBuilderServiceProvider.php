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
        $this->bindFormIfNeeded();

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

    private function bindFormIfNeeded()
    {
        if (!$this->app['form']) {
            $this->app->bindShared('form', function($app)
            {
                $form = new FormBuilder($app['html'], $app['url'], $app['session.store']->getToken());

                return $form->setSessionStore($app['session.store']);
            });

            AliasLoader::getInstance()->alias(
                'Form',
                'Illuminate\Html\FormFacade'
            );
        }

    }
}