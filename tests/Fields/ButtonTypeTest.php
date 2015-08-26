<?php

use Kris\LaravelFormBuilder\Fields\ButtonType;
use Kris\LaravelFormBuilder\Form;

class ButtonTypeTest extends FormBuilderTestCase
{

    /** @test */
    public function it_creates_button()
    {
        $options = [
            'wrapper' => ['class' => 'form-group'],
            'attr' => ['class' => 'btn-class', 'disabled' => 'disabled']
        ];

        $expectedOptions = $this->getDefaults(
            ['class' => 'btn-class', 'type' => 'button', 'disabled' => 'disabled'],
            'Some button'
        );

        $button = new ButtonType('some_button', 'button', $this->plainForm, $options);

        $this->assertEquals($expectedOptions, $button->getOptions());

        $button->render();
    }

    /** @test */
    public function it_can_handle_object_with_getters_and_setters()
    {
        $expectedOptions = $this->getDefaults(['type' => 'submit'], 'Save');
        $expectedOptions['wrapperAttrs'] = null;
        $expectedOptions['wrapper'] = false;

        $button = new ButtonType('save', 'submit', $this->plainForm);

        $this->assertEquals('save', $button->getName());
        $this->assertEquals('submit', $button->getType());
        $this->assertEquals($expectedOptions, $button->getOptions());
        $this->assertFalse($button->isRendered());

        $button->setName('cancel');
        $button->setType('reset');
        $button->setOptions(['attr' => ['id' => 'button-id'], 'label' => 'Cancel it']);

        $expectedOptions = $this->getDefaults(['type' => 'submit', 'id' => 'button-id'], 'Cancel it');
        $expectedOptions['wrapperAttrs'] = null;
        $expectedOptions['wrapper'] = false;

        $this->assertEquals('cancel', $button->getName());
        $this->assertEquals('reset', $button->getType());

        $button->render();

        $this->assertEquals($expectedOptions, $button->getOptions());

        $this->assertTrue($button->isRendered());
    }

    /** @test */
    public function it_can_change_template_with_options()
    {
        $expectedOptions = $this->getDefaults(
            ['type' => 'submit'],
            'Some submit'
        );

        $expectedOptions['wrapper'] = false;
        $expectedOptions['wrapperAttrs'] = null;
        $expectedOptions['template'] = 'laravel-form-builder::text';

        $expectedViewData = [
            'name' => 'some_submit',
            'nameKey' => 'some_submit',
            'type' => 'submit',
            'options' => $expectedOptions,
            'showLabel' => true,
            'showField' => true,
            'showError' => true
        ];

        $button = new ButtonType('some_submit', 'submit', $this->plainForm, [
            'template' => 'laravel-form-builder::text'
        ]);

        $renderedView = $button->render();

        $this->assertEquals($expectedOptions, $button->getOptions());
        $this->assertContains('<input', $renderedView);
    }
}
