<?php

use Kris\LaravelFormBuilder\Fields\ButtonType;
use Kris\LaravelFormBuilder\Fields\InputType;
use Kris\LaravelFormBuilder\Form;

class InputTypeTest extends FormBuilderTestCase
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
    public function it_prevents_rendering_label_for_hidden_field()
    {
        $options = [
            'default_value' => 12
        ];

        $expectedOptions = $this->getDefaults(
            [],
            'hidden_id',
            'Hidden Id',
            12
        );

        $expectedViewData = [
            'name' => 'hidden_id',
            'type' => 'hidden',
            'options' => $expectedOptions,
            'showLabel' => false,
            'showField' => true,
            'showError' => true
        ];

        $this->fieldExpetations('text', $expectedViewData);

        $hidden = new InputType('hidden_id', 'hidden', $this->form, $options);

        $hidden->render();
    }
}
