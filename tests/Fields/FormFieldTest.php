<?php

use Kris\LaravelFormBuilder\Form;
use Kris\LaravelFormBuilder\FormHelper;
use Kris\LaravelFormBuilder\Fields\InputType;

class FormFieldTest extends FormBuilderTestCase
{
    /** @test */
    public function it_uses_the_template_prefix()
    {
        $viewStub = $this->getMockBuilder('Illuminate\View\Factory')->setMethods(['make', 'with', 'render'])->disableOriginalConstructor()->getMock();
        $viewStub->method('make')->willReturn($viewStub);
        $viewStub->method('with')->willReturn($viewStub);

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
        $this->assertNotContains('label', $view);
    }


    /** @test */
    public function it_sets_required_as_class_on_the_label_and_attribute_on_the_field_when_setting_required_explicitly()
    {
        $options = [
            'required' => true
        ];

        $hidden = new InputType('hidden_id', 'hidden', $this->plainForm, $options);
        $hidden->render();

        $this->assertRegExp('/required/', $hidden->getOption('label_attr.class'));
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

        $this->assertRegExp('/required/', $hidden->getOption('label_attr.class'));
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

        $this->assertRegExp('/required/', $hidden->getOption('label_attr.class'));
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

        $this->assertRegExp('/appended/', $text->getOption('attr.class'));

        $defaultClasses = $this->config['defaults']['field_class'];
        $this->assertEquals('form-control appended', $text->getOption('attr.class'));
        
        $this->assertContains($defaultClasses, $text->getOption('attr.class'));
        $this->assertNotContains('class_append', $renderResult);
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

        $this->assertRegExp('/appended/', $text->getOption('label_attr.class'));

        $defaultClasses = $this->config['defaults']['label_class'];
        $this->assertEquals('control-label appended', $text->getOption('label_attr.class'));
        
        $this->assertContains($defaultClasses, $text->getOption('label_attr.class'));
        $this->assertNotContains('class_append', $renderResult);
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

        $this->assertRegExp('/appended/', $text->getOption('wrapper.class'));

        $defaultClasses = $this->config['defaults']['wrapper_class'];
        $this->assertEquals('form-group appended', $text->getOption('wrapper.class'));
        
        $this->assertContains($defaultClasses, $text->getOption('wrapper.class'));
        $this->assertNotContains('class_append', $renderResult);
    }

    /** @test */
    public function it_translates_the_label_if_translation_exists()
    {
        // We use build in validation translations prefix for easier testing
        // just to make sure translation file is properly used
        $this->plainForm->setLanguageName('validation')->add('accepted', 'text');

        $this->assertEquals(
            'The :attribute must be accepted.',
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
}
