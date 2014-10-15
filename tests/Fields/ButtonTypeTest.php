<?php

use Kris\LaravelFormBuilder\Fields\ButtonType;
use Kris\LaravelFormBuilder\Form;

class ButtonTypeTest extends FormBuilderTestCase
{

    /**
     * @var Form
     */
    protected $form;

    protected $buttonType;

    public function setUp()
    {
        parent::setUp();
        $this->form = (new Form())->setFormHelper($this->formHelper);
    }

    /** @test */
    public function it_creates_button()
    {
        $options = [
            'attr' => ['class' => 'btn-class', 'disabled' => 'disabled']
        ];

        $expectedOptions = $this->getDefaults(
            ['class' => 'btn-class', 'type' => 'button', 'disabled' => 'disabled'],
            'some_button'
        );

        $expectedViewData = [
            'name' => 'some_button',
            'type' => 'button',
            'options' => $expectedOptions,
            'showLabel' => true,
            'showField' => true,
            'showError' => true
        ];

        $this->fieldExpetations('button', $expectedViewData);

        $button = new ButtonType('some_button', 'button', $this->form, $options);

        $button->render();
    }

    /** @test */
    public function it_can_handle_object_with_getters_and_setters()
    {
        $expectedOptions = $this->getDefaults(['type' => 'submit'], 'save');

        $this->fieldExpetations('button', Mockery::any());

        $button = new ButtonType('save', 'submit', $this->form);

        $this->assertEquals('save', $button->getName());
        $this->assertEquals('submit', $button->getType());
        $this->assertEquals($expectedOptions, $button->getOptions());
        $this->assertFalse($button->isRendered());

        $button->setName('cancel');
        $button->setType('reset');
        $button->setOptions(['attr' => ['id' => 'button-id'], 'label' => 'Cancel it']);

        $expectedOptions = $this->getDefaults(['type' => 'submit', 'id' => 'button-id'], 'Cancel it');

        $this->assertEquals('cancel', $button->getName());
        $this->assertEquals('reset', $button->getType());
        $this->assertEquals($expectedOptions, $button->getOptions());

        $button->render();

        $this->assertTrue($button->isRendered());
    }

    /** @test */
    public function it_can_change_template_with_options()
    {
        $expectedOptions = $this->getDefaults(
            ['type' => 'submit'],
            'some_submit'
        );

        $expectedViewData = [
            'name' => 'some_submit',
            'type' => 'submit',
            'options' => $expectedOptions,
            'showLabel' => true,
            'showField' => true,
            'showError' => true
        ];

        $this->fieldExpetations('button', $expectedViewData, 'custom-template');

        $button = new ButtonType('some_submit', 'submit', $this->form, ['template' => 'custom-template']);

        $button->render();
    }

    private function getDefaults($attr = [], $label = '')
    {
        return [
            'wrapper' => ['class' => 'form-group'],
            'attr' => array_merge(['class' => 'form-control'], $attr),
            'default_value' => null,
            'label' => $label,
            'label_attr' => [],
            'errors' => ['class' => 'text-danger'],
            'wrapperAttrs' => 'class="form-group" ',
            'errorAttrs' => 'class="text-danger" '
        ];
    }

    private function fieldExpetations($name, $expectedViewData, $templatePrefix = 'laravel-form-builder::')
    {
        $viewRenderer = Mockery::mock('Illuminate\Contracts\View\View');
        $viewRenderer->shouldReceive('render');

        $this->config->shouldReceive('get')
            ->with('laravel-form-builder::'.$name, 'laravel-form-builder::' . $name)
            ->andReturn('laravel-form-builder::'.$name);

        $this->config->shouldReceive('get')
            ->with('laravel-form-builder::defaults.wrapper_class')
            ->andReturn('form-group');

        $this->config->shouldReceive('get')
            ->with('laravel-form-builder::defaults.field_class')
            ->andReturn('form-control');

        $this->config->shouldReceive('get')
            ->with('laravel-form-builder::defaults.error_class')
            ->andReturn('text-danger');

        $this->view->shouldReceive('make')
            ->with(
                $templatePrefix == 'laravel-form-builder::' ? 'laravel-form-builder::button' : $templatePrefix,
                $expectedViewData
            )
            ->andReturn($viewRenderer);
    }

}
