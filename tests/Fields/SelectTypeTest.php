<?php

use Kris\LaravelFormBuilder\Fields\SelectType;

class SelectTypeTest extends FormBuilderTestCase
{
    /** @test */
    public function it_creates_select_field(): void
    {
        $choices = [
            '0' => 'Not known',
            '1' => 'Male',
            '2' => 'Female',
            '9' => 'Not applicable',
        ];

        $options = [
            'choices' => $choices,
            'selected' => '2',
            'empty_value' => 'Select your sex',
            'required' => true,
            'help_block' => [
                'text' => 'this is help'
            ]
        ];

        $expectedOptions = $this->getDefaults(
            [
                'required' => 'required'
            ],
            'Sex',
            null,
            'this is help'
        );

        $expectedOptions['help_block']['helpBlockAttrs'] = 'class="help-block" ';
        $expectedOptions['required'] = true;
        $expectedOptions['label_attr']['class'] .= ' required';
        $expectedOptions['choices'] = $choices;
        $expectedOptions['empty_value'] = 'Select your sex';
        $expectedOptions['selected'] = '1';
        $expectedOptions['option_attributes'] = [];

        $select = new SelectType('sex', 'select', $this->plainForm, $options);

        $select->render(['selected' => '1']);

        $this->assertEquals($expectedOptions, $select->getOptions());
    }
}
