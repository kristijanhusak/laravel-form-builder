<?php

use Kris\LaravelFormBuilder\Fields\ButtonGroupType;
use PHPUnit\Framework\Attributes\Test;

class ButtonGroupTypeTest extends FormBuilderTestCase
{
    #[Test]
    public function it_creates_checkbox_field(): void
    {
        $defaultOptions = [
            'splitted' => false,
            'size' => 'md',
            'buttons' => [],
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
                'required' => 'required',
            ],
            'Test',
            2,
            'this is help'
        );

        $expectedOptions['help_block']['helpBlockAttrs'] = 'class="help-block" ';
        $expectedOptions['required'] = true;
        $expectedOptions['label_attr']['class'] .= ' required';

        $expectedOptions += $defaultOptions;

        $buttongroup = new ButtonGroupType('test', 'buttongroup', $this->plainForm, $options);

        $buttongroup->render();

        $this->assertEquals($expectedOptions, $buttongroup->getOptions());
    }

    #[Test]
    public function it_handles_splitted(): void
    {
        $options = [
            'splitted' => true,
        ];
        $this->plainForm->setModel(null);
        $buttongroup = new ButtonGroupType('test', 'buttongroup', $this->plainForm, $options);

        $this->assertTrue($buttongroup->getOption('splitted'));
    }

    #[Test]
    public function it_handles_size(): void
    {
        $expectedValue = 'lg';

        $options = [
            'size' => $expectedValue,
        ];
        $this->plainForm->setModel(null);
        $buttongroup = new ButtonGroupType('test', 'buttongroup', $this->plainForm, $options);

        $this->assertSame($expectedValue, $buttongroup->getOption('size'));
    }

    #[Test]
    public function it_handles_buttons(): void
    {
        $buttons = [
            "submit" => [
                "label" => "Submit Button",
                "attr" => [
                    "type" => "submit",
                    "class" => "btn btn-primary hugo"
                ]
            ],
            "clear" => [
                "label" => "Clear Button",
                "attr" => [
                    "type" => "clear",
                    "class" => "btn btn-error was"
                ]
            ],
            "button" => [
                "label" => "Normal Button",
                "attr" => [
                    "type" => "button",
                    "class" => "btn btn-success here"
                ]
            ],
        ];

        $options = [
            'buttons' => $buttons,
        ];
        $this->plainForm->setModel(null);
        $buttongroup = new ButtonGroupType('test', 'buttongroup', $this->plainForm, $options);

        $this->assertSame($buttons, $buttongroup->getOption('buttons'));
    }
}
