<?php

use Kris\LaravelFormBuilder\Form;
use Kris\LaravelFormBuilder\FormBuilder;

class FormBuilderTest extends FormBuilderTestCase
{

    protected $container;

    protected $formBuilder;

    protected $model;

    protected $form;

    public function setUp()
    {
        parent::setUp();
        $this->container = Mockery::mock('Illuminate\Contracts\Container\Container');
        $this->model = Mockery::mock('Illuminate\Database\Eloquent\Model');
        $this->formBuilder = new FormBuilder($this->container, $this->formHelper);
        $this->form = new Form();
    }

    /** @test */
    public function it_creates_plain_form_and_sets_options_on_it()
    {
        $this->container->shouldReceive('make')
            ->with('Kris\LaravelFormBuilder\Form')
            ->andReturn($this->form);

        $options = [
            'method' => 'PUT',
            'url' => '/some/url/1',
            'model' => $this->model
        ];

        $plainForm = $this->formBuilder->plain($options);

        $this->assertEquals('PUT', $plainForm->getMethod());
        $this->assertEquals('/some/url/1', $plainForm->getUrl());
        $this->assertEquals($this->model, $plainForm->getModel());
        $this->assertNull($plainForm->buildForm());
    }

    /** @test */
    public function it_creates_custom_form_and_sets_options_on_it()
    {
        $customForm = new CustomForm($this->formHelper);

        $this->container->shouldReceive('make')
                ->with('CustomForm')
                ->andReturn($customForm);

        $options = [
            'method' => 'POST',
            'url' => '/posts',
        ];

        $customFormInstance = $this->formBuilder->create('CustomForm', $options);

        $this->assertEquals('POST', $customFormInstance->getMethod());
        $this->assertEquals('/posts', $customFormInstance->getUrl());
        $this->assertInstanceOf('Kris\\LaravelFormBuilder\\Form', $customFormInstance);
        $this->assertArrayHasKey('title', $customForm->getFields());
        $this->assertArrayHasKey('body', $customForm->getFields());
    }

    /** @test */
    public function it_can_set_form_helper_once_and_call_build_form()
    {
        $form = new CustomForm();
        $form->setFormHelper($this->formHelper);
        $form->buildForm();

        $this->assertEquals($this->formHelper, $form->getFormHelper());
        $this->assertArrayHasKey('title', $form->getFields());
        $this->assertArrayHasKey('body', $form->getFields());
    }
}

class CustomForm extends Form {

    public function buildForm()
    {
        $this->add('title', 'text')
            ->add('body', 'textarea');
    }
}