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
    public function it_can_modify_existing_fields()
    {
        $this->form
            ->add('name', 'text')
            ->add('description', 'textarea', [
                'attr' => ['placeholder' => 'Enter text here...']
            ])
            ->add('category', 'select', [
                'choices' => [ 1 => 'category-1', 2 => 'category-2']
            ]);
        // Adds new if provided name doesn't exist
        $this->form->modify('remember', 'checkbox');

        // Modifies without complete ovewrite of options

        $this->assertEquals('textarea', $this->form->description->getType());
        $this->assertEquals(
            ['placeholder' => 'Enter text here...', 'class' => 'form-control'],
            $this->form->description->getOption('attr')
        );

        $this->form->modify('description', 'text', [
            'attr' => ['class' => 'modified-input']
        ]);

        $this->assertEquals('text', $this->form->description->getType());
        $this->assertEquals(
            ['placeholder' => 'Enter text here...', 'class' => 'modified-input'],
            $this->form->description->getOption('attr')
        );

        // Check if complete option ovewrite work
        $this->assertEquals(
            [ 1 => 'category-1', 2 => 'category-2'],
            $this->form->category->getOption('choices')
        );

        $this->assertArrayNotHasKey('expanded', $this->form->category->getOptions());

        $this->form->modify('category', 'choice', [
            'expanded' => true
        ], true);

        $this->assertNotEquals(
            [ 1 => 'category-1', 2 => 'category-2'],
            $this->form->category->getOption('choices')
        );

        $this->assertTrue($this->form->category->getOption('expanded'));

    }

    /** @test */
    public function it_throws_exception_when_removing_nonexisting_field()
    {
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
        $this->form->setData('some_data', ['this', 'is', 'some', 'data']);

        $this->assertEquals(
            ['method' => 'DELETE', 'url' => '/posts/all'],
            $this->form->getFormOptions()
        );

        $this->assertEquals(
            $this->model,
            $this->form->getModel()
        );

         $this->assertEquals(
             ['this', 'is', 'some', 'data'],
             $this->form->getData('some_data')
         );
    }

    /** @test */
    public function it_sets_file_option_to_true_if_file_type_added()
    {
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

        $this->prepareFieldRender('select');
        $this->prepareFieldRender('text');

        $fields = [
            new InputType('name', 'text', $this->form),
            new InputType('email', 'email', $this->form),
        ];

        $this->prepareRender($options, false, true, true, $fields);

        $this->form->setFormOptions($options);

        $this->form
            ->add('gender', 'select')
            ->add('name', 'text')
            ->add('email', 'email');

        $this->form->gender->render();

        $this->form->renderRest();
    }

    /** @test */
    public function it_can_add_child_form_as_field()
    {
        $form = (new Form())->setFormHelper($this->formHelper);
        $customForm = (new CustomDummyForm())->setFormHelper($this->formHelper);

        $form
            ->add('title', 'text')
            ->add('song', 'form', [
                'class' => $customForm
            ]);

        $this->prepareFieldRender('title');
        $this->prepareFieldRender('child_form');
        $this->prepareRender(Mockery::any(), true, true, true, Mockery::any());

        $this->assertEquals($form, $form->title->getParent());

        $form->renderForm();

        $this->assertTrue($customForm->isChildForm());

        $this->assertEquals('song[title]', $form->song->getChildren()['title']->getName());
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

        $this->view->shouldReceive('make')->with('laravel-form-builder::form')
                   ->andReturn($viewRenderer);


        $viewRenderer->shouldReceive('with')->with(
            compact('showStart', 'showFields', 'showEnd')
        )->andReturnSelf();

        $viewRenderer->shouldReceive('with')->with(
            'formOptions',
            $formOptions
        )->andReturnSelf();

        $viewRenderer->shouldReceive('with')->with(
            'showFieldErrors',
            true
        )->andReturnSelf();

        $viewRenderer->shouldReceive('with')->with('fields', $fields)
                     ->andReturnSelf();

        $viewRenderer->shouldReceive('with')->with('model', null)
                     ->andReturnSelf();

        $viewRenderer->shouldReceive('render');
    }

    private function prepareFieldRender($view)
    {
            $viewRenderer = Mockery::mock('Illuminate\Contracts\View\View');
            $viewRenderer->shouldReceive('with')->andReturnSelf();
            $viewRenderer->shouldReceive('render');

            $this->view->shouldReceive('make')
                   ->with('laravel-form-builder::' . $view, Mockery::any())
                   ->andReturn($viewRenderer);
    }
}
