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
            'hidden_id',
            'Hidden Id',
            13,
            'this is help'
        );

        $expectedOptions['help_block']['helpBlockAttrs'] = 'class="help-block" ';
        $expectedOptions['required'] = true;
        $expectedOptions['label_attr']['class'] .= ' required';

        $expectedViewData = [
            'name' => 'hidden_id',
            'nameKey' => 'hidden_id',
            'type' => 'hidden',
            'options' => $expectedOptions,
            'showLabel' => false,
            'showField' => true,
            'showError' => true
        ];

        $this->fieldExpetations('text', $expectedViewData);

        $hidden = new InputType('hidden_id', 'hidden', $this->plainForm, $options);

        $hidden->render(['value' => 13]);
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

        $this->assertEquals(5, $input->getOption('value'));
    }

    /** @test */
    public function explicit_value_overrides_default_values()
    {
        $options = [
            'default_value' => 100,
            'value' => 500
        ];

        $input = new InputType('test', 'text', $this->plainForm, $options);

        $this->assertEquals(500, $input->getOption('value'));
    }

}
