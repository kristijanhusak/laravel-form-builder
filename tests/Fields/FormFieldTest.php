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
    public function it_sets_the_required_attribute_explicitly()
    {
        $options = [
            'required' => true
        ];

        $hidden = new InputType('hidden_id', 'hidden', $this->plainForm, $options);
        $hidden->render();

        $this->assertRegExp('/required/', $hidden->getOption('label_attr.class'));
    }

    /** @test */
    public function it_sets_the_required_attribute_implicitly()
    {
        $options = [
            'rules' => 'required|min:3'
        ];

        $hidden = new InputType('hidden_id', 'hidden', $this->plainForm, $options);
        $hidden->render();

        $this->assertRegExp('/required/', $hidden->getOption('label_attr.class'));
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

        $this->assertEquals(
            'Validation.nonexisting',
            $this->plainForm->nonexisting->getOption('label')
        );

        $this->assertEquals(
            'The name without translation',
            $customPlainForm->the_name_without_translation->getOption('label')
        );
    }
}
