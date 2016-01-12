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

        $helper = new FormHelper($viewStub, $this->config);

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
}
