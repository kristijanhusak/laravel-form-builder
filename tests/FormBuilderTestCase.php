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

    /**
     * @var FormHelper
     */
    protected $formHelper;

    public function setUp()
    {
        $this->view = Mockery::mock('Illuminate\Contracts\View\Factory');
        $this->config = Mockery::mock('Illuminate\Contracts\Config\Repository');
        $this->formHelper = new FormHelper($this->view, $this->config);
    }

    public function tearDown()
    {
        Mockery::close();
    }

}