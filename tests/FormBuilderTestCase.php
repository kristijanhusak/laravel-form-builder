<?php

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Contracts\Validation\Validator;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormHelper;
use Kris\LaravelFormBuilder\Form;
use Orchestra\Testbench\TestCase;
use Illuminate\Database\Eloquent\Model;

class TestModel extends Model {
    protected $fillable = ['m', 'f'];
}

abstract class FormBuilderTestCase extends TestCase {

    /**
     * @var \Illuminate\View\Factory
     */
    protected $view;

    /**
     * @var \Illuminate\Translation\Translator
     */
    protected $translator;

    /**
     * @var \Illuminate\Http\Request
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
     * @var Factory
     */
    protected $validatorFactory;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var Form
     */
    protected $plainForm;

    public function setUp()
    {
        parent::setUp();

        $this->view = $this->app['view'];
        $this->translator = $this->app['translator'];
        $this->request = $this->app['request'];
        $this->request->setLaravelSession($this->app['session.store']);
        $this->validatorFactory = $this->app['validator'];
        $this->eventDispatcher = $this->app['events'];
        $this->model = new TestModel();
        $this->config = include __DIR__.'/../src/config/config.php';

        $this->formHelper = new FormHelper($this->view, $this->translator, $this->config);
        $this->formBuilder = new FormBuilder($this->app, $this->formHelper, $this->eventDispatcher);

        $this->plainForm = $this->formBuilder->plain();
    }

    public function tearDown()
    {
        $this->view = null;
        $this->request = null;
        $this->container = null;
        $this->model = null;
        $this->config = null;
        $this->formHelper = null;
        $this->formBuilder = null;
        $this->plainForm = null;
    }

    protected function getDefaults($attr = [], $label = '', $defaultValue = null, $helpText = null)
    {
        return [
            'wrapper' => ['class' => 'form-group'],
            'attr' => array_merge(['class' => 'form-control'], $attr),
            'help_block' => ['text' => $helpText, 'tag' => 'p', 'attr' => [
                'class' => 'help-block'
            ]],
            'value' => $defaultValue,
            'default_value' => null,
            'label' => $label,
            'label_show' => true,
            'is_child' => false,
            'label_attr' => ['class' => 'control-label'],
            'errors' => ['class' => 'text-danger'],
            'wrapperAttrs' => 'class="form-group" ',
            'errorAttrs' => 'class="text-danger" ',
            'rules' => [],
            'error_messages' => []
        ];
    }

    protected function getPackageProviders($app)
    {
        return ['Kris\LaravelFormBuilder\FormBuilderServiceProvider'];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Acme' => 'Kris\LaravelFormBuilder\Facades\FormBuilder'
        ];
    }
}
