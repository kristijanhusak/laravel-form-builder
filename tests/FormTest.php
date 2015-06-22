<?php

use Kris\LaravelFormBuilder\Fields\InputType;
use Kris\LaravelFormBuilder\Form;

class FormTest extends FormBuilderTestCase
{

    /** @test */
    public function it_adds_fields()
    {
        $this->plainForm
            ->add('name', 'text')
            ->add('description', 'textarea')
            ->add('address', 'static')
            ->add('remember', 'checkbox');

        $this->assertEquals(4, count($this->plainForm->getFields()));

        $this->assertTrue($this->plainForm->has('name'));
        $this->assertFalse($this->plainForm->has('body'));

        // Accessed with magic methods
        $this->assertEquals($this->plainForm->name, $this->plainForm->getField('name'));

        $this->assertInstanceOf(
            'Kris\LaravelFormBuilder\Fields\InputType',
            $this->plainForm->getField('name')
        );

        $this->assertInstanceOf(
            'Kris\LaravelFormBuilder\Fields\TextareaType',
            $this->plainForm->getField('description')
        );

        $this->assertInstanceOf(
            'Kris\LaravelFormBuilder\Fields\CheckableType',
            $this->plainForm->getField('remember')
        );

        $this->assertInstanceOf(
            'Kris\LaravelFormBuilder\Fields\StaticType',
            $this->plainForm->getField('address')
        );
    }

    /** @test */
    public function it_adds_after_some_field()
    {
        $this->plainForm
            ->add('name', 'text')
            ->add('description', 'textarea');

        $descIndexBefore = array_search(
            'description',
            array_keys($this->plainForm->getFields())
        );

        $this->assertEquals(1, $descIndexBefore);
        $this->assertNull($this->plainForm->address);

        $this->plainForm->addAfter('name', 'address');

        $descIndexAfter = array_search(
            'description',
            array_keys($this->plainForm->getFields())
        );

        $addressIndex = array_search(
            'address',
            array_keys($this->plainForm->getFields())
        );

        $this->assertEquals(2, $descIndexAfter);
        $this->assertEquals(1, $addressIndex);

        $this->assertInstanceOf(
            'Kris\LaravelFormBuilder\Fields\InputType',
            $this->plainForm->address
        );
    }

    /** @test */
    public function it_adds_before_some_field()
    {
        $this->plainForm
            ->add('name', 'text')
            ->add('description', 'textarea');

        $descIndexBefore = array_search(
            'description',
            array_keys($this->plainForm->getFields())
        );

        $this->assertEquals(1, $descIndexBefore);
        $this->assertNull($this->plainForm->address);

        $this->plainForm->addBefore('name', 'address');

        $descIndexAfter = array_search(
            'description',
            array_keys($this->plainForm->getFields())
        );

        $addressIndex = array_search(
            'address',
            array_keys($this->plainForm->getFields())
        );

        $this->assertEquals(2, $descIndexAfter);
        $this->assertEquals(0, $addressIndex);

        $this->assertInstanceOf(
            'Kris\LaravelFormBuilder\Fields\InputType',
            $this->plainForm->address
        );
    }


    /** @test */
    public function it_can_remove_existing_fields_from_form_object()
    {
        $this->plainForm
            ->add('name', 'text')
            ->add('description', 'textarea')
            ->add('remember', 'checkbox');

        $this->assertEquals(3, count($this->plainForm->getFields()));

        $this->assertTrue($this->plainForm->has('name'));

        $this->plainForm->remove('name');

        $this->assertEquals(2, count($this->plainForm->getFields()));

        $this->assertFalse($this->plainForm->has('name'));
    }

    /** @test */
    public function it_can_modify_existing_fields()
    {
        $this->plainForm
            ->add('name', 'text')
            ->add('description', 'textarea', [
                'attr' => ['placeholder' => 'Enter text here...']
            ])
            ->add('category', 'select', [
                'choices' => [ 1 => 'category-1', 2 => 'category-2']
            ]);
        // Adds new if provided name doesn't exist
        $this->plainForm->modify('remember', 'checkbox');

        // Modifies without complete ovewrite of options

        $this->assertEquals('textarea', $this->plainForm->description->getType());
        $this->assertEquals(
            ['placeholder' => 'Enter text here...', 'class' => 'form-control'],
            $this->plainForm->description->getOption('attr')
        );

        $this->plainForm->modify('description', 'text', [
            'attr' => ['class' => 'modified-input']
        ]);

        $this->assertEquals('text', $this->plainForm->description->getType());
        $this->assertEquals(
            ['placeholder' => 'Enter text here...', 'class' => 'modified-input'],
            $this->plainForm->description->getOption('attr')
        );

        // Check if complete option ovewrite work
        $this->assertEquals(
            [ 1 => 'category-1', 2 => 'category-2'],
            $this->plainForm->category->getOption('choices')
        );

        $this->assertArrayNotHasKey('expanded', $this->plainForm->category->getOptions());

        $this->plainForm->modify('category', 'choice', [
            'expanded' => true
        ], true);

        $this->assertNotEquals(
            [ 1 => 'category-1', 2 => 'category-2'],
            $this->plainForm->category->getOption('choices')
        );

        $this->assertTrue($this->plainForm->category->getOption('expanded'));

    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_exception_when_removing_nonexisting_field()
    {
        $this->plainForm->add('name', 'text');

        $this->plainForm->remove('nonexisting');
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_prevents_adding_fields_with_same_name()
    {
        $this->plainForm->add('name', 'text')->add('name', 'textarea');
    }

    /** @test */
    public function it_throws_InvalidArgumentException_on_non_existing_property()
    {
        $exceptionThrown = false;

        $this->plainForm
            ->add('name', 'text')
            ->add('description', 'textarea');

        try {
            $this->plainForm->nonexisting;
        } catch (\InvalidArgumentException $e) {
            $exceptionThrown = true;
        }

        try {
            $this->plainForm->getField('nonexisting');
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

        $this->plainForm->setFormOptions($options);

        // After the setup model is removed from options
        unset($options['model']);

        $this->assertEquals($options, $this->plainForm->getFormOptions());

        $this->assertEquals('POST', $this->plainForm->getMethod());
        $this->assertEquals('/url/1', $this->plainForm->getUrl());
        $this->assertInstanceOf(
            'Illuminate\Database\Eloquent\Model',
            $this->plainForm->getModel()
        );
    }

    /** @test */
    public function it_can_set_form_options_with_setters()
    {
        $this->plainForm->setMethod('DELETE');
        $this->plainForm->setUrl('/posts/all');
        $this->plainForm->setModel($this->model);
        $this->plainForm->setData('some_data', ['this', 'is', 'some', 'data']);
        $this->plainForm->setName('test_name');

        $this->assertEquals(
            ['method' => 'DELETE', 'url' => '/posts/all'],
            $this->plainForm->getFormOptions()
        );

        $this->assertEquals(
            $this->model,
            $this->plainForm->getModel()
        );

         $this->assertEquals(
             ['this', 'is', 'some', 'data'],
             $this->plainForm->getData('some_data')
         );

        $this->assertEquals(
            $this->plainForm->getData(),
            ['some_data' => ['this', 'is', 'some', 'data']]
        );

        $this->assertEquals('test_name', $this->plainForm->getName());
    }

    /** @test */
    public function it_sets_file_option_to_true_if_file_type_added()
    {
        $this->plainForm->add('upload_file', 'file');

        $this->assertTrue($this->plainForm->getFormOption('files'));
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

        $this->plainForm->renderForm($options, true, true, true);
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
            new InputType('name', 'text', $this->plainForm),
            new InputType('email', 'email', $this->plainForm),
        ];

        $this->prepareRender($options, false, true, true, $fields);

        $this->plainForm->setFormOptions($options);

        $this->plainForm
            ->add('gender', 'select')
            ->add('name', 'text')
            ->add('email', 'email');

        $this->plainForm->gender->render();

        $this->plainForm->renderRest();
    }

    /** @test */
    public function it_renders_rest_of_the_form_until_specified_field()
    {
        $options = [
            'method' => 'GET',
            'url' => '/some/url/10'
        ];

        $this->prepareFieldRender('select');
        $this->prepareFieldRender('text');

        $fields = [
            new InputType('name', 'text', $this->plainForm),
            new InputType('email', 'email', $this->plainForm),
        ];

        $this->prepareRender($options, false, true, true, $fields);

        $this->plainForm->setFormOptions($options);

        $this->plainForm
            ->add('gender', 'select')
            ->add('name', 'text')
            ->add('email', 'email')
            ->add('address', 'text');

        $this->plainForm->gender->render();

        $this->plainForm->renderUntil('email');
        $this->assertEquals($this->plainForm->address->isRendered(), false);
    }

    /** @test */
    public function it_can_add_child_form_as_field()
    {
        $form = $this->setupForm(new Form());
        $customForm = $this->setupForm(new CustomDummyForm());
        $customForm->add('img', 'file');
        $this->request->shouldReceive('old');
        $model = ['song' => ['body' => 'test body'], 'title' => 'main title'];
        $form->setModel($model);

        $form
            ->add('title', 'text')
            ->add('song', 'form', [
                'class' => $customForm
            ])
            ->add('songs', 'collection', [
                'type' => 'form',
                'data' => [
                    ['title' => 'lorem', 'body' => 'ipsum'],
                    new \Illuminate\Support\Collection(['title' => 'dolor', 'body' => 'sit'])
                ],
                'options' => [
                    'class' => $customForm
                ]
            ])
        ;

        $this->prepareFieldRender('title');
        $this->prepareFieldRender('child_form');
        $this->prepareRender(Mockery::any(), true, true, true, Mockery::any(), $model);

        $this->assertEquals($form, $form->title->getParent());

        $form->renderForm();

        $this->assertEquals('songs[1]', $customForm->getName());

        $this->assertEquals('song[title]', $form->song->getChild('title')->getName());
        $this->assertCount(2, $form->songs->getChildren());
        $this->assertEquals('lorem', $form->songs->getChild(0)->title->getOption('value'));
        $this->assertEquals('test body', $form->song->body->getOption('value'));
        $this->assertEquals('main title', $form->title->getOption('value'));
        $this->assertInstanceOf(
            'Kris\LaravelFormBuilder\Form',
            $form->song->getForm()
        );

        $this->assertTrue($form->song->getFormOption('files'));

        try {
            $form->song->badMethod();
        } catch (\BadMethodCallException $e) {
            return;
        }
        $this->fail('No exception on bad method call on child form.');
    }

    /** @test */
    public function it_creates_named_form()
    {
        $model = new \Illuminate\Support\Collection([
            'name' => 'John Doe',
            'gender' => 'f'
        ]);

        $expectModel = [
            'test_name' => $model->all()
        ];
        $this->plainForm
            ->add('name', 'text')
            ->add('address', 'static');
        $this->assertEquals('name', $this->plainForm->getField('name')->getName());
        $this->assertEquals('address', $this->plainForm->getField('address')->getName());
        $this->plainForm->setName('test_name')->setModel($model);
        $this->prepareFieldRender('text');
        $this->prepareFieldRender('static');
        $this->prepareRender(Mockery::any(), true, true, true, Mockery::any(), $expectModel);
        $this->plainForm->renderForm();

        $this->assertEquals('test_name[name]', $this->plainForm->getField('name')->getName());
        $this->assertEquals('test_name[address]', $this->plainForm->getField('address')->getName());
        $this->assertEquals($expectModel, $this->plainForm->getModel());
    }

    /** @test */
    public function it_adds_custom_type()
    {
        $this->plainForm->addCustomField('datetime', 'Some\\Namespace\\DatetimeType');

        $fieldType = $this->formHelper->getFieldType('datetime');

        $this->assertEquals('Some\\Namespace\\DatetimeType', $fieldType);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_exception_when_adding_field_with_invalid_name()
    {
        $this->plainForm->add('', 'text');
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_exception_when_adding_field_with_invalid_type()
    {
        $this->plainForm->add('name', '');
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_prevents_adding_duplicate_custom_type()
    {
        $this->plainForm->addCustomField('datetime', 'Some\\Namespace\\DatetimeType');

        $this->plainForm->addCustomField('datetime', 'Some\\Namespace\\DateType');
    }


    /** @test */
    public function it_can_compose_another_forms_fields_into_itself()
    {
        $form = $this->setupForm(new Form());
        $customForm = $this->setupForm(new CustomDummyForm());


        $form
            ->add('name', 'text')
            ->compose($customForm)
        ;

        $this->assertEquals($form, $form->name->getParent());

        $this->assertEquals(3, count($form->getFields()));
        $this->assertEquals(true, $form->has('title'));
        $this->assertEquals('title', $form->title->getName());
        $this->assertEquals('title', $form->title->getRealName());
    }

    /** @test */
    public function it_disables_all_fields_in_form()
    {
        $form = $this->setupForm(new Form());

        $form->add('name', 'text')
            ->add('email', 'email');

        $this->assertNull($form->name->getOption('attr.disabled'));

        $form->disableFields();

        $this->assertEquals('disabled', $form->name->getOption('attr.disabled'));
    }

    /** @test */
    public function it_enables_all_fields_in_form()
    {
        $form = $this->setupForm(new Form());

        $form
            ->add('name', 'text', [
                'attr' => ['disabled' => 'disabled']
            ])
            ->add('email', 'email');

        $this->assertEquals('disabled', $form->name->getOption('attr.disabled'));

        $form->enableFields();

        $this->assertNull($form->name->getOption('attr.disabled'));

    }

    private function prepareRender(
        $formOptions = [],
        $showStart = true,
        $showFields = true,
        $showEnd = true,
        $fields = [],
        $model = null,
        $exclude = []
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

        $viewRenderer->shouldReceive('with')->with('model', $model)
                     ->andReturnSelf();

        $viewRenderer->shouldReceive('with')->with('exclude', $exclude)
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
