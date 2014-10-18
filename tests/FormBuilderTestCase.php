<?php

use Kris\LaravelFormBuilder\FormHelper;

abstract class FormBuilderTestCase extends PHPUnit_Framework_TestCase {

    /**
     * @var Mockery\MockInterface
     */
    protected $view;

    /**
     * @var Mockery\MockInterface
     */
    protected $config;

    protected $request;

    /**
     * @var FormHelper
     */
    protected $formHelper;


    public function setUp()
    {
        $this->view = Mockery::mock('Illuminate\Contracts\View\Factory');
        $this->config = Mockery::mock('Illuminate\Contracts\Config\Repository');
        $this->request = Mockery::mock('Illuminate\Http\Request');

        $session = Mockery::mock('Symfony\Component\HttpFoundation\Session\SessionInterface');
        $session->shouldReceive('get')->zeroOrMoreTimes()->andReturnSelf();
        $session->shouldReceive('has')->zeroOrMoreTimes()->andReturn(true);

        $this->config->shouldReceive('get')->with('laravel-form-builder::custom_fields')
            ->andReturn([]);

        $this->request->shouldReceive('getSession')->zeroOrMoreTimes()->andReturn($session);

        $this->formHelper = new FormHelper($this->view, $this->config, $this->request);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    protected function fieldExpetations($name, $expectedViewData, $templatePrefix = 'laravel-form-builder::')
    {
        $viewRenderer = Mockery::mock('Illuminate\Contracts\View\View');
        $viewRenderer->shouldReceive('render');

        $this->config->shouldReceive('get')
            ->with('laravel-form-builder::'.$name, 'laravel-form-builder::' . $name)
            ->andReturn('laravel-form-builder::'.$name);

        $this->config->shouldReceive('get')
            ->with('laravel-form-builder::defaults.wrapper_class')
            ->andReturn('form-group');

        $this->config->shouldReceive('get')
            ->with('laravel-form-builder::defaults.wrapper_error_class')
            ->andReturn('has-error');

        $this->config->shouldReceive('get')
            ->with('laravel-form-builder::defaults.label_class')
            ->andReturn('control-label');

        $this->config->shouldReceive('get')
            ->with('laravel-form-builder::defaults.field_class')
            ->andReturn('form-control');

        $this->config->shouldReceive('get')
            ->with('laravel-form-builder::defaults.error_class')
            ->andReturn('text-danger');

        $this->view->shouldReceive('make')
            ->with(
               $templatePrefix == 'laravel-form-builder::' ? 'laravel-form-builder::'.$name : $templatePrefix,
               $expectedViewData
            )
            ->andReturn($viewRenderer);
    }

    protected function getDefaults($attr = [], $label = '', $defaultValue = null)
    {
        return [
            'wrapper' => ['class' => 'form-group has-error'],
            'attr' => array_merge(['class' => 'form-control'], $attr),
            'default_value' => $defaultValue,
            'label' => $label,
            'label_attr' => ['class' => 'control-label'],
            'errors' => ['class' => 'text-danger'],
            'wrapperAttrs' => 'class="form-group has-error" ',
            'errorAttrs' => 'class="text-danger" '
        ];
    }
}