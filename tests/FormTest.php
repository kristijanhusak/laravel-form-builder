<?php

use Illuminate\Http\Exceptions\HttpResponseException;
use Kris\LaravelFormBuilder\Events\AfterFormValidation;
use Kris\LaravelFormBuilder\Events\BeforeFormValidation;
use Kris\LaravelFormBuilder\Fields\InputType;
use Kris\LaravelFormBuilder\Form;
use Kris\LaravelFormBuilder\FormHelper;
use Kris\LaravelFormBuilder\FormBuilder;

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
    public function it_passes_validation()
    {
        $this->plainForm
            ->add('name', 'text', [
                'rules' => 'required|min:5'
            ])
            ->add('description', 'textarea', [
                'rules' => 'max:10'
            ]);

        $this->request['name'] = 'some name';
        $this->request['description'] = 'somedesc';

        $isValid = $this->plainForm->isValid();

        $this->assertTrue($isValid);

        $this->assertEquals(
            ['name' => ['required', 'min:5'], 'description' => ['max:10']],
            $this->plainForm->getRules()
        );
    }

    /** @test */
    public function it_fails_validation()
    {
        $this->plainForm
            ->add('name', 'text', [
                'rules' => 'required|min:5'
            ])
            ->add('description', 'textarea', [
                'rules' => 'max:10'
            ]);

        $this->request['description'] = 'some long description';
        $isValid = $this->plainForm->isValid();

        $this->assertFalse($isValid);

        $errors = [
            'name' => ['The Name field is required.'],
            'description' => ['The Description must not be greater than 10 characters.']
        ];

        $this->assertEquals($errors, $this->plainForm->getErrors());
    }

    /** @test */
    public function it_alters_validity_and_adds_messages()
    {
        $customForm = $this->formBuilder->create('CustomNesterDummyForm');

        $this->request['subcustom'] = ['title' => "don't fail on this"];

        $isValid = $customForm->isValid();
        $this->assertTrue($isValid);

        $this->request['subcustom'] = ['title' => 'fail on this'];

        $isValid = $customForm->isValid();
        $this->assertFalse($isValid);

        $errors = $customForm->getErrors();
        $this->assertEquals(
            ['subcustom.title' => ['Error on title!']],
            $errors
        );
    }

    /** @test */
    public function it_can_automatically_redirect_back_when_failing_verification()
    {
        $this->plainForm
            ->add('name', 'text', [
                'rules' => 'required|min:5'
            ])
            ->add('description', 'textarea', [
                'rules' => 'max:10'
            ]);

        $this->request['description'] = 'some long description';

        try {
            $this->plainForm->redirectIfNotValid();
            $this->fail('Expected an HttpResponseException, but was allowed to continue');
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
            $this->assertNotNull($response);

            // It should be a redirect
            $this->assertEquals(302, $response->status());

            // It should go "back" to the root, which is the fallback when no referer is given
            $this->assertEquals('http://localhost', $response->getTargetUrl());

            // It should contain the old input
            $this->assertEquals('some long description', $response->getSession()->getOldInput('description'));

            // It should contain an error
            $this->assertNotEmpty($response->getSession()->get('errors'));
            $errorBag = $response->getSession()->get('errors');
            $this->assertTrue($errorBag->has('description'));
            $this->assertTrue($errorBag->has('name'));
            $this->assertEquals('The Description must not be greater than 10 characters.', $errorBag->first('description'));
        }
    }

    /** @test */
    public function it_can_automatically_redirect_to_a_specified_destination_when_failing_verification()
    {
        $this->plainForm
            ->add('name', 'text', [
                'rules' => 'required|min:5'
            ])
            ->add('description', 'textarea', [
                'rules' => 'max:10'
            ]);

        $this->request['description'] = 'some long description';

        try {
            $this->plainForm->redirectIfNotValid('my-custom-destination');
            $this->fail('Expected an HttpResponseException, but was allowed to continue');
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
            $this->assertNotNull($response);

            // It should be a redirect
            $this->assertEquals(302, $response->status());

            // It should go to 'my-custom-destination'
            $this->assertEquals('http://localhost/my-custom-destination', $response->getTargetUrl());

            // It should contain the old input
            $this->assertEquals('some long description', $response->getSession()->getOldInput('description'));

            // It should contain an error
            $this->assertNotEmpty($response->getSession()->get('errors'));
            $errorBag = $response->getSession()->get('errors');
            $this->assertTrue($errorBag->has('description'));
            $this->assertTrue($errorBag->has('name'));
            $this->assertEquals('The Description must not be greater than 10 characters.', $errorBag->first('description'));
        }
    }

    /** @test */
    public function it_overrides_default_rules_and_messages()
    {
        $this->plainForm
            ->add('name', 'text', [
                'rules' => 'required|min:5'
            ])
            ->add('age', 'text', [
                'rules' => 'required',
                'error_messages' => [
                    'age.required' => 'The age field is a must.'
                ]
            ])
            ->add('email', 'email', [
                'rules' => 'required|email',
                'error_messages' => [
                    'email.email' => 'The email is needed and this will not be shown.'
                ]
            ])
            ->add('description', 'textarea', [
                'rules' => 'max:10'
            ]);

        $this->request['name'] = 'name';
        $this->request['description'] = 'some long description';
        $this->request['email'] = 'invalidemail';

        $validate = $this->plainForm->validate(['name' => 'numeric'], [
            'name.numeric' => 'Name field must be numeric.',
            'email.email' => 'The email is very required.'
        ]);

        $isValid = $this->plainForm->isValid();

        $this->assertFalse($isValid);

        $errors = [
            'name' => ['Name field must be numeric.'],
            'description' => ['The Description must not be greater than 10 characters.'],
            'age' => ['The age field is a must.'],
            'email' => ['The email is very required.']
        ];

        $this->assertEquals($errors, $this->plainForm->getErrors());
    }

    /** @test */
    public function it_uses_error_messages_from_fields()
    {
        $childForm = $this->formBuilder->plain();
        $childForm->add('street', 'text', [
            'rules' => 'required|min:5',
            'error_messages' => [
                'street.min' => 'Street needs to have 5 letters.'
            ]
        ]);

        $this->plainForm
            ->add('name', 'text', [
                'rules' => 'required|min:5',
                'error_messages' => [
                    'name.required' => 'Please provide your name.'
                ]
            ])
            ->add('address', 'form', [
                'class' => $childForm
            ]);

        $this->request['address'] = ['street' => 'ab'];

        $this->assertFalse($this->plainForm->isValid());

        $errors = [
            'name' => ['Please provide your name.'],
            'address.street' => ['Street needs to have 5 letters.']
        ];

        $this->assertEquals($errors, $this->plainForm->getErrors());
    }

    /**
     * @test
     * */
    public function it_throws_exception_when_errors_requested_from_non_validated_form()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->plainForm
            ->add('name', 'text', [
                'rules' => 'required|min:5'
            ])
            ->add('description', 'textarea', [
                'rules' => 'max:10'
            ]);

        $this->request['description'] = 'some long description';
        $this->plainForm->getErrors();
    }

    /** @test */
    public function it_returns_field_values()
    {
        $this->plainForm
            ->add('name', 'text', [
                'rules' => ['required', 'min:20', 'max:255'],
            ])
            ->add('description[text]', 'textarea')
            ->add('address', 'form', [
                'class' => $this->formBuilder->plain()
                    ->add('street[name]', 'text', ['rules' => 'required']),
            ])
            ->add('user[address]', 'form', [
                'class' => $this->formBuilder->plain()
                    ->add('street', 'text', ['rules' => 'required'])
                    ->add('number', 'number'),
            ]);

        // Should return all fields, including nested, no matter validation rules
        $this->assertEquals(
            ['name', 'description.text', 'address.street.name', 'user.address.street', 'user.address.number'],
            $this->plainForm->getAllAttributes()
        );

        $this->request['status'] = 1;
        $this->request['role'] = 'admin';
        $this->request['name'] = 'Foo';
        $this->request['description'] = ['text' => 'Foo Bar'];
        $this->request['address'] = ['street' => ['id' => 1000, 'name' => 'Street 1']];
        $this->request['user'] = ['id' => 1000, 'address' => ['street' => 'Street 2']]; // Missing optional 'number'

        $check_values = [
            'name' => 'Foo',
            'description' => ['text' => 'Foo Bar'],
            'address' => ['street' => ['name' => 'Street 1']],
            'user' => ['address' => ['street' => 'Street 2']],
        ];

        // Ignore unknown data, skip missing input
        $this->assertEquals(
            $check_values,
            $this->plainForm->getFieldValues(false)
        );

        // Ignore unknown data, add NIL for missing input
        $check_values['user']['address']['number'] = null;
        $this->assertEquals(
            $check_values,
            $this->plainForm->getFieldValues()
        );
    }

    /** @test */
    public function it_returns_altered_field_values()
    {
        $this->request['name'] = 'lower case';
        $this->request['subcustom'] = ['title' => 'Bar foo', 'body' => 'Foo bar'];
        $this->request['subcustom_collection'] = [
            5 => ['title' => 'Item 1 title', 'body' => 'Item 1 body'],
            17 => ['title' => 'Item 2 title', 'body' => 'Item 2 body'],
        ];

        $customForm = $this->formBuilder->create('CustomNesterDummyForm');

        $this->assertEquals(
            [
                'name' => 'LOWER CASE',
                'options' => ['x'],
                'subcustom' => ['title' => 'Bar foo', 'body' => 'FOO BAR'],
                'subcustom_collection' => [
                    5 => ['title' => 'Item 1 title', 'body' => 'ITEM 1 BODY'],
                    17 => ['title' => 'Item 2 title', 'body' => 'ITEM 2 BODY'],
                ],
            ],
            $customForm->getFieldValues()
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

        $this->plainForm->remove('name', 'description');

        $this->assertEquals(1, count($this->plainForm->getFields()));

        $this->assertFalse($this->plainForm->has('name'));
        $this->assertFalse($this->plainForm->has('description'));
    }

    /** @test */
    public function it_can_take_and_replace_existing_fields()
    {
        $this->plainForm
            ->add('name', 'text')
            ->add('description', 'textarea')
            ->add('remember', 'checkbox');

        $this->plainForm->only('remember', 'name');

        $this->assertEquals(2, count($this->plainForm->getFields()));

        $this->assertTrue($this->plainForm->has('remember'));
        $this->assertTrue($this->plainForm->has('name'));
        $this->assertFalse($this->plainForm->has('description'));
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
     */
     public function it_throws_exception_when_rendering_until_nonexisting_field()
     {
         $this->expectException(\InvalidArgumentException::class);

         $this->plainForm
            ->add('gender', 'select')
            ->add('name', 'text');

        $this->plainForm->gender->render();
        $this->plainForm->renderUntil('nonexisting');
     }

    /**
     * @test
     */
    public function it_prevents_adding_fields_with_same_name()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->plainForm->add('name', 'text')->add('name', 'textarea');
    }

    /**
     * @test
     */
    public function it_throws_exception_if_field_name_is_reserved()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->plainForm->add('save', 'submit');
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
            $this->assertTrue($exceptionThrown);
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
            'attr' => ['class' => 'form-container'],
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
        $this->plainForm->setName('test_name');

        $this->assertEquals(
            ['method' => 'DELETE', 'url' => '/posts/all', 'attr' => []],
            $this->plainForm->getFormOptions()
        );

        $this->assertEquals(
            $this->model,
            $this->plainForm->getModel()
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
        $formOptions = [
            'method' => 'POST',
            'url' => '/someurl',
            'class' => 'has-error',
            'array_option' => ['foo' => 'bar'],
        ];

        $this->plainForm->renderForm($formOptions, true, true, true);

        $this->assertNotThrown();
    }

    /** @test */
    public function it_renders_rest_of_the_form()
    {
        $options = [
            'method' => 'GET',
            'url' => '/some/url/10'
        ];

        $fields = [
            new InputType('name', 'text', $this->plainForm),
            new InputType('email', 'email', $this->plainForm),
        ];

        $this->plainForm->setFormOptions($options);

        $this->plainForm
            ->add('gender', 'select')
            ->add('name', 'text')
            ->add('email', 'email');

        $this->plainForm->gender->render();

        $this->plainForm->renderRest();

        $this->assertNotThrown();
    }

    /** @test */
    public function it_renders_rest_of_the_form_until_specified_field()
    {
        $options = [
            'method' => 'GET',
            'url' => '/some/url/10'
        ];

        $fields = [
            new InputType('name', 'text', $this->plainForm),
            new InputType('email', 'email', $this->plainForm),
        ];

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
        $model = ['song' => ['body' => 'test body'], 'title' => 'main title'];
        $form = $this->formBuilder->plain([
            'model' => $model,
        ]);
        $customForm = $this->formBuilder->create('CustomDummyForm');
        $customForm->add('img', 'file')->add('name', 'text', ['label_show' => false]);

        $form
            ->add('title', 'text', [
                'label_attr' => [
                    'for' => 'custom_title'
                ],
                'attr' => [
                    'id' => 'custom_title'
                ]
            ])
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

        $this->assertEquals($form, $form->title->getParent());

        $view = $form->renderForm();

        $this->assertEquals('songs[1]', $customForm->getName());

        $this->assertEquals('song[title]', $form->song->getChild('title')->getName());
        $this->assertEquals('custom_title', $form->title->getOption('attr.id'));
        $this->assertEquals('custom_title', $form->title->getOption('label_attr.for'));
        $this->assertFalse($form->song->name->getOption('label_show'));
        $this->assertCount(2, $form->songs->getChildren());
        $this->assertEquals('lorem', $form->songs->getChild(0)->title->getOption('value'));
        $this->assertEquals('test body', $form->song->body->getOption('value'));
        $this->assertEquals('main title', $form->title->getOption('value'));
        $this->assertInstanceOf(
            'Kris\LaravelFormBuilder\Form',
            $form->song->getForm()
        );

        $this->assertDoesNotMatchRegularExpression('/label.*for="name"/', $view);
        $this->assertMatchesRegularExpression('/label.*for="custom_title"/', $view);
        $this->assertMatchesRegularExpression('/input.*id="custom_title"/', $view);

        $this->assertTrue($form->song->getFormOption('files'));

        try {
            $form->song->badMethod();
        } catch (\BadMethodCallException $e) {
            return;
        }
        $this->fail('No exception on bad method call on child form.');
    }

    /** @test */
    public function it_can_use_model_property_to_set_value()
    {
        $form = $this->formBuilder->plain([
            'model' => $this->model,
        ]);

        $form->add('alias_accessor', 'choice', [
            'value_property' => 'accessor',
        ]);

        $this->assertEquals($form->alias_accessor->getValue(), $this->model->accessor);
    }

    /** @test */
    public function it_sets_entity_field_value_to_the_entity_model_value()
    {
        $dummyModel = new DummyModel();
        $dummyModel->id = 1;

        $this->model->dummy_model_id = $dummyModel->id;

        $form = $this->formBuilder
            ->plain([
                'model' => $this->model,
            ])
            ->add('dummy_model_id', 'entity', [
                'class' => DummyModel::class,
                'property' => 'name',
            ]);

        $this->assertEquals($form->dummy_model_id->getValue(), $this->model->dummy_model_id);
    }

    /** @test */
    public function it_reads_configuration_properly()
    {
        $config = $this->config;
        $config['defaults']['textarea'] = ['field_class' => 'my-textarea-class'];
        $formHelper = new FormHelper($this->view, $this->translator, $config);
        $formBuilder = new FormBuilder($this->app, $formHelper, $this->eventDispatcher);

        $form = $formBuilder->plain()
            ->add('name', 'text')
            ->add('desc', 'textarea');

        $overridenClassForm = $formBuilder->plain()
            ->add('name', 'text', ['attr' => ['class' => 'my-text-class']])
            ->add('desc', 'textarea', ['attr' => ['class' => 'overwrite-textarea-class']]);

        $formView = $form->renderForm();
        $overridenView = $overridenClassForm->renderForm();

        $this->assertMatchesRegularExpression('/textarea.*class="my-textarea-class"/', $formView);
        $this->assertMatchesRegularExpression('/input.*class="form-control"/', $formView);

        $this->assertMatchesRegularExpression('/textarea.*class="overwrite-textarea-class"/', $overridenView);
        $this->assertMatchesRegularExpression('/input.*class="my-text-class"/', $overridenView);
    }

    /** @test */
    public function it_works_when_setModel_method_is_called()
    {
        $customForm = $this->formBuilder->create('CustomDummyForm')->setModel([
            'title' => 'john doe'
        ]);

        $customForm->renderForm();

        $this->assertEquals('john doe', $customForm->title->getValue());
    }

    /** @test */
    public function it_removes_children_from_parent_type_fields()
    {
        $model = ['song' => ['title' => 'test song title', 'body' => 'test body'], 'title' => 'main title'];
        $form = $this->formBuilder->plain([
            'model' => $model,
        ]);
        $customForm = $this->formBuilder->create('CustomDummyForm');

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

        $this->assertCount(2, $form->song->getChildren());
        $this->assertCount(2, $form->song->getForm()->getFields());
        $form->song->removeChild('title');
        $this->assertCount(1, $form->song->getChildren());
        $this->assertCount(1, $form->song->getForm()->getFields());
        $this->assertEquals('test body', $form->song->body->getValue());
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
        $this->plainForm->renderForm();

        $this->assertEquals('test_name[name]', $this->plainForm->getField('name')->getName());
        $this->assertEquals('test_name[address]', $this->plainForm->getField('address')->getName());
        $this->assertEquals($expectModel, $this->plainForm->getModel());
    }

    /** @test */
    public function it_has_html_valid_element_names()
    {
        $this->plainForm
            ->add('name[text]', 'text')
            ->add('child[form]', 'form', [
                'class' => $this->formBuilder->plain()->add('name[text]', 'text'),
            ]);

        $this->assertEquals('name[text]', $this->plainForm->getField('name[text]')->getName());
        $this->assertEquals('child[form][name][text]', $this->plainForm->getField('child[form]')->getField('name[text]')->getName());
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
     */
    public function it_throws_exception_when_adding_field_with_invalid_name()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->plainForm->add('', 'text');
    }

    /**
     * @test
     */
    public function it_throws_exception_when_adding_field_with_invalid_type()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->plainForm->add('name', '');
    }

    /**
     * @test
     */
    public function it_prevents_adding_duplicate_custom_type()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->plainForm->addCustomField('datetime', 'Some\\Namespace\\DatetimeType');

        $this->plainForm->addCustomField('datetime', 'Some\\Namespace\\DateType');
    }


    /** @test */
    public function it_can_compose_another_forms_fields_into_itself()
    {
        $form = $this->formBuilder->plain();
        $customForm = $this->formBuilder->create('CustomDummyForm');

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
        $form = $this->formBuilder->plain();

        $form->add('name', 'text')
            ->add('email', 'email')
            ->add('dummy', 'form', [
                'class' => 'CustomDummyForm'
            ]);

        $this->assertNull($form->name->getOption('attr.disabled'));

        $form->disableFields();

        $this->assertEquals('disabled', $form->name->getOption('attr.disabled'));
    }

    /** @test */
    public function it_enables_all_fields_in_form()
    {
        $form = $this->formBuilder->plain();

        $form
            ->add('name', 'text', [
                'attr' => ['disabled' => 'disabled']
            ])
            ->add('email', 'email')
            ->add('dummy', 'form', [
                'class' => 'CustomDummyForm'
            ]);

        $this->assertEquals('disabled', $form->name->getOption('attr.disabled'));

        $form->enableFields();

        $this->assertNull($form->name->getOption('attr.disabled'));
    }

    /** @test */
    public function it_allows_disabling_showing_form_errors()
    {
        $this->plainForm->add('child_form', 'form', ['class'=> 'CustomDummyForm']);
        $this->assertTrue($this->plainForm->haveErrorsEnabled());
        $this->assertTrue($this->plainForm->getField('child_form')->haveErrorsEnabled());
        $this->plainForm->setErrorsEnabled(false);
        $this->plainForm->rebuildForm();
        $this->assertFalse($this->plainForm->haveErrorsEnabled());
        $this->assertFalse($this->plainForm->getField('child_form')->haveErrorsEnabled());
    }

    /** @test */
    public function it_allows_disabling_client_validation()
    {
        $this->plainForm->add('child_form', 'form', ['class'=> 'CustomDummyForm']);
        $this->assertTrue($this->plainForm->clientValidationEnabled());
        $this->assertTrue($this->plainForm->getField('child_form')->clientValidationEnabled());

        $this->plainForm->setClientValidationEnabled(false);
        $this->plainForm->rebuildForm();

        $this->assertFalse($this->plainForm->clientValidationEnabled());
        $this->assertFalse($this->plainForm->getField('child_form')->clientValidationEnabled());
    }

    /** @test */
    public function it_receives_validation_events()
    {
        $events = [];

        $this->eventDispatcher->listen(BeforeFormValidation::class, function($event) use (&$events) {
            $events[] = get_class($event);
        });

        $this->eventDispatcher->listen(AfterFormValidation::class, function($event) use (&$events) {
            $events[] = get_class($event);
        });

        $this->plainForm->add('name', 'text', ['rules' => ['required', 'min:3']]);

        $this->request['name'] = 'Foo Bar';

        $this->plainForm->isValid();

        $this->assertEquals(
            [
                'Kris\LaravelFormBuilder\Events\BeforeFormValidation',
                'Kris\LaravelFormBuilder\Events\AfterFormValidation',
            ],
            $events
        );
    }

    /** @test */
    public function it_has_a_template_prefix()
    {
        $form = $this->formBuilder->plain();
        $form->setFormOptions(['template_prefix' => 'test::']);
        $form->add('name', 'text');

        $this->assertEquals('test::', $form->getTemplatePrefix());
        $this->assertNull($form->getFormOption('template_prefix'));
    }

    /** @test */
    public function it_stores_a_template_prefix()
    {
        $form = $this->formBuilder->plain();
        $form->setTemplatePrefix('test_prefix::');

        $this->assertEquals('test_prefix::', $form->getTemplatePrefix());
    }

    /** @test */
    public function it_uses_the_template_prefix()
    {
        $viewStub = $this->getMockBuilder('Illuminate\View\Factory')->setMethods(['make', 'with', 'render'])->disableOriginalConstructor()->getMock();
        $viewStub->method('make')->willReturn($viewStub);
        $viewStub->method('with')->willReturn($viewStub);

        $helper = new FormHelper($viewStub, $this->translator, $this->config);

        $form = $this->formBuilder->plain();
        $form->setFormOptions([
            'template_prefix' => 'test::',
            'template' => 'a_template'
        ]);

        // Check that the form uses the correct template
        $viewStub->expects($this->atLeastOnce())
                 ->method('make')
                 ->with($this->equalTo('test::a_template'));

        $form->setFormHelper($helper);
        $form->renderForm();
    }

    /** @test */
    public function it_locks_filtering()
    {
        $customPlainForm = $this->formBuilder->plain();
        $customPlainForm->lockFiltering();

        $this->assertTrue(
            $customPlainForm->isFilteringLocked()
        );
    }

    /** @test */
    public function it_returns_binded_field_filters()
    {
        $customPlainForm = $this->formBuilder->plain();
        $customPlainForm
            ->add('test_field', 'text', [
                'filters' => ['Trim', 'Uppercase']
            ])
            ->add('test_field2', 'text', [
                'filters' => ['Uppercase']
            ])
        ;

        $expected = [
            'test_field' => [
                'Trim'    => new \Kris\LaravelFormBuilder\Filters\Collection\Trim(),
                'Uppercase' => new \Kris\LaravelFormBuilder\Filters\Collection\Uppercase()
            ],
            'test_field2' => [
                'Uppercase' => new \Kris\LaravelFormBuilder\Filters\Collection\Uppercase()
            ]
        ];

        $bindedFields = $customPlainForm->getFilters();

        $this->assertEquals(
            $expected, $bindedFields
        );
    }

    /** @test */
    public function it_filter_and_mutate_fields_request_values()
    {
        $toMutateValue = ' test ';
        $this->request['test_field'] = $toMutateValue;

        $customPlainForm = $this->formBuilder->plain();
        $customPlainForm->add('test_field', 'text', [
            'filters' => ['Trim', 'Uppercase']
        ]);
        $customPlainForm->filterFields();

        $this->assertEquals('TEST', $this->request['test_field']);
    }

    /** @test */
    public function it_add_option_attributes_properly()
    {
        $config = $this->config;

        $formHelper = new FormHelper($this->view, $this->translator, $config);
        $formBuilder = new FormBuilder($this->app, $formHelper, $this->eventDispatcher);

        $choices = ['en' => 'English', 'fr' => 'French', 'zh' => 'Chinese'];
        $optionAttributes = ['zh' => ['disabled' => 'disabled']];

        $form = $formBuilder->plain()
            ->add('languages_select', 'select', [
                'choices' => $choices,
                'option_attributes' => $optionAttributes
            ])
            ->add('languages_choice_select', 'choice', [
                'choices' => $choices,
                'option_attributes' => $optionAttributes,
                'expanded' => false,
                'multiple' => false
            ])
            ->add('languages_choice_select_multiple', 'choice', [
                'choices' => $choices,
                'option_attributes' => $optionAttributes,
                'expanded' => false,
                'multiple' => true
            ])
            ->add('languages_choice_checkbox', 'choice', [
                'choices' => $choices,
                'option_attributes' => $optionAttributes,
                'expanded' => true,
                'multiple' => true
            ])
            ->add('languages_choice_radio', 'choice', [
                'choices' => $choices,
                'option_attributes' => $optionAttributes,
                'expanded' => true,
                'multiple' => false
            ]);

        $formView = $form->renderForm();

        $this->assertStringContainsString('<option value="zh" disabled="disabled">', $formView);
        $this->assertStringNotContainsString('<option value="en" disabled="disabled">', $formView);
        $this->assertStringNotContainsString('<option value="fr" disabled="disabled">', $formView);
        $this->assertStringContainsString('<input id="languages_choice_checkbox_zh" disabled="disabled" name="languages_choice_checkbox[]" type="checkbox" value="zh">', $formView);
        $this->assertStringNotContainsString('<input id="languages_choice_checkbox_en" disabled="disabled" name="languages_choice_checkbox[]" type="checkbox" value="en">', $formView);
        $this->assertStringContainsString('<input id="languages_choice_radio_zh" disabled="disabled" name="languages_choice_radio" type="radio" value="zh">', $formView);
        $this->assertStringNotContainsString('<input id="languages_choice_radio_en" disabled="disabled" name="languages_choice_radio" type="radio" value="en">', $formView);
    }
}
