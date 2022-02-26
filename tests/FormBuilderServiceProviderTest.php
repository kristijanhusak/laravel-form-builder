<?php

use Collective\Html\FormBuilder as LaravelForm;
use Collective\Html\HtmlBuilder;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderServiceProvider;
use Kris\LaravelFormBuilder\FormHelper;

class FormBuilderServiceProviderTest extends FormBuilderTestCase
{
    /** @test */
    public function it_provides(): void
    {
        $provider = new FormBuilderServiceProvider($this->app);

        $this->assertEquals(
            [
                'laravel-form-builder',
            ],
            $provider->provides()
        );
    }

    /** @test */
    public function it_register(): void
    {
        $provider = new FormBuilderServiceProvider($this->app);

        $provider->register();

        $this->assertTrue($this->app->bound('html'));
        $this->assertInstanceOf(HtmlBuilder::class, $this->app['html']);

        $this->assertTrue($this->app->bound('form'));
        $this->assertInstanceOf(LaravelForm::class, $this->app['form']);

        $this->assertTrue($this->app->bound('laravel-form-builder'));
        $this->assertInstanceOf(FormBuilder::class, $this->app['laravel-form-builder']);

        $this->assertTrue($this->app->bound('laravel-form-helper'));
        $this->assertInstanceOf(FormHelper::class, $this->app['laravel-form-helper']);
    }
}