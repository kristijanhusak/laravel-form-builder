<?php

use Kris\LaravelFormBuilder\Fields\CheckableType;
use Kris\LaravelFormBuilder\Fields\ChoiceType;

class ChoiceTypeTest extends FormBuilderTestCase
{
    /** @test */
    public function it_creates_choice_as_select()
    {
        $options = [
            'attr' => ['class' => 'choice-class'],
            'choices' => ['yes' => 'Yes', 'no' => 'No'],
            'selected' => 'yes'
        ];

        $choice = new ChoiceType('some_choice', 'choice', $this->plainForm, $options);

        $choice->render();

        $this->assertEquals(1, count($choice->getChildren()));

        $this->assertEquals('yes', $choice->getOption('selected'));
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

        $choice = new ChoiceType('some_choice', 'choice', $this->plainForm, $options);

        $choice->render();

        $this->assertEquals(2, count($choice->getChildren()));

        $this->assertEquals('tuesday', $choice->getOption('selected'));

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

        $choice = new ChoiceType('some_choice', 'choice', $this->plainForm, $options);

        $choice->render();

        $this->assertEquals(2, count($choice->getChildren()));

        $this->assertInstanceOf(
            'Kris\LaravelFormBuilder\Fields\CheckableType',
            $choice->getChild(1)
        );

        $this->assertEquals('no', $choice->getOption('selected'));

        $this->assertContainsOnlyInstancesOf('Kris\LaravelFormBuilder\Fields\CheckableType', $choice->getChildren());
    }

    /** @test */
    public function it_sets_proper_name_for_multiple()
    {
        $this->plainForm->add('users', 'select', [
            'choices' => [1 => 'user1', 2 => 'user2'],
            'attr' => [
                'multiple' => 'multple'
            ]
        ]);

        $this->plainForm->renderForm();

        $this->assertEquals('users[]', $this->plainForm->users->getName());
    }

    /** @test */
    public function it_can_override_choices()
    {
        $options = [
            'choices' => ['yes' => 'Yes', 'no' => 'No'],
            'selected' => 'test',
            'data_override' => function ($choices, $field) {
                $choices['test'] = 'test';

                return $choices;
            }
        ];

        $choice = new ChoiceType('some_choice', 'choice', $this->plainForm, $options);

        $choice->render();

        $this->assertEquals(3, count($choice->getOption('choices')));

        $this->assertEquals('test', $choice->getOption('selected'));
    }

    /** @test */
    public function it_keeps_default_options_from_children()
    {
        $options = [
            'rules' => 'required',
            'choices' => ['yes' => 'Yes', 'no' => 'No'],
            'expanded' => true,
            'multiple' => true,
        ];

        $choice = new ChoiceType('some_choice', 'choice', $this->plainForm, $options);

        $choice->render();

        $this->assertEquals(2, count($choice->getChildren()));

        $this->assertContainsOnlyInstancesOf(CheckableType::class, $choice->getChildren());

        /** @var CheckableType $firstChoice */
        $firstChoice = $choice->getChildren()[0];

        $this->assertEquals('some_choice_yes', $firstChoice->getOption('attr.id'));
        $this->assertEquals($firstChoice->getOption('label_attr.for'), $firstChoice->getOption('attr.id'));
    }
}
