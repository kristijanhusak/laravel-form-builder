<?php

use Kris\LaravelFormBuilder\Fields\CheckableType;

class CheckableTypeTest extends FormBuilderTestCase
{
    /** @test */
    public function it_creates_checkbox_field(): void
    {
        $defaultOptions = [
            'checked' => null,
        ];

        $options = [
            'value' => 2,
            'required' => true,
            'help_block' => [
                'text' => 'this is help'
            ],
        ];

        $expectedOptions = $this->getDefaults(
            [
                'class' => null,
                'required' => 'required',
                'id' => 'test',
            ],
            'Test',
            2,
            'this is help'
        );

        $expectedOptions['help_block']['helpBlockAttrs'] = 'class="help-block" ';
        $expectedOptions['required'] = true;
        $expectedOptions['label_attr']['class'] .= ' required';

        $expectedOptions += $defaultOptions;

        $checkable = new CheckableType('test', 'checkbox', $this->plainForm, $options);

        $checkable->render();

        $this->assertEquals($expectedOptions, $checkable->getOptions());
    }

    /** @test */
    public function it_creates_radio_field(): void
    {
        $defaultOptions = [
            'checked' => null,
        ];

        $options = [
            'value' => 2,
            'required' => true,
            'help_block' => [
                'text' => 'this is help'
            ],
        ];

        $expectedOptions = $this->getDefaults(
            [
                'class' => null,
                'required' => 'required',
                'id' => 'test',
            ],
            'Test',
            2,
            'this is help'
        );

        $expectedOptions['help_block']['helpBlockAttrs'] = 'class="help-block" ';
        $expectedOptions['required'] = true;
        $expectedOptions['label_attr']['class'] .= ' required';

        $expectedOptions += $defaultOptions;

        $checkable = new CheckableType('test', 'radio', $this->plainForm, $options);

        $checkable->render();

        $this->assertEquals($expectedOptions, $checkable->getOptions());
    }

    /** @test */
    public function it_handles_values(): void
    {
        $expectedValue = 2;

        $options = [
            'value' => $expectedValue,
        ];
        $this->plainForm->setModel(null);
        $checkable = new CheckableType('test', 'checkbox', $this->plainForm, $options);

        $this->assertSame($expectedValue, $checkable->getOption('value'));
    }

    /** @test */
    public function it_handles_checked(): void
    {
        $options = [
            'checked' => true,
        ];
        $this->plainForm->setModel(null);
        $checkable = new CheckableType('test', 'checkbox', $this->plainForm, $options);

        $this->assertTrue($checkable->getValue());
        $this->assertTrue($checkable->getOption('checked'));
    }
}