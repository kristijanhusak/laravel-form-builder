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

        $this->registerHtmlIfNeeded();
        $this->registerFormIfHeeded();

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
        $this->registerConfig();
    }

    /**
     * @return string[]
     */
    public function provides()
    {
        return ['laravel-form-builder'];
    }

    /**
     * Add Laravel Form to container if not already set
     */
    private function registerFormIfHeeded()
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
    private function registerHtmlIfNeeded()
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

     /**
     * Register our config file
     *
     * @return void
     */
    protected function registerConfig()
    {
        // The path to the user config file
        $userConfigPath = app()->configPath() . '/packages/kris/laravel-form-builder/config.php';

        // Path to the default config
        $defaultConfigPath = __DIR__ . '/../../config/config.php';

        // Load the default config
        $config = $this->app['files']->getRequire($defaultConfigPath);

        if (file_exists($userConfigPath))
        {
            // User has their own config, let's merge them properly
            $userConfig = $this->app['files']->getRequire($userConfigPath);
            $config = array_replace_recursive($config, $userConfig);
        }

        // Set each of the items like ->package() previously did
        $this->app['config']->set('laravel-form-builder::config', $config);
        $this->loadViewsFrom('laravel-form-builder', __DIR__ . '/../../views');
    }
}
