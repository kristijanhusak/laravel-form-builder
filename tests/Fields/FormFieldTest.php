<?php

use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormHelper;
use Kris\LaravelFormBuilder\Fields\InputType;

class FormFieldTest extends FormBuilderTestCase
{
    /** @test */
    public function it_use_set_rendered()
    {
        $field = new InputType('name', 'text', $this->plainForm);

        $this->assertFalse($field->isRendered());

        $field->setRendered();

        $this->assertTrue($field->isRendered());
    }

    /** @test */
    public function it_uses_the_template_prefix()
    {
        $viewStub = $this->getViewFactoryMock();

        $helper = new FormHelper($viewStub, $this->translator, $this->config);

        $form = $this->formBuilder->plain();
        $form->setTemplatePrefix('test::');

        // Check that the field uses the correct template
        $viewStub->expects($this->atLeastOnce())
                 ->method('make')
                 ->with($this->equalTo('test::textinput'));

        $viewStub->expects($this->atLeastOnce())
                 ->method('make')
                 ->with($this->equalTo('test::textinput'));

        $form->setFormHelper($helper);

        // Create a new field to render directly and test
        // with the view stub generated above
        $field = new InputType('name', 'text', $form, ['template' => 'textinput']);
        $field->render();
    }

    /** @test */
    public function it_hides_the_label_with_label_show_property()
    {
        $options = [
            'label' => 'Name',
            'label_show' => false
        ];
        $field = new InputType('name', 'text', $this->plainForm, $options);

        $view = $field->render();

        $this->assertFalse($field->getOption('label_show'));
        $this->assertStringNotContainsString('label', $view);
    }


    /** @test */
    public function it_sets_required_as_class_on_the_label_and_attribute_on_the_field_when_setting_required_explicitly()
    {
        $options = [
            'required' => true
        ];

        $hidden = new InputType('hidden_id', 'hidden', $this->plainForm, $options);
        $hidden->render();

        $this->assertMatchesRegularExpression('/required/', $hidden->getOption('label_attr.class'));
        $this->assertArrayHasKey('required', $hidden->getOption('attr'));
    }

    /** @test */
    public function it_sets_required_as_class_on_the_label_and_attribute_on_the_field_when_setting_required_via_a_rule()
    {
        $options = [
            'rules' => 'required|min:3'
        ];

        $hidden = new InputType('hidden_id', 'hidden', $this->plainForm, $options);
        $hidden->render();

        $this->assertMatchesRegularExpression('/required/', $hidden->getOption('label_attr.class'));
        $this->assertArrayHasKey('required', $hidden->getOption('attr'));
    }

    /** @test */
    public function it_adds_the_required_class_to_the_label_when_client_side_validation_is_disabled()
    {
        $options = [
            'rules' => 'required|min:3'
        ];

        $this->plainForm->setClientValidationEnabled(false);

        $hidden = new InputType('hidden_id', 'hidden', $this->plainForm, $options);
        $hidden->render();

        $this->assertMatchesRegularExpression('/required/', $hidden->getOption('label_attr.class'));
        $this->assertArrayNotHasKey('required', $hidden->getOption('attr'));
    }

    /** @test */
    public function it_appends_to_the_class_attribute_of_the_field()
    {
        $options = [
            'attr' => [
                'class_append' => 'appended',
            ],
        ];

        $text = new InputType('field_name', 'text', $this->plainForm, $options);
        $renderResult = $text->render();

        $this->assertMatchesRegularExpression('/appended/', $text->getOption('attr.class'));

        $defaultClasses = $this->config['defaults']['field_class'];
        $this->assertEquals('form-control appended', $text->getOption('attr.class'));

        $this->assertStringContainsString($defaultClasses, $text->getOption('attr.class'));
        $this->assertStringNotContainsString('class_append', $renderResult);
    }

    /** @test */
    public function it_appends_to_the_class_attribute_of_the_label()
    {
        $options = [
            'label_attr' => [
                'class_append' => 'appended',
            ],
        ];

        $text = new InputType('field_name', 'text', $this->plainForm, $options);
        $renderResult = $text->render();

        $this->assertMatchesRegularExpression('/appended/', $text->getOption('label_attr.class'));

        $defaultClasses = $this->config['defaults']['label_class'];
        $this->assertEquals('control-label appended', $text->getOption('label_attr.class'));

        $this->assertStringContainsString($defaultClasses, $text->getOption('label_attr.class'));
        $this->assertStringNotContainsString('class_append', $renderResult);
    }

    /** @test */
    public function it_appends_to_the_class_attribute_of_the_wrapper()
    {
        $options = [
            'wrapper' => [
                'class_append' => 'appended',
            ],
        ];

        $text = new InputType('field_name', 'text', $this->plainForm, $options);
        $renderResult = $text->render();

        $this->assertMatchesRegularExpression('/appended/', $text->getOption('wrapper.class'));

        $defaultClasses = $this->config['defaults']['wrapper_class'];
        $this->assertEquals('form-group appended', $text->getOption('wrapper.class'));

        $this->assertStringContainsString($defaultClasses, $text->getOption('wrapper.class'));
        $this->assertStringNotContainsString('class_append', $renderResult);
    }

    /** @test */
    public function it_appends_rules_properly()
    {

        $closureRule = function ($attribute, $value, $fail) {
            $this->assertEquals('my_email', $attribute);
            $this->assertEquals('foo@bar.com', $value);
        };

        // Default rules of DummyField : ['email', 'max:100']

        $fieldOptions = [
            'rules_append' => [
                'email', // duplicated rule with DummyField
                'required', // new rule
                'confirmed', // new rule
                $closureRule, // new rule
            ],
        ];

        $expected = [
            'email', 'max:100', // default rules
            'required', 'confirmed', $closureRule, // appended rules
        ];

        $this->plainForm->setRequest(new Request(['my_email' => 'foo@bar.com']));
        $this->plainForm->addCustomField('dummy',DummyField::class);
        $this->plainForm->add('my_email', 'dummy', $fieldOptions);
        $this->plainForm->isValid();
        $field = $this->plainForm->getField('my_email');

        $this->assertEquals($expected, $field->getOption('rules'));
    }

    /** @test */
    public function it_translates_the_label_if_translation_exists()
    {
        // We use build in validation translations prefix for easier testing
        // just to make sure translation file is properly used
        $this->plainForm->setLanguageName('validation')->add('accepted', 'text');

        $this->assertEquals(
            'The :attribute field must be accepted.',
            $this->plainForm->accepted->getOption('label')
        );
    }

    /** @test */
    public function it_translates_the_label_using_translation_templates()
    {
        // We use build in validation translations prefix for easier testing
        // just to make sure translation file is properly used
        $this->plainForm->setTranslationTemplate('validation.{name}')->add('accepted', 'text');

        $this->assertEquals(
            'The :attribute field must be accepted.',
            $this->plainForm->accepted->getOption('label')
        );
    }

    /** @test */
    public function provided_label_from_option_overrides_translated_one()
    {
        // We use build in validation translations prefix for easier testing
        // just to make sure translation file is properly used
        $this->plainForm->setLanguageName('validation')->add('accepted', 'text', [
            'label' => 'Custom accepted label'
        ]);

        $this->assertEquals(
            'Custom accepted label',
            $this->plainForm->accepted->getOption('label')
        );
    }

    /** @test */
    public function it_fallbacks_to_simple_format_if_no_translation_and_custom_label_provided()
    {
        // We use build in validation translations prefix for easier testing
        // just to make sure translation file is properly used
        $options = [
            'language_name' => 'validation'
        ];

        $customPlainForm = $this->formBuilder->plain();

        $this->plainForm->setFormOptions($options)->add('nonexisting', 'text');
        $customPlainForm->add('the_name_without_translation', 'text');

        // Case where translation is nested (an array) should be invalid and fallback
        // in validation translation file the custom key does this
        $customPlainForm->add('custom', 'text');

        $this->assertEquals(
            'Validation.nonexisting',
            $this->plainForm->nonexisting->getOption('label')
        );

        $this->assertEquals(
            'The name without translation',
            $customPlainForm->the_name_without_translation->getOption('label')
        );

        $this->assertEquals(
            'Custom',
            $customPlainForm->custom->getOption('label')
        );
    }

    /** @test */
    public function it_initialize_all_defined_field_filters()
    {
        $customPlainForm = $this->formBuilder->plain();

        $filters = ['Trim', 'Uppercase'];
        $customPlainForm->add('test_field', 'text', [
            'filters' => $filters
        ]);

        $testField = $customPlainForm->getField('test_field');

        foreach ($filters as $filterName) {
            $this->assertArrayHasKey($filterName, $testField->getFilters());
        }
    }

    /** @test */
    public function it_enables_overriding_existing_filters()
    {
        $customPlainForm = $this->formBuilder->plain();
        $customPlainForm->add('test_field', 'text', [
            'filters_override' => true
        ]);

        $testField = $customPlainForm->getField('test_field');
        $this->assertTrue(
            $testField->getFiltersOverride()
        );
    }

    /** @test  */
    public function it_throws_an_exception_if_filters_override_is_false_but_passed_already_binded_filter()
    {
        $this->expectException(\Kris\LaravelFormBuilder\Filters\Exception\FilterAlreadyBindedException::class);

        $customPlainForm = $this->formBuilder->plain();
        $customPlainForm->add('test_field', 'text', [
            'filters' => ['Trim']
        ]);

        $testField = $customPlainForm->getField('test_field');
        $testField->addFilter('Trim');
    }

    /** @test */
    public function it_overrides_already_existing_filter()
    {
        $customPlainForm = $this->formBuilder->plain();
        $filter = 'Trim';
        $customPlainForm->add('test_field', 'text', [
            'filters' => [$filter],
            'filters_override' => true
        ]);

        // TODO: Find out for mocking object or stubing new with same alias/name but different implementation.
        $testField = $customPlainForm->getField('test_field');
        $testField->addFilter($filter);
        $this->assertArrayHasKey($filter, $testField->getFilters());
    }

    /** @test */
    public function it_removes_binded_filter()
    {
        $customPlainForm = $this->formBuilder->plain();
        $customPlainForm->add('test_field', 'text', [
            'filters' => ['Trim', 'Uppercase']
        ]);

        $testField = $customPlainForm->getField('test_field');
        $testField->removeFilter('Trim');
        $this->assertTrue(count($testField->getFilters()) == 1);
        $this->assertArrayHasKey('Uppercase', $testField->getFilters());
    }

    /** @test */
    public function it_removes_multiple_filters()
    {
        $customPlainForm = $this->formBuilder->plain();
        $filters = ['Trim', 'Uppercase'];
        $customPlainForm->add('test_field', 'text', [
            'filters' => $filters
        ]);

        $testField = $customPlainForm->getField('test_field');
        $testField->removeFilters($filters);
        $this->assertEmpty($testField->getFilters());
    }

    /** @test */
    public function it_clears_all_filters()
    {
        $customPlainForm = $this->formBuilder->plain();
        $customPlainForm->add('test_field', 'text', [
            'filters' => ['Trim', 'Uppercase']
        ]);

        $testField = $customPlainForm->getField('test_field');
        $testField->clearFilters();
        $this->assertEmpty($testField->getFilters());
    }

    /** @test */
    public function it_is_plain()
    {
        $methodName = 'isPlain';

        $plainForm = $this->formBuilder->plain();
        $modifiedForm = $this->formBuilder->create(TestForm::class);

        $reflection = new \ReflectionClass($plainForm);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($plainForm, []));
        $this->assertFalse($method->invokeArgs($modifiedForm, []));
    }

    /** @test */
    public function it_custom_plain_form_is_plain()
    {
        $methodName = 'isPlain';

        $this->formBuilder->setFormClass(TestForm::class);

        $customPlainForm = $this->formBuilder->create(TestForm::class);

        $reflection = new \ReflectionClass($customPlainForm);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);


        $this->assertTrue($method->invokeArgs($customPlainForm, []));
    }

    /** @test */
    public function label_template()
    {
        $fieldsOptions = [
            [
                'type' => 'checkbox',
                'name' => 'checkbox_field',
                'options' => [
                    'label' => 'Checkbox Field #1',
                    'label_show' => true,
                    'label_template' => 'laravel-form-builder-test::test-label',
                ]
            ],
            [
                'type' => 'choice',
                'name' => 'choide_field',
                'options' => [
                    'label' => 'Choice Field #1',
                    'choices' => [true => 'Yes', false => 'No'],
                    'label_show' => true,
                    'label_template' => 'laravel-form-builder-test::test-label',
                ]
            ],
            [
                'type' => 'collection',
                'name' => 'collection_field',
                'options' => [
                    'label' => 'Collection Field #1',
                    'label_show' => true,
                    'label_template' => 'laravel-form-builder-test::test-label',
                ]
            ],
            [
                'type' => 'radio',
                'name' => 'radio_field',
                'options' => [
                    'label' => 'Radio Field #1',
                    'label_show' => true,
                    'label_template' => 'laravel-form-builder-test::test-label',
                ]
            ],
            [
                'type' => 'select',
                'name' => 'select_field',
                'options' => [
                    'label' => 'Select Field #1',
                    'label_show' => true,
                    'label_template' => 'laravel-form-builder-test::test-label',
                ]
            ],
            [
                'type' => 'static',
                'name' => 'static_field',
                'options' => [
                    'label' => 'Static Field #1',
                    'label_show' => true,
                    'label_template' => 'laravel-form-builder-test::test-label',
                ]
            ],
            [
                'type' => 'text',
                'name' => 'text_field',
                'options' => [
                    'label' => 'Text Field #1',
                    'label_show' => true,
                    'label_template' => 'laravel-form-builder-test::test-label',
                ]
            ],
            [
                'type' => 'textarea',
                'name' => 'textarea_field',
                'options' => [
                    'label' => 'Textarea Field #1',
                    'label_show' => true,
                    'label_template' => 'laravel-form-builder-test::test-label',
                ]
            ],

        ];

        foreach ($fieldsOptions as $config) {
            $field = new InputType($config['name'] ?? 'name', $config['type'], $this->plainForm, $config['options']);
            $view = $field->render();
            $this->assertMatchesRegularExpression('/test label view/', $view);
        }
    }

}


class DummyField extends InputType
{
    protected function getDefaults()
    {
        return [
            'rules' => ['email', 'max:100'],
        ];
    }
}
