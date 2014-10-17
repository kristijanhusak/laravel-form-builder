<?php

use Kris\LaravelFormBuilder\Fields\ChoiceType;
use Kris\LaravelFormBuilder\Fields\SelectType;
use Kris\LaravelFormBuilder\Form;

class ChoiceTypeTest extends FormBuilderTestCase
{
    /**
     * @var Form
     */
    protected $form;

    public function setUp()
    {
        parent::setUp();
        $this->form = (new Form())->setFormHelper($this->formHelper);
    }

    /** @test */
    public function it_creates_choice_as_select()
    {
        $options = [
            'attr' => ['class' => 'choice-class'],
            'selected' => 'yes'
        ];

        $this->fieldExpetations(Mockery::any(), 'select');

        $this->fieldExpetations(Mockery::any(), 'choice');

        $choice = new ChoiceType('some_choice', 'choice', $this->form, $options);

        $choice->render();

        $this->assertEquals(1, count($choice->getChildren()));

        $this->assertEquals('yes', $choice->getOptions()['selected']);
    }

    /** @test */
    public function it_creates_choice_as_checkbox_list()
    {
        $options = [
            'attr' => ['class' => 'choice-class-something'],
            'choices' => [1 => 'monday', 2 => 'tuesday'],
            'selected' => 'tuesday',
            'multiple' => true,
            'expanded' => true
        ];

        $this->fieldExpetations(Mockery::any(), 'checkbox');

        $this->fieldExpetations(Mockery::any(), 'checkbox');

        $this->fieldExpetations(Mockery::any(), 'choice');

        $choice = new ChoiceType('some_choice', 'choice', $this->form, $options);

        $choice->render();

        $this->assertEquals(2, count($choice->getChildren()));

        $this->assertEquals('tuesday', $choice->getOptions()['selected']);

        $this->assertContainsOnlyInstancesOf('Kris\LaravelFormBuilder\Fields\CheckableType', $choice->getChildren());
    }

    /** @test */
    public function it_creates_choice_as_radio_buttons()
    {
        $options = [
            'attr' => ['class' => 'choice-class-something'],
            'choices' => [1 => 'yes', 2 => 'no'],
            'selected' => 'no',
            'expanded' => true
        ];

        $this->fieldExpetations(Mockery::any(), 'radio');

        $this->fieldExpetations(Mockery::any(), 'radio');

        $this->fieldExpetations(Mockery::any(), 'choice');

        $choice = new ChoiceType('some_choice', 'choice', $this->form, $options);

        $choice->render();

        $this->assertEquals(2, count($choice->getChildren()));

        $this->assertEquals('no', $choice->getOptions()['selected']);

        $this->assertContainsOnlyInstancesOf('Kris\LaravelFormBuilder\Fields\CheckableType', $choice->getChildren());
    }

    private function fieldExpetations($expectedViewData, $childType = 'select')
    {
        $viewRenderer = Mockery::mock('Illuminate\Contracts\View\View');
        $viewRenderer->shouldReceive('render');

        $this->config->shouldReceive('get')
            ->with('laravel-form-builder::'.$childType, 'laravel-form-builder::'.$childType)
            ->andReturn('laravel-form-builder::'.$childType);

        $this->config->shouldReceive('get')
                     ->with('laravel-form-builder::defaults.label_class')
                     ->andReturn('control-label');

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
                       'laravel-form-builder::choice',
                       $expectedViewData
                   )
                   ->andReturn($viewRenderer);
    }

}