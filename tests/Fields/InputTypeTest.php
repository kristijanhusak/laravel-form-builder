<?php

use Kris\LaravelFormBuilder\Fields\ButtonType;
use Kris\LaravelFormBuilder\Fields\InputType;
use Kris\LaravelFormBuilder\Form;
use PHPUnit\Framework\Attributes\Test;

class InputTypeTest extends FormBuilderTestCase
{
    #[Test]
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


    #[Test]
    public function it_handles_default_values()
    {
        $options = [
            'default_value' => 100,
            'model' => null,
        ];
        $input = new InputType('test', 'text', $this->plainForm, $options);

        $this->assertEquals(100, $input->getOption('value'));
    }

    #[Test]
    public function model_value_overrides_default_value()
    {
        $options = [
            'default_value' => 100
        ];
        $this->plainForm->setModel(['test' => 5]);
        $input = new InputType('test', 'text', $this->plainForm, $options);

        $this->assertEquals(5, $input->getValue());
    }

    #[Test]
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
