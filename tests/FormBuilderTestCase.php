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
    protected $request;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var FormHelper
     */
    protected $formHelper;


    public function setUp()
    {
        $this->view = Mockery::mock('Illuminate\Contracts\View\Factory');
        $this->request = Mockery::mock('Illuminate\Http\Request');
        $this->config = include __DIR__.'/../src/config/config.php';

        $session = Mockery::mock('Symfony\Component\HttpFoundation\Session\SessionInterface');
        $session->shouldReceive('get')->zeroOrMoreTimes()->andReturnSelf();
        $session->shouldReceive('has')->zeroOrMoreTimes()->andReturn(true);

        $this->request->shouldReceive('getSession')->zeroOrMoreTimes()->andReturn($session);

        $this->formHelper = new FormHelper($this->view, $this->request, $this->config);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    protected function fieldExpetations($name, $expectedViewData, $templatePrefix = 'laravel-form-builder::')
    {
        $viewRenderer = Mockery::mock('Illuminate\Contracts\View\View');
        $viewRenderer->shouldReceive('render');

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