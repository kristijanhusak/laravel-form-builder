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

        $helper = new FormHelper($viewStub, $this->request, $this->config);

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
}