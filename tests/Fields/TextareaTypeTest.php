<?php

use Kris\LaravelFormBuilder\Fields\TextareaType;

class TextareaTypeTest extends FormBuilderTestCase
{
    /** @test */
    public function it_creates_txetarea_field(): void
    {
        $options = [
            'value' => 'default text',
            'required' => true,
            'help_block' => [
                'text' => 'this is help'
            ]
        ];

        $expectedOptions = $this->getDefaults(
            [
                'required' => 'required'
            ],
            'Test',
            'text',
            'this is help'
        );

        $expectedOptions['help_block']['helpBlockAttrs'] = 'class="help-block" ';
        $expectedOptions['required'] = true;
        $expectedOptions['label_attr']['class'] .= ' required';

        $textarea = new TextareaType('test', 'textarea', $this->plainForm, $options);

        $textarea->render(['value' => 'text']);

        $this->assertEquals($expectedOptions, $textarea->getOptions());
    }

    /** @test */
    public function it_handles_default_values(): void
    {
        $options = [
            'default_value' => 'default text',
        ];
        $this->plainForm->setModel(null);
        $textarea = new TextareaType('test', 'textarea', $this->plainForm, $options);

        $this->assertEquals('default text', $textarea->getOption('value'));
    }

    /** @test */
    public function model_value_overrides_default_value(): void
    {
        $options = [
            'default_value' => 'default text',
        ];
        $this->plainForm->setModel(['test' =>  'override text']);
        $textarea = new TextareaType('test', 'textarea', $this->plainForm, $options);

        $this->assertEquals( 'override text', $textarea->getValue());
    }

    /** @test */
    public function explicit_value_overrides_default_values(): void
    {
        $options = [
            'default_value' => 'default text',
            'value' => 'text',
        ];

        $textarea = new TextareaType('test', 'textarea', $this->plainForm, $options);
        $data = $textarea->render();

        $this->assertEquals('text', $textarea->getValue());
        $this->assertEquals('default text', $textarea->getDefaultValue());
    }
}