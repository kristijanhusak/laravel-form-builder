<?php

use Kris\LaravelFormBuilder\Fields\ChoiceType;
use Kris\LaravelFormBuilder\Fields\SelectType;
use Kris\LaravelFormBuilder\Form;

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
}
