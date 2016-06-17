<?php

use Kris\LaravelFormBuilder\Fields\ButtonType;
use Kris\LaravelFormBuilder\Fields\InputType;
use Kris\LaravelFormBuilder\Form;

class InputTypeTest extends FormBuilderTestCase
{
    /** @test */
    public function it_prevents_rendering_label_for_hidden_field()
    {
        $options = [
            'value' => 12,
            'required' => true,
            'help_block' => [
                'text' => 'this is help'
            ]
        ];

        $expectedOptions = $this->getDefaults(
            ['required' => 'required'],
            'Hidden id',
            13,
            'this is help'
        );

        $expectedOptions['help_block']['helpBlockAttrs'] = 'class="help-block" ';
        $expectedOptions['required'] = true;
        $expectedOptions['label_attr']['class'] .= ' required';

        $hidden = new InputType('hidden_id', 'hidden', $this->plainForm, $options);

        $hidden->render(['value' => 13]);

        $this->assertEquals($expectedOptions, $hidden->getOptions());
    }


    /** @test */
    public function it_handles_default_values()
    {
        $options = [
            'default_value' => 100
        ];
        $this->plainForm->setModel(null);
        $input = new InputType('test', 'text', $this->plainForm, $options);

        $this->assertEquals(100, $input->getOption('value'));
    }

    /** @test */
    public function model_value_overrides_default_value()
    {
        $options = [
            'default_value' => 100
        ];
        $this->plainForm->setModel(['test' => 5]);
        $input = new InputType('test', 'text', $this->plainForm, $options);

        $this->assertEquals(5, $input->getValue());
    }

    /** @test */
    public function explicit_value_overrides_default_values()
    {
        $options = [
            'default_value' => 100,
            'value' => 500
        ];

        $input = new InputType('test', 'text', $this->plainForm, $options);
        $data = $input->render();

        $this->assertEquals(500, $input->getValue());
        $this->assertEquals(100, $input->getDefaultValue());
    }

}
