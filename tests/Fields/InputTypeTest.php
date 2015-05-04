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
            'default_value' => 12,
            'required' => true,
            'help_block' => [
                'text' => 'this is help'
            ]
        ];

        $expectedOptions = $this->getDefaults(
            ['required' => 'required'],
            'hidden_id',
            'Hidden Id',
            12,
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

        $hidden->render();
    }
}
