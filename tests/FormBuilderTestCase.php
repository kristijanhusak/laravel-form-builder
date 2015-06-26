<?php

use Illuminate\Contracts\Container\Container;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormHelper;
use Kris\LaravelFormBuilder\Form;

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

    /**
     * @var Container
     */
    protected $container;

    /**
     * Model
     */
    protected $model;

    /**
     * @var FormBuilder
     */
    protected $formBuilder;

    /**
     * @var Form
     */
    protected $plainForm;

    public function setUp()
    {
        $this->view = Mockery::mock('Illuminate\Contracts\View\Factory');
        $this->request = Mockery::mock('Illuminate\Http\Request');
        $this->container = Mockery::mock('Illuminate\Contracts\Container\Container');
        $this->model = Mockery::mock('Illuminate\Database\Eloquent\Model');
        $this->config = include __DIR__.'/../src/config/config.php';

        $session = Mockery::mock('Symfony\Component\HttpFoundation\Session\SessionInterface');
        $session->shouldReceive('get')->zeroOrMoreTimes()->andReturnSelf();
        $session->shouldReceive('has')->zeroOrMoreTimes()->andReturn(true);

        $this->request->shouldReceive('getSession')->zeroOrMoreTimes()->andReturn($session);

        $this->formHelper = new FormHelper($this->view, $this->request, $this->config);
        $this->formBuilder = new FormBuilder($this->container, $this->formHelper);

        $this->plainForm = $this->setupForm(new Form());
    }

    public function tearDown()
    {
        Mockery::close();
        $this->view = null;
        $this->request = null;
        $this->container = null;
        $this->model = null;
        $this->config = null;
        $this->formHelper = null;
        $this->formBuilder = null;
        $this->plainForm = null;
    }

    protected function fieldExpetations($name, $expectedViewData, $templatePrefix = null, $renderReturn = null) {
        $viewRenderer = Mockery::mock('Illuminate\Contracts\View\View');

        if ($renderReturn) {
            $viewRenderer->shouldReceive('render')->andReturn($renderReturn);
        } else {
            $viewRenderer->shouldReceive('render');
        }

        if (!$templatePrefix) {
            $templatePrefix = 'laravel-form-builder::'.$name;
        }

        $this->view->shouldReceive('make')
            ->with($templatePrefix, $expectedViewData)
            ->andReturn($viewRenderer);
    }

    protected function getDefaults($attr = [], $id = '', $label = '', $defaultValue = null, $helpText = null)
    {
        return [
            'wrapper' => ['class' => 'form-group has-error'],
            'attr' => array_merge(['class' => 'form-control'], $attr),
            'help_block' => ['text' => $helpText, 'tag' => 'p', 'attr' => [
                'class' => 'help-block'
            ]],
            'value' => $defaultValue,
            'default_value' => null,
            'label' => $label,
            'is_child' => false,
            'label_attr' => ['class' => 'control-label'],
            'errors' => ['class' => 'text-danger'],
            'wrapperAttrs' => 'class="form-group has-error" ',
            'errorAttrs' => 'class="text-danger" '
        ];
    }

    protected function setupForm(Form $form)
    {
        $form->setFormHelper($this->formHelper)
            ->setFormBuilder($this->formBuilder);

        $form->buildForm();
        return $form;
    }
}
