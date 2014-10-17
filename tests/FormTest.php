<?php

use Kris\LaravelFormBuilder\Fields\InputType;
use Kris\LaravelFormBuilder\Form;
use Illuminate\Contracts\View\View;

class FormTest extends FormBuilderTestCase
{

    /**
     * @var Form
     */
    protected $form;

    protected $model;

    public function setUp()
    {
        parent::setUp();
        $this->form = (new Form())->setFormHelper($this->formHelper);
        $this->model = Mockery::mock('Illuminate\Database\Eloquent\Model');
    }

    /** @test */
    public function it_adds_fields()
    {
        $this->config->shouldReceive('get');

        $this->form
            ->add('name', 'text')
            ->add('description', 'textarea')
            ->add('remember', 'checkbox');

        $this->assertEquals(3, count($this->form->getFields()));

        $this->assertTrue($this->form->has('name'));
        $this->assertFalse($this->form->has('body'));

        // Accessed with magic methods
        $this->assertEquals($this->form->name, $this->form->getField('name'));

        $this->assertInstanceOf(
            'Kris\LaravelFormBuilder\Fields\InputType',
            $this->form->getField('name')
        );

        $this->assertInstanceOf(
            'Kris\LaravelFormBuilder\Fields\TextareaType',
            $this->form->getField('description')
        );

        $this->assertInstanceOf(
            'Kris\LaravelFormBuilder\Fields\CheckableType',
            $this->form->getField('remember')
        );
    }

    /** @test */
    public function it_can_remove_existing_fields_from_form_object()
    {
        $this->config->shouldReceive('get');

        $this->form
            ->add('name', 'text')
            ->add('description', 'textarea')
            ->add('remember', 'checkbox');

        $this->assertEquals(3, count($this->form->getFields()));

        $this->assertTrue($this->form->has('name'));

        $this->form->remove('name');

        $this->assertEquals(2, count($this->form->getFields()));

        $this->assertFalse($this->form->has('name'));
    }

    /** @test */
    public function it_throws_exception_when_removing_nonexisting_field()
    {
        $this->config->shouldReceive('get');
        $this->form->add('name', 'text');

        try {
            $this->form->remove('nonexisting');
        } catch (\InvalidArgumentException $e) {
            return;
        }

        $this->fail('Exception was not thrown when tried removing non existing field.');

    }

    /** @test */
    public function it_prevents_adding_fields_with_same_name()
    {
        $this->config->shouldReceive('get');

        try {
            $this->form->add('name', 'text')->add('name', 'textarea');
        } catch (\InvalidArgumentException $e) {
            return;
        }

        $this->fail('Exception was not thrown when adding fields with same name');
    }

    /** @test */
    public function it_throws_InvalidArgumentException_on_non_existing_property()
    {
        $this->config->shouldReceive('get');

        $exceptionThrown = false;

        $this->form
            ->add('name', 'text')
            ->add('description', 'textarea');

        try {
            $this->form->nonexisting;
        } catch (\InvalidArgumentException $e) {
            $exceptionThrown = true;
        }

        try {
            $this->form->getField('nonexisting');
        } catch (\InvalidArgumentException $e) {
            $exceptionThrown = true;
        }

        if ($exceptionThrown) {
            return;
        }

        $this->fail('Exception was not thrown for non existing field.');
    }

    /** @test */
    public function it_can_set_form_options_with_array_of_options()
    {

        $options = [
            'method' => 'POST',
            'url' => '/url/1',
            'class' => 'form-container',
            'model' => $this->model
        ];

        $this->form->setFormOptions($options);

        // After the setup model is removed from options
        unset($options['model']);

        $this->assertEquals($options, $this->form->getFormOptions());

        $this->assertEquals('POST', $this->form->getMethod());
        $this->assertEquals('/url/1', $this->form->getUrl());
        $this->assertInstanceOf(
            'Illuminate\Database\Eloquent\Model',
            $this->form->getModel()
        );
    }

    /** @test */
    public function it_can_set_form_options_with_setters()
    {
        $this->form->setMethod('DELETE');
        $this->form->setUrl('/posts/all');
        $this->form->setModel($this->model);

        $this->assertEquals(
            ['method' => 'DELETE', 'url' => '/posts/all'],
            $this->form->getFormOptions()
        );

        $this->assertEquals(
            $this->model,
            $this->form->getModel()
        );
    }

    /** @test */
    public function it_sets_file_option_to_true_if_file_type_added()
    {
        $this->config->shouldReceive('get');

        $this->form->add('upload_file', 'file');

        $this->assertTrue($this->form->getFormOptions()['files']);
    }

    /** @test */
    public function it_renders_the_form()
    {
        $options = [
            'method' => 'POST',
            'url' => '/someurl',
            'class' => 'has-error'
        ];


        $this->prepareRender($options);

        $this->form->renderForm($options, true, true, true);
    }

    /** @test */
    public function it_renders_rest_of_the_form()
    {
        $options = [
            'method' => 'GET',
            'url' => '/some/url/10'
        ];

        $this->prepareFieldRender('select', 'select');
        // Expect text 4 times because we are not mocking passed fields
        // We need them that way so we can test if they are passed correctly
        $this->prepareFieldRender('text', 'text', 4, false);

        $fields = [
            new InputType('name', 'text', $this->form),
            new InputType('email', 'email', $this->form),
        ];

        $this->prepareRender($options, false, true, false, $fields);

        $this->form
            ->add('gender', 'select')
            ->add('name', 'text')
            ->add('email', 'email');

        $this->form->gender->render();

        $this->form->renderRest($options);
    }

    /** @test */
    public function it_adds_custom_type()
    {
        $this->form->addCustomField('datetime', 'Some\\Namespace\\DatetimeType');

        $fieldType = $this->formHelper->getFieldType('datetime');

        $this->assertEquals('Some\\Namespace\\DatetimeType', $fieldType);
    }

    /** @test */
    public function it_prevents_adding_duplicate_custom_type()
    {
        $this->form->addCustomField('datetime', 'Some\\Namespace\\DatetimeType');

        try {
            $this->form->addCustomField('datetime', 'Some\\Namespace\\DateType');
        } catch (\InvalidArgumentException $e) {
            return;
        }

        $this->fail('Exception was not thrown when adding duplicate custom fields');
    }

    private function prepareRender(
        $formOptions = [],
        $showStart = true,
        $showFields = true,
        $showEnd = true,
        $fields = []
    ) {
        $viewRenderer = Mockery::mock('Illuminate\Contracts\View\View');

        $this->config->shouldReceive('get')->with('laravel-form-builder::defaults.form_error_class')
                     ->andReturn('has-error');

        $this->config->shouldReceive('get')->with('laravel-form-builder::form')
                     ->andReturn('laravel-form-builder::form');

        $this->view->shouldReceive('make')->with('laravel-form-builder::form')
                   ->andReturn($viewRenderer);


        $viewRenderer->shouldReceive('with')->with(
            compact('showStart', 'showFields', 'showEnd')
        )->andReturnSelf();

        $viewRenderer->shouldReceive('with')->with(
            'formOptions',
            $formOptions
        )->andReturnSelf();

        $viewRenderer->shouldReceive('with')->with('fields', $fields)
                     ->andReturnSelf();

        $viewRenderer->shouldReceive('with')->with('model', null)
                     ->andReturnSelf();

        $viewRenderer->shouldReceive('render');

    }

    private function prepareFieldRender($configViewVar, $realView, $times = 1, $mockViewMake = true)
    {
        if ($mockViewMake) {
            $viewRenderer = Mockery::mock('Illuminate\Contracts\View\View');
            $viewRenderer->shouldReceive('with')->andReturnSelf();
            $viewRenderer->shouldReceive('render');

            $this->view->shouldReceive('make')
                   ->with('laravel-form-builder::' . $realView, Mockery::any())
                   ->times($times)
                   ->andReturn($viewRenderer);
        }

        $this->config->shouldReceive('get')
            ->with('laravel-form-builder::' . $configViewVar, 'laravel-form-builder::' . $configViewVar)
            ->times($times)
            ->andReturn('laravel-form-builder::' . $realView);

        $this->config->shouldReceive('get')
            ->with('laravel-form-builder::defaults.label_class')
            ->times($times);

        $this->config->shouldReceive('get')
            ->with('laravel-form-builder::defaults.wrapper_class')
            ->times($times);

        $this->config->shouldReceive('get')
                     ->with('laravel-form-builder::defaults.wrapper_error_class');

        $this->config->shouldReceive('get')
            ->with('laravel-form-builder::defaults.field_class')
            ->times($times);

        $this->config->shouldReceive('get')
             ->with('laravel-form-builder::defaults.error_class')
             ->times($times);

    }
}